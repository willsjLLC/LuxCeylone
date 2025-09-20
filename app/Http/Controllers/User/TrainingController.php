<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Models\Training;
use App\Models\User;
use App\Models\UserTraining;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class TrainingController extends Controller
{
    public function trainingView()
    {
        $user = auth()->user();
        $pageTitle = "Training Page";
        $userTrainingData = UserTraining::where('user_id', $user->id)->get();
        $trainings = Training::all();
        $totalEarning = $user->total_earning;

        return view('Template::user.training.index', compact('pageTitle', 'userTrainingData', 'trainings', 'user'));
    }

}
