<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CompanyExpenseItem extends Model
{

    protected $fillable = [
        'expense_id',
        'item_no',
        'description',
        'debit',
        'image'
    ];

    public function expense()
    {
        return $this->belongsTo(CompanyExpense::class, 'expense_id', 'id');
    }
}
