<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use App\Models\Favorite;
use App\Models\FavoriteProducts;

class FavoriteController extends Controller
{

    public function toggleFavorite(Request $request)
    {
        $userId = $request->user_id;
        $jobId = $request->job_id;

        // Check if the userId and jobId is not null
        if (!$userId || !$jobId) {
            return response()->json(['status' => 'error', 'message' => 'User ID or Job ID is missing'], 400);
        }

        // Check if the job is already favorited
        $favorite = Favorite::where('user_id', $userId)
            ->where('job_id', $jobId)
            ->first();

        if ($favorite) {
            $favorite->delete();
            return response()->json(['status' => 'removed']);
        } else {
            Favorite::create(['user_id' => $userId, 'job_id' => $jobId]); // Add to favorites
            return response()->json(['status' => 'added']);
        }
    }

    public function getUserFavorites(Request $request)
    {
        $userId = $request->input('user_id');

        if (!$userId) {
            return response()->json(['status' => 'error', 'message' => 'User ID is missing'], 400);
        }

        try {
            // Get the favorite job IDs for this user
            $favorites = Favorite::where('user_id', $userId)->get();

            return response()->json($favorites);
        } catch (\Exception $e) {
            // Log::error('Error retrieving favorites: ' . $e->getMessage());
            return response()->json(['status' => 'error', 'message' => 'Error retrieving favorites'], 500);
        }
    }

    public function toggleFavoriteProducts(Request $request)
    {
        $userId = $request->user_id;
        $productId = $request->product_id;

        // Check if the userId and jobId is not null
        if (!$userId || !$productId) {
            return response()->json(['status' => 'error', 'message' => 'User ID or Product ID is missing'], 400);
        }

        // Check if the job is already favorited
        $favorite = FavoriteProducts::where('user_id', $userId)
            ->where('product_id', $productId)
            ->first();

        if ($favorite) {
            $favorite->delete();
            return response()->json(['status' => 'removed']);
        } else {
            FavoriteProducts::create(['user_id' => $userId, 'product_id' => $productId]); // Add to favorites
            return response()->json(['status' => 'added']);
        }
    }

    public function getFavoritesProducts(Request $request)
    {
        $userId = $request->input('user_id');

        if (!$userId) {
            return response()->json(['status' => 'error', 'message' => 'User ID is missing'], 400);
        }
        try {
            // Get the favorite product IDs for this user
            $favoriteProductIds = FavoriteProducts::where('user_id', $userId)->pluck('product_id')->toArray();
            return response()->json($favoriteProductIds);
        } catch (\Exception $e) {
            return response()->json(['status' => 'error', 'message' => 'Error retrieving favorites'], 500);
        }
    }

    public function removeUserFavorites($fav_id)
    {
        Favorite::find($fav_id)->delete();
        return back();
    }

    public function removeProductFavorites($fav_id)
    {
        FavoriteProducts::find($fav_id)->delete();
        return back();
    }
}
