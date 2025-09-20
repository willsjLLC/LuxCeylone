<?php

namespace App\Http\Controllers\Admin;

use App\Constants\Status;
use App\Http\Controllers\Controller;
use App\Models\Training;
use App\Models\UserTraining;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class TrainingController extends Controller
{
    public function index()
    {
        $admin = auth()->guard('admin')->user();

        if (!$admin || !$admin->can('training.view')) {
            return response()->view('admin.errors.403', [], 403);
        }

        $pageTitle = "Training Types";
        $trainings = Training::all();
        return view('admin.training.index', compact('pageTitle', 'trainings'));
    }

    public function create()
    {
        $admin = auth()->guard('admin')->user();

        if (!$admin || !$admin->can('expenses.create')) {
        	return response()->view('admin.errors.403', [], 403);
        }

        $pageTitle = "Create New Training";
        return view('admin.training.create', compact('pageTitle'));
    }

    public function store(Request $request, $id = 0)
    {
        $admin = auth()->guard('admin')->user();

        if (!$admin || !$admin->can('training.update') && $id) {
            return response()->view('admin.errors.403', [], 403);
        } elseif (!$admin || !$admin->can('training.create')) {
            return response()->view('admin.errors.403', [], 403);
        }

        $request->validate([
            "ticket_price" => 'required',
            "image" => 'nullable|image|mimes:jpeg,png,jpg,gif|max:5120'
        ]);

        if ($id) {
            $training = Training::findOrFail($id);
            $message = "Training Types updated successfully";
        } else {
            $training = new Training();
            $message = "Training Types created successfully";
        }

        if ($request->hasFile('image') && $request->file('image')->isValid()) {
            try {
                $old = $training->image;
                $training->image = fileUploader($request->file('image'), getFilePath('training'), getFileSize('training'), $old);
            } catch (\Exception $exp) {
                $notify[] = ['error', 'Couldn\'t upload your image'];
                return back()->withNotify($notify);
            }
        }
        $training->min_income_threshold = $request->min_income_threshold;
        $training->ticket_price = $request->ticket_price;
        $training->save();

        $notify[] = ["success", $message];
        return back()->withNotify($notify);
    }

    public function delete($id)
    {
        $training = Training::findOrFail($id);
        $training->delete();
        $message = "Training deleted successfully";
        $notify[] = ["success", $message];
        return back()->withNotify($notify);
    }

    public function list()
    {
        $admin = auth()->guard('admin')->user();

        if (!$admin || !$admin->can('training.view')) {
            return response()->view('admin.errors.403', [], 403);
        }

        $pageTitle = "All User Trainings";
        $userTrainings = $this->trainingData();
        return view('admin.training.list', compact('pageTitle', 'userTrainings'));
    }

    public function pending()
    {
        $admin = auth()->guard('admin')->user();

        if (!$admin || !$admin->can('training.view')) {
            return response()->view('admin.errors.403', [], 403);
        }

        $pageTitle = "Pending Trainings";
        $userTrainings = $this->trainingData('pending');
        return view('admin.training.list', compact('pageTitle', 'userTrainings'));
    }

    public function complete()
    {
        $admin = auth()->guard('admin')->user();

        if (!$admin || !$admin->can('training.view')) {
            return response()->view('admin.errors.403', [], 403);
        }

        $pageTitle = "Completed Trainings";
        $userTrainings = $this->trainingData('completed');
        return view('admin.training.list', compact('pageTitle', 'userTrainings'));
    }

    public function reject()
    {
        $admin = auth()->guard('admin')->user();

        if (!$admin || !$admin->can('training.view')) {
            return response()->view('admin.errors.403', [], 403);
        }

        $pageTitle = "Completed Trainings";
        $userTrainings = $this->trainingData('rejected');
        return view('admin.training.list', compact('pageTitle', 'userTrainings'));
    }

    protected function trainingData($scope = null)
    {
        $admin = auth()->guard('admin')->user();

        if (!$admin || !$admin->can('training.view')) {
            return response()->view('admin.errors.403', [], 403);
        }
        
        if ($scope) {
            $trainings = UserTraining::$scope();
        } else {
            $trainings = UserTraining::query();
        }
        return $trainings->searchable(['user:username', 'training:ticket_price'])->filter(['user_id'])->with('user')->orderBy('id', 'desc')->paginate(getPaginate());
    }

    public function updateStatus(Request $request, $id): JsonResponse
    { 

        $request->validate([
            'status' => 'required|integer|in:' . Status::TRAINING_PENDING . ',' . Status::TRAINING_COMPLETED . ',' . Status::TRAINING_REJECTED,
        ]);

        $userTraining = UserTraining::find($id);

        if (!$userTraining) {
            return response()->json(['status' => 'error', 'message' => 'Training record not found.'], 404);
        }

        $userTraining->status = $request->status;
        $userTraining->save();

        $statusText = '';
        switch ($userTraining->status) {
            case Status::TRAINING_PENDING:
                $statusText = 'Pending';
                break;
            case Status::TRAINING_COMPLETED:
                $statusText = 'Completed';
                break;
            case Status::TRAINING_REJECTED:
                $statusText = 'Rejected';
                break;
        }

        return response()->json([
            'status' => 'success',
            'message' => 'Training status updated successfully.',
            'status_text' => $statusText,
        ]);
    }

}
