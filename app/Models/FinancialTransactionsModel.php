<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class FinancialTransactionsModel extends Model
{
    protected $table = 'financial_transactions';
    protected $primaryKey = 'id';

    protected $fillable = [
        'type',
        'amount',
        'comment'
    ];
}
