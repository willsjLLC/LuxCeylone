<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;

class ClaimedRankReward extends Model
{
    protected $fillable = [
        'user_id',
        'current_rank_id',
        'rank_one_status',
        'rank_one_claimed_status',
        'rank_two_status',
        'rank_two_claimed_status',
        'rank_three_status',
        'rank_three_claimed_status',
        'rank_four_status',
        'rank_four_claimed_status'
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function rank()
    {
        return $this->belongsTo(Rank::class, 'current_rank_id', 'id');
    }

    public function getRankStatus($rankNumber)
    {
        $statusField = 'rank_' . $this->numberToWord($rankNumber) . '_status';
        Log::info('Retrieving rank status', [
            'user_id' => $this->user_id,
            'rank_number' => $rankNumber,
            'status_field' => $statusField,
            'value' => $this->$statusField
        ]);
        return $this->$statusField;
    }

    public function getRankClaimedStatus($rankNumber)
    {
        $statusField = 'rank_' . $this->numberToWord($rankNumber) . '_claimed_status';
        Log::info('Retrieving claim status', [
            'user_id' => $this->user_id,
            'rank_number' => $rankNumber,
            'status_field' => $statusField,
            'value' => $this->$statusField
        ]);
        return $this->$statusField;
    }

    public function setRankClaimedStatus($rankNumber, $status)
    {
        $statusField = 'rank_' . $this->numberToWord($rankNumber) . '_claimed_status';

        Log::info('Attempting to set claim status', [
            'user_id' => $this->user_id,
            'rank_number' => $rankNumber,
            'status_field' => $statusField,
            'old_status' => $this->$statusField,
            'new_status' => $status
        ]);

        $this->$statusField = $status;

        if (!$this->save()) {
            Log::error('Failed to save claim status', [
                'user_id' => $this->user_id,
                'rank_number' => $rankNumber,
                'status_field' => $statusField,
                'attempted_status' => $status
            ]);
            throw new \Exception('Failed to save claim status to database');
        }

        Log::info('Claim status updated successfully', [
            'user_id' => $this->user_id,
            'rank_number' => $rankNumber,
            'status_field' => $statusField,
            'new_status' => $this->$statusField
        ]);

        return $this;
    }

    private function numberToWord($number)
    {
        $words = [
            1 => 'one',
            2 => 'two',
            3 => 'three',
            4 => 'four'
        ];
        return $words[$number] ?? 'one';
    }
}