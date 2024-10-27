<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Ticket extends Model
{
    use HasFactory;
    protected $fillable = [
        'ticket_id',
        'shop_id',
        'shop_name',
        'buyer_id',
        'buyer_name',
        'item_id',
        'item_name',
        'item_image',
        'quantity',
        'price',
        'status',
        'location',
        'desc'
    ];
}
