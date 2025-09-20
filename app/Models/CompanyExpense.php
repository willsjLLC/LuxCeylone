<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Symfony\Component\Console\Exception\CommandNotFoundException;

class CompanyExpense extends Model
{
    protected $fillable = [
        'date',
        'total_debit',
        'no_of_items'
    ];

    public function expenseItem()
    {
        return $this->hasMany(CompanyExpenseItem::class);
    }

    public function scopeSearchable($query, $searchableFields = [])
    {
        $search = request()->search;
        
        if ($search) {
            $query->where(function ($q) use ($search, $searchableFields) {
                foreach ($searchableFields as $field) {
                    if (strpos($field, ':') !== false) {
                        $parts = explode(':', $field);
                        $relation = $parts[0];
                        $relationField = $parts[1];
                        $q->orWhereHas($relation, function ($relationQuery) use ($relationField, $search) {
                            $relationQuery->where($relationField, 'like', '%' . $search . '%');
                        });
                    } else {
                        if ($field === 'date') {
                            $q->orWhere($field, 'like', '%' . $search . '%');
                        } else {
                            $q->orWhere($field, 'like', '%' . $search . '%');
                        }
                    }
                }
            });
        }
        
        return $query;
    }
}
