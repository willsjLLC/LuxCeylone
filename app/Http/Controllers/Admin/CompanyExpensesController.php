<?php

namespace App\Http\Controllers\Admin;

use App\Constants\Status;
use App\Http\Controllers\Controller;
use App\Models\CompanyExpense;
use App\Models\CompanyExpenseItem;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;

class CompanyExpensesController extends Controller
{
	public function index(Request $request)
	{
		$admin = auth()->guard('admin')->user();
		if (!$admin || !$admin->can('expenses.view')) {
			return response()->view('admin.errors.403', [], 403);
		}

		$pageTitle = "All Company Expenses";

		$companyExpenses = CompanyExpense::query()
			->when($request->filled('search'), function ($query) use ($request) {
				$query->where('date', 'like', '%' . $request->search . '%')
					->orWhere('description', 'like', '%' . $request->search . '%')
					->orWhere('amount', 'like', '%' . $request->search . '%');
			})
			->when($request->filled('date'), function ($query) use ($request) {
				$dateRange = explode(' - ', $request->date);
				if (count($dateRange) == 2) {
					$startDate = Carbon::createFromFormat('F d, Y', trim($dateRange[0]))->startOfDay();
					$endDate = Carbon::createFromFormat('F d, Y', trim($dateRange[1]))->endOfDay();
					$query->whereBetween('date', [$startDate, $endDate]);
				} elseif (count($dateRange) == 1) {
					$searchDate = Carbon::createFromFormat('Y-m-d', trim($dateRange[0]))->format('Y-m-d');
					$query->whereDate('date', $searchDate);
				}
			})
			->orderBy('date', 'desc')
			->paginate(getPaginate());

		return view('admin.CompanyExpenses.index', compact('pageTitle', 'companyExpenses'));
	}

	public function create()
	{
		$admin = auth()->guard('admin')->user();

		if (!$admin || !$admin->can('expenses.create')) {
			return response()->view('admin.errors.403', [], 403);
		}

		$pageTitle = "Create Company Expenses";
		return view('admin.CompanyExpenses.create', compact('pageTitle'));
	}

	public function store(Request $request, $id = null)
	{

		$admin = auth()->guard('admin')->user();

        if (!$admin || !$admin->can('expenses.update') && $id) {
            return response()->view('admin.errors.403', [], 403);
        } elseif (!$admin || !$admin->can('expenses.create')) {
            return response()->view('admin.errors.403', [], 403);
        }

		$request->validate([
			'date' => 'required|date',
			'total_debit' => 'required|numeric|min:0',
			'items' => 'required|array|min:1',
			'items.*.item_no' => 'required|string|max:255',
			'items.*.description' => 'required|string',
			'items.*.debit' => 'required|numeric|min:0',
			'items.*.image' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
			'deleted_items' => 'nullable|array',
			'deleted_items.*' => 'integer|exists:company_expense_items,id'
		]);

		if ($id) {
			$rules['date'][] = Rule::unique('company_expenses', 'date')->ignore($id);
		} else {
			$rules['date'][] = 'unique:company_expenses,date';
		}

		$request->validate($rules);

		DB::beginTransaction();

		try {
			$isUpdate = !is_null($id);

			if ($isUpdate) {
				$companyExpense = CompanyExpense::findOrFail($id);
				$companyExpense->date = $request->date;
				$companyExpense->total_debit = $request->total_debit;
				$companyExpense->no_of_items = count($request->items);
				$companyExpense->save();

				if ($request->has('deleted_items') && is_array($request->deleted_items)) {
					foreach ($request->deleted_items as $deletedItemId) {
						$itemToDelete = CompanyExpenseItem::where('id', $deletedItemId)
							->where('expense_id', $companyExpense->id)
							->first();

						if ($itemToDelete) {

							if ($itemToDelete->image) {
								$imagePath = getFilePath('expenseItems') . '/' . $itemToDelete->image;
								if (file_exists($imagePath)) {
									unlink($imagePath);
								}
							}
							$itemToDelete->delete();
						}
					}
				}
			} else {
				$companyExpense = new CompanyExpense();
				$companyExpense->date = $request->date;
				$companyExpense->total_debit = $request->total_debit;
				$companyExpense->no_of_items = count($request->items);
				$companyExpense->save();
			}

			foreach ($request->items as $index => $itemData) {
				$expenseItem = null;

				if (isset($itemData['id']) && $itemData['id'] && $isUpdate) {
					$expenseItem = CompanyExpenseItem::where('id', $itemData['id'])
						->where('expense_id', $companyExpense->id)
						->first();

					if ($expenseItem) {
						$expenseItem->item_no = $itemData['item_no'];
						$expenseItem->description = $itemData['description'];
						$expenseItem->debit = $itemData['debit'];
					} else {
						$expenseItem = new CompanyExpenseItem();
						$expenseItem->expense_id = $companyExpense->id;
						$expenseItem->item_no = $itemData['item_no'];
						$expenseItem->description = $itemData['description'];
						$expenseItem->debit = $itemData['debit'];
					}
				} else {
					$expenseItem = new CompanyExpenseItem();
					$expenseItem->expense_id = $companyExpense->id;
					$expenseItem->item_no = $itemData['item_no'];
					$expenseItem->description = $itemData['description'];
					$expenseItem->debit = $itemData['debit'];
				}

				if ($request->hasFile("items.{$index}.image")) {
					try {
						if ($isUpdate && $expenseItem->image) {
							$oldImagePath = getFilePath('expenseItems') . '/' . $expenseItem->image;
							if (file_exists($oldImagePath)) {
								unlink($oldImagePath);
							}
						}

						$expenseItem->image = fileUploader($request->file("items.{$index}.image"), getFilePath('expenseItems'));
					} catch (\Exception $exp) {
						DB::rollBack();
						$notify[] = ['error', 'Couldn\'t upload image for item ' . ($index + 1) . ': ' . $exp->getMessage()];
						return back()->withNotify($notify);
					}
				} else {

					if (isset($itemData['old_image']) && $itemData['old_image']) {
						$expenseItem->image = $itemData['old_image'];
					}
				}

				$expenseItem->save();
			}

			DB::commit();

			$message = $isUpdate ? "Company Expense updated successfully" : "Company Expense created successfully";
			$notify[] = ["success", $message];

			return redirect()->route('admin.expenses.index')->withNotify($notify);

		} catch (\Exception $e) {
			DB::rollBack();
			$notify[] = ['error', 'Failed to save expenses: ' . $e->getMessage()];
			return back()->withNotify($notify);
		}
	}

	public function edit($id)
	{
		$admin = auth()->guard('admin')->user();

		if (!$admin || !$admin->can('expenses.update')) {
			return response()->view('admin.errors.403', [], 403);
		}

		$pageTitle = "Edit Expense Entry";
		$companyExpense = CompanyExpense::findOrFail($id);

		$companyExpenseItems = CompanyExpenseItem::where('expense_id', $id)->get();

		return view('admin.CompanyExpenses.create', compact('pageTitle', 'companyExpense', 'companyExpenseItems'));
	}

}
