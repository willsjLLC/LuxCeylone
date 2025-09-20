<?php

namespace App\Models;

use App\Constants\Status;
use App\Traits\GlobalStatus;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Model;

class JobPost extends Model
{
    use GlobalStatus;

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }
    public function category()
    {
        return $this->belongsTo(Category::class, 'category_id');
    }

    public function subCategory()
    {
        return $this->belongsTo(SubCategory::class, 'subcategory_id');
    }

    public function proves()
    {
        return $this->hasMany(JobProve::class);
    }

    public function scopePending($query)
    {
        return $query->where('status', Status::JOB_PENDING);
    }

    public function scopeApproved($query)
    {
        return $query->where('status', Status::JOB_APPROVED);
    }

    public function scopeCompleted($query)
    {
        return $query->where('status', Status::JOB_COMPLETED);
    }

    public function scopePause($query)
    {
        return $query->where('status', Status::JOB_PAUSE);
    }

    public function scopeRejected($query)
    {
        return $query->where('status', Status::JOB_REJECTED);
    }

    public function statusJob(): Attribute
    {
        return new Attribute(function () {
            $html = '';
            if ($this->status == Status::JOB_PENDING) {
                $html = '<span class="badge badge--warning">' . trans('Pending') . '</span>';
            } elseif ($this->status == Status::JOB_APPROVED) {
                $html = '<span class="badge badge--primary">' . trans('Approved') . '</span>';
            } elseif ($this->status == Status::JOB_COMPLETED) {
                $html = '<span class="badge badge--success">' . trans('Completed') . '</span>';
            } elseif ($this->status == Status::JOB_PAUSE) {
                $html = '<span class="badge badge--dark">' . trans('Paused') . '</span>';
            } else if ($this->status == Status::JOB_ONGOING) {
                $html = '<span class="badge badge--success">' . trans('Ongoing') . '</span>';
            } else {
                $html =  '<span class="badge badge--danger">' . trans('Rejected') . '</span>';
            }
            return $html;
        });
    }

    public function allowedExtensions($isHtml = false)
    {
        $extensions = $this->file_name ? explode(',', $this->file_name) : [];
        if (!$isHtml) return $extensions;

        return count($extensions) ? '.' . implode(',.', $extensions) : null;
    }
}
