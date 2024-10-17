<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Ticket extends Model
{
    use HasFactory;
    protected $fillable = [
        'ticker_id',
        'shop_id',
        'buyer_id',
        'order',
        'price',
        'status',
        'desc'
    ];
}
