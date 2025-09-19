<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Expense extends Model
{
    use HasFactory;

    protected $fillable = [
        'expense_head_id',
        'user_id',
        'expense_date',
        'remarks',
        'amount',
    ];

    public function expenseHead()
    {
        return $this->belongsTo(ExpenseHead::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
