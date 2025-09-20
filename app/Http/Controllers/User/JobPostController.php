<?php

namespace App\Http\Controllers\User;

use Exception;
use App\Models\JobPost;
use App\Models\Category;
use App\Models\FileType;
use App\Models\JobProve;
use App\Constants\Status;
use App\Models\Transaction;
use Illuminate\Http\Request;
use App\Rules\FileTypeValidate;
use App\Models\AdminNotification;
use App\Http\Controllers\Controller;
use App\Models\PaymentOption;
use App\Models\User;

class JobPostController extends Controller
{
    public function create()
    {
        $pageTitle  = "Create Job";
        $categories = Category::active()->orderBy('name')->with('subcategory', function ($query) {
            $query->active()->get();
        })->get();

        $paymentOptions = PaymentOption::where('status', Status::ENABLE)->get();
        $files      = FileType::active()->get();
        return view('Template::user.job.create', compact('pageTitle', 'files', 'categories', 'paymentOptions'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'category_id'    => 'required',
            'subcategory_id' => 'nullable|integer|exists:sub_categories,id',
            'job_proof'      => 'required|in:1,2',
            'file_name'      => 'required_if:job_proof,2|array',
            'quantity'       => 'required|integer|gt:0',
            'rate'           => 'required|numeric|gt:0',
            'title'          => 'required|string|max:255',
            'description'    => 'required',
            'attachment'     => ['required', 'image', new FileTypeValidate(['jpeg', 'jpg', 'png'])],
            'payment_option' => 'required|integer|exists:payment_option,id',

        ], [
            'attachment.required' => 'Image field required, Please upload image'
        ]);

        $purifier = new \HTMLPurifier();

        $filename  = '';
        $user      = auth()->user();
        $jobAmount = $request->quantity * $request->rate;

        if ($request->job_proof == Status::JOB_PROVE_REQUIRED) {
            $file = FileType::active()->whereIn('name', $request->file_name)->count();

            if ($file != count($request->file_name)) {
                $notify[] = ['error', 'Job proof file name is wrong'];
                return back()->withNotify($notify)->withInput();
            }

            $filename = implode(',', $request->file_name);
        }

        if ($user->balance < $jobAmount && $request->payment_option == 6) {
            $notify[] = ['error', 'You have no sufficient balance.'];
            return back()->withNotify($notify)->withInput();
        }


        $job = new JobPost();

        if ($request->hasFile('attachment')) {
            try {
                $old             = $job->attachment;
                $job->attachment = fileUploader($request->attachment, getFilePath('jobPoster'), getFileSize('jobPoster'), $old);
            } catch (\Exception $exp) {
                $notify[] = ['error', 'Couldn\'t upload your image'];
                return back()->withNotify($notify);
            }
        }

        $job->user_id           = $user->id;
        $job->category_id       = $request->category_id;
        $job->subcategory_id    = $request->subcategory_id ?? 0;
        $job->job_proof         = $request->job_proof;
        $job->file_name         = $filename;
        $job->quantity          = $request->quantity;
        $job->vacancy_available = $request->quantity;
        $job->rate              = $request->rate;
        $job->total             = $jobAmount;
        $job->amount            = $jobAmount;
        $job->title             = $request->title;
        $job->payment_option_id    = $request->payment_option;
        $job->description       = $purifier->purify($request->description);
        $job->status            = gs()->approve_job;
        $job->job_code          = getTrx();
        $job->save();

        $companyAccount = User::where('username', 'sljob')->first();

        if (!$companyAccount) {
            $notify[] = ['error', 'Something went wrong'];
            return back()->withNotify($notify);
        }

        if ($request->payment_option == 6) {
            $transaction               = new Transaction();
            $transaction->user_id      = $companyAccount->id;
            $transaction->amount       = $jobAmount;
            $transaction->post_balance = $companyAccount->balance + $jobAmount;
            $transaction->trx_type     = '+';
            $transaction->details      = "Job title " . $job->title;
            $transaction->trx          = $job->job_code;
            $transaction->remark       = 'job_post';
            $transaction->save();

            $transaction               = new Transaction();
            $transaction->user_id      = $job->user_id;
            $transaction->amount       = $jobAmount;
            $transaction->post_balance = $user->balance - $jobAmount;
            $transaction->trx_type     = '-';
            $transaction->details      = "Job title " . $job->title;
            $transaction->trx          = $job->job_code;
            $transaction->remark       = 'job_post';
            $transaction->save();

            $postBalance   = $user->balance - $jobAmount;
            $user->balance = $postBalance;
            $user->save();

            $companyAccount->balance += $jobAmount;
            $companyAccount->save();
        }

        $adminNotification            = new AdminNotification();
        $adminNotification->user_id   = $user->id;
        $adminNotification->title     = 'Money has been deducted for job posting';
        $adminNotification->click_url = urlPath('admin.jobs.view', $job->id);
        $adminNotification->save();

        notify($user, 'JOB_POST_SUCCESSFULLY', [
            'quantity' => $request->quantity,
            'amount'   => showAmount($job->rate, currencyFormat: false),
            'charge'   => showAmount($job->amount, currencyFormat: false),
            'job_code' => $job->job_code,
        ]);

        $notify[] = ['success', 'Job created successfully'];
        return redirect()->route('user.job.history')->withNotify($notify);
    }

    public function edit($id)
    {
        $pageTitle  = 'Edit Job';
        $job        = JobPost::where('user_id', auth()->id())->findOrFail($id);
        $categories = Category::active()->orderBy('name')->with('subcategory')->get();
        $files      = FileType::active()->get();
        return view('Template::user.job.edit', compact('pageTitle', 'job', 'categories', 'files'));
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'category_id'    => 'required',
            'subcategory_id' => 'nullable|integer|exists:sub_categories,id',
            'job_proof'      => 'required|in:1,2',
            'file_name'      => 'required_if:job_proof,2',
            'quantity'       => 'required|integer|gt:0',
            'rate'           => 'required|numeric|gt:0',
            'title'          => 'required|string|max:255',
            'description'    => 'required',
            'attachment'     => ['image', new FileTypeValidate(['jpeg', 'jpg', 'png'])],
        ]);

        $purifier = new \HTMLPurifier();
        $user     = auth()->user();
        $job      = JobPost::where('id', $id)->where('user_id', $user->id)->first();

        if ($job->status == Status::JOB_COMPLETED || $job->status == Status::JOB_REJECTED) {
            $notify[] = ['error', 'Sorry! your job will not editable'];
            return back()->withNotify($notify)->withInput();
        }

        $filename = '';

        if ($request->job_proof == Status::JOB_PROVE_REQUIRED) {

            $file = FileType::active()->whereIn('name', $request->file_name)->count();

            if ($file != count($request->file_name)) {
                $notify[] = ['error', 'Job proof file name is wrong'];
                return back()->withNotify($notify)->withInput();
            }

            $filename = implode(',', $request->file_name);
        }

        if ($request->hasFile('attachment')) {
            try {
                $old             = $job->attachment;
                $job->attachment = fileUploader($request->attachment, getFilePath('jobPoster'), getFileSize('jobPoster'), $old);
            } catch (\Exception $exp) {
                $notify[] = ['error', 'Couldn\'t upload your image'];
                return back()->withNotify($notify);
            }
        }

        $newAmount  = null;
        $subBalance = null;
        $oldAmount = $job->amount;

        // if (!($job->quantity == $request->quantity && $job->rate == $request->rate)) {

        //     $oldPostBalance = $job->amount;
        //     $newAmount      = $request->rate * $request->quantity;

        //     if ($oldPostBalance > $newAmount) {
        //         $addBalance  = $oldPostBalance - $newAmount;
        //         $userBalance = $user->balance + $addBalance;
        //     }

        //     if ($oldPostBalance < $newAmount) {
        //         $subBalance  = $newAmount - $oldPostBalance;
        //         $userBalance = $user->balance - $subBalance;
        //     }

        //     if (@$subBalance && ($subBalance > $user->balance)) {
        //         $notify[] = ['error', 'You have no sufficient balance in your account'];
        //         return back()->withNotify($notify)->withInput();
        //     }

        //     $user->balance = $userBalance;
        //     $user->save();

        //     $job->quantity = $request->quantity;
        //     $job->rate     = $request->rate;
        //     $job->total    = $newAmount;
        //     $job->amount   = $newAmount;
        // } else {
        //     $oldAmount = $job->amount;
        // }

        $job->category_id       = $request->category_id;
        $job->subcategory_id    = $request->subcategory_id;
        $job->job_proof         = $request->job_proof;
        $job->file_name         = $filename;
        $job->quantity          = $request->quantity;
        $job->vacancy_available = $request->quantity;
        $job->rate              = $request->rate;
        $job->total             = $newAmount ?? $oldAmount;
        $job->amount            = $newAmount ?? $oldAmount;
        $job->title             = $request->title;
        $job->status            = gs()->approve_job;
        $job->description       = $purifier->purify($request->description);
        $job->save();

        if ($newAmount) {
            $transaction               = new Transaction();
            $transaction->user_id      = $user->id;
            $transaction->amount       = $addBalance ?? $subBalance;
            $transaction->post_balance = $user->balance;
            $transaction->trx_type     = $subBalance ? '-' : '+';
            $transaction->details      = "Updated job " . $job->title;
            $transaction->trx          = $job->job_code;
            $transaction->remark       = 'job_post';
            $transaction->save();
        }

        $adminNotification            = new AdminNotification();
        $adminNotification->user_id   = $user->id;
        $adminNotification->title     = 'User job updated';
        $adminNotification->click_url = urlPath('admin.jobs.index');
        $adminNotification->save();

        notify($user, 'JOB_UPDATE_SUCCESSFULLY', [
            'quantity' => showAmount($request->quantity, currencyFormat: false),
            'amount'   => showAmount($job->rate, currencyFormat: false),
            'charge'   => showAmount($job->amount, currencyFormat: false),
            'job_code' => $job->job_code,
            'name'     => $user->name,
        ]);

        $notify[] = ['success', 'Job updated successfully'];
        return redirect()->route('user.job.history')->withNotify($notify);
    }

    public function prove(Request $request, $id)
    {
        $user       = auth()->user();
        $job        = JobPost::approved()->where('user_id', '!=', $user->id)->where('id', $id)->firstOrFail();

        if ($user->employee_package_activated == Status::DISABLE) {
            $notify[] = ['error', 'Please Activate Employee Package.'];
            return redirect('user/deposit/employee/package-active')->withNotify($notify); 
        }

        $attachmentValidation = '';
        if ($job->job_proof == Status::JOB_PROVE_REQUIRED) {
            $attachmentValidation = ['required', new FileTypeValidate($job->allowedExtensions())];
        }

        $request->validate([
            'detail' => 'required',
            'attachment' => $attachmentValidation
        ]);

        $existProve = JobProve::where('job_post_id', $job->id)->where('user_id', $user->id)->exists();

        if ($existProve) {
            $notify[] = ['error', 'You\'ve already submitted'];
            return back()->withNotify($notify)->withInput();
        }

        if ($job->vacancy_available <= 0) {
            $notify[] = ['error', 'Job already finished'];
            return back()->withNotify($notify);
        }

        $attachment = '';

        if ($job->job_proof == Status::JOB_PROVE_REQUIRED) {
            if ($request->hasFile('attachment')) {
                try {
                    $attachment = fileUploader($request->attachment, getFilePath('jobProve'));
                } catch (\Exception $e) {
                    $notify[] = ['error', 'Couldn\'t upload your image'];
                    return back()->withNotify($notify);
                }
            }
        }

        $prove              = new JobProve();
        $prove->user_id     = $user->id;
        $prove->job_post_id = $job->id;
        $prove->detail      = $request->detail;
        $prove->attachment  = $attachment;
        $prove->save();

        notify($user, 'JOB_PROOF_SUBMITTED', [
            'job_rate' => showAmount($job->rate, currencyFormat: false),
            'job_code' => $job->job_code,
        ]);

        $notify[] = ['success', 'Your job proof request has been taken.'];
        return to_route('job.list')->withNotify($notify);
    }

    public function attachment($id)
    {
        $pageTitle = "Job Details";

        try {
            $id = decrypt($id);
        } catch (Exception $e) {
            $notify[] = ['error', 'Invalid URL.'];
            return back()->withNotify($notify);
        }
        $prove     = JobProve::where('id', $id)->WhereHas('job', function ($query) {
            $query->where('user_id', auth()->id());
        })->with(['job', 'user'])->firstOrFail();
        $prove->notification = Status::YES;
        $prove->save();

        return view('Template::user.job.attachment', compact('pageTitle', 'prove'));
    }

    public function downloadAttachment($id)
    {
        try {
            $id = decrypt($id);
        } catch (Exception $e) {
            $notify[] = ['error', 'Invalid URL'];
            return back()->withNotify($notify);
        }

        $attachment = JobProve::WhereHas('job', function ($query) {
            $query->where('user_id', auth()->id());
        })->findOrFail($id);
        $path = getFilePath('jobProve') . '/' . $attachment->attachment;

        if (!file_exists($path)) {
            $notify[] = ['error', 'File doesn\'t found'];
            return back()->withNotify($notify);
        }

        return response()->download($path);
    }

    public function approve($id)
    {
        try {
            $id = decrypt($id);
        } catch (Exception $e) {
            $notify[] = ['error', 'Invalid URL'];
            return back()->withNotify($notify);
        }

        $jobProve = JobProve::where('id', $id)->WhereHas('job', function ($query) {
            $query->where('user_id', auth()->id());
        })->with('job', 'user')->pending()->firstOrFail();

        $job    = $jobProve->job;
        $approvedProvesCount = JobProve::approve()->where('job_post_id', $job->id)->count();

        if ($approvedProvesCount >= $job->quantity) {
            $notify[] = ['error', 'Job vacancy already finished'];
            return back()->withNotify($notify);
        }

        $amount = $job->rate;

        if ($job->amount < $amount) {
            $notify[] = ['error', 'You have no sufficient job amount'];
            return back()->withNotify($notify);
        }

        $job->decrement('amount', $amount);

        $job->decrement('vacancy_available', 1);

        $job->status = Status::JOB_ONGOING;
        $job->save();
        $jobProve->status = Status::JOB_PROVE_APPROVE;
        $jobProve->save();

        notify($job->user, 'JOB_APPROVE_SUCCESSFULLY', [
            'job_code'     => $jobProve->job->job_code,
        ]);

        $notify[] = ['success', 'Job request approved successfully'];
        return redirect()->route('user.job.details', $job->id)->withNotify($notify);
    }

    public function reject($id)
    {
        try {
            $id = decrypt($id);
        } catch (Exception $e) {
            $notify[] = ['error', 'Invalid URL.'];
            return back()->withNotify($notify);
        }
        $jobProve = JobProve::where('id', $id)->WhereHas('job', function ($query) {
            $query->where('user_id', auth()->id());
        })->with('job', 'user')->firstOrFail();
        $job = $jobProve->job;

        if (!$job) {
            $notify[] = ['error', 'Job not found!'];
            return back()->withNotify($notify);
        }

        $jobProve->status = Status::JOB_PROVE_REJECT;
        $jobProve->save();

        $job->vacancy_available += 1;
        if ($job->status == Status::JOB_COMPLETED) {
            $job->status = Status::JOB_APPROVED;
        }
        $job->save();

        notify($jobProve->user, 'JOB_PROVE_REJECTED', [
            'rejected_by' => auth()->user()->username,
            'amount'    => showAmount($job->rate, currencyFormat: false),
            'job_code'  => $job->job_code,
        ]);

        $notify[] = ['success', 'Job rejected successfully'];
        return back()->withNotify($notify);
    }

    public function finished()
    {
        $pageTitle = "Complete Job History";
        $jobs = $this->jobProve('approve');
        return view('Template::user.job.finished', compact('pageTitle', 'jobs'));
    }

    public function apply()
    {
        $pageTitle = "Apply Jobs";
        $jobs = $this->jobProve();
        return view('Template::user.job.finished', compact('pageTitle', 'jobs'));
    }

    public function history(Request $request)
    {
        $pageTitle = "Jobs History";
        $jobs      = JobPost::searchable(['job_code', 'title'])->where('user_id', auth()->id())->with('proves')->latest()->paginate(getPaginate());
        return view('Template::user.job.history', compact('pageTitle', 'jobs'));
    }

    protected function jobProve($scope = null)
    {
        if ($scope) {
            $jobProves = JobProve::$scope()->where('user_id', auth()->id());
        } else {
            $jobProves = JobProve::where('user_id', auth()->id());
        }
        return $jobProves->with(['job', 'job.user'])->orderBy('id', 'desc')->paginate(getPaginate());
    }

    public function status($id)
    {
        $job = JobPost::where('user_id', auth()->id())->where('id', $id)->firstOrFail();

        if ($job->status == Status::JOB_COMPLETED || $job->status == Status::JOB_REJECTED || $job->status == Status::JOB_PENDING || $job->status == Status::JOB_ONGOING) {
            $notify[] = ['success', 'Job status wrong'];
            return back()->withNotify($notify);
        }

        $job->status = $job->status == Status::JOB_APPROVED ? Status::JOB_PAUSE : Status::JOB_APPROVED;
        $job->save();
        $notify[] = ['success', 'Job status updated successfully'];
        return back()->withNotify($notify);
    }

    public function details($id)
    {
        $pageTitle = "Job Request Details";
        $job       = JobPost::where('id', $id)->where('user_id', auth()->id())->with('proves', function ($query) {
            $query->orderBy('status')->orderBy('created_at')->with('user');
        })->firstOrFail();

        return view('Template::user.job.details', compact('pageTitle', 'job'));
    }

    public function updateJobStatus(Request $request)
    {
        $validated = $request->validate([
            'job_prove_id' => 'required|exists:job_proves,id',
            'action' => 'required|in:start,cancel,finish,confirm_finish',
        ]);

        $jobProve = JobProve::where('id', $validated['job_prove_id'])->firstOrFail();
        $job = $jobProve->job;

        switch ($validated['action']) {
            case 'start':
                if ($job->status == Status::JOB_PENDING || $job->status == Status::JOB_REJECTED || $job->status == Status::JOB_PAUSE) {
                    $notify[] = ['error', 'Job cannot be started.'];
                    return back()->withNotify($notify);
                }
                if ($jobProve->user->employee_package_activated == Status::DISABLE) {
                    $notify[] = ['error', 'Please Activate Employee Package.'];
                    return redirect('user/employee/package-active')->withNotify($notify);
                }

                updateEmployeePackageHistory($jobProve->user_id);

                $jobProve->status = Status::JOB_PROVE_START;
                $jobProve->save();

                $notify[] = ['success', 'Job started successfully.'];
                return back()->withNotify($notify);

            case 'finish':
                if ($jobProve->status !== Status::JOB_PROVE_START) {
                    $notify[] = ['error', 'Only started jobs can be finished.'];
                    return back()->withNotify($notify);
                }

                if ($job->payment_option_id == 5) {

                    $companyAccount = User::where('username', 'sljob')->first();

                    if (!$companyAccount) {
                        $notify[] = ['error', 'Something went wrong'];
                        return back()->withNotify($notify);
                    }

                    $jobProve->payment_status = Status::PAYMENT_SUCCESS;
                    $jobProve->payment_option_id = 5;

                    $transaction               = new Transaction();
                    $transaction->user_id      = $jobProve->user_id;
                    $transaction->amount       = $jobProve->job->rate;
                    $transaction->post_balance = $jobProve->user->balance;
                    $transaction->charge       = 0;
                    $transaction->trx_type     = '+';
                    $transaction->remark       = "payment_receive";
                    $transaction->details      = 'Payment By ' . $job->user->username . 'For Job Code ' . $jobProve->job->job_code;
                    $transaction->trx          = $jobProve->job->job_code;
                    $transaction->save();

                    // $jobProve->user->balance += $jobProve->job->rate;
                    // $jobProve->user->save();

                    jobCommission($jobProve->user_id, $jobProve->job->rate,$job->payment_option_id);
                }

                $jobProve->status = Status::JOB_PROVE_COMPLETE;
                $jobProve->save();
                $notify[] = ['success', 'Job finished successfully.'];
                return back()->withNotify($notify);

            case 'confirm_finish':
                if ($jobProve->status !== Status::JOB_PROVE_COMPLETE) {
                    $notify[] = ['error', 'Only completed jobs can be confirmed as finished.'];
                    return back()->withNotify($notify);
                }

                if ($job->payment_option_id == 6) {

                    $companyAccount = User::where('username', 'sljob')->first();

                    if (!$companyAccount) {
                        $notify[] = ['error', 'Something went wrong'];
                        return back()->withNotify($notify);
                    }

                    $jobProve->payment_status = Status::PAYMENT_SUCCESS;
                    $jobProve->payment_option_id = 6;

                    $transaction               = new Transaction();
                    $transaction->user_id      = $jobProve->user_id;
                    $transaction->amount       = $jobProve->job->rate;
                    $transaction->post_balance = $jobProve->user->balance;
                    $transaction->charge       = 0;
                    $transaction->trx_type     = '+';
                    $transaction->remark       = "payment_receive";
                    $transaction->details      = 'Payment By ' . $job->user->username . 'For Job Code ' . $jobProve->job->job_code;
                    $transaction->trx          = $jobProve->job->job_code;
                    $transaction->save();

                    $transaction               = new Transaction();
                    $transaction->user_id      = $companyAccount->id;
                    $transaction->amount       = $jobProve->job->rate;
                    $transaction->post_balance = $companyAccount->balance - $jobProve->job->rate;
                    $transaction->charge       = 0;
                    $transaction->trx_type     = '-';
                    $transaction->remark       = "payment_send";
                    $transaction->details      = 'Payment To ' . $jobProve->user->username . 'For Job Code ' . $jobProve->job->job_code;
                    $transaction->trx          = $jobProve->job->job_code;
                    $transaction->save();

                    $jobProve->user->balance += $jobProve->job->rate;
                    $jobProve->user->save();

                    $companyAccount->balance -= $jobProve->job->rate;
                    $companyAccount->save();

                    jobCommission($jobProve->user_id, $jobProve->job->rate,$job->payment_option_id);
                }

                $jobProve->status = Status::JOB_PROVE_COMPLETE_CONFIRM;
                $jobProve->save();

                if ($job->vacancy_available == 0) {
                    $job->status = Status::JOB_COMPLETED;
                    $job->save();
                }

                $notify[] = ['success', 'Job confirmed as finished successfully.'];
                return back()->withNotify($notify);

            default:
                $notify[] = ['error', 'Invalid action.'];
                return back()->withNotify($notify);
        }
    }
}
