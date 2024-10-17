<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Item extends Model
{
    protected $fillable = [
        'shop_id',
        'item_id',
        'item_name',
        'item_price',
        'item_image',
        'available',
        'item_desc'
    ];
    use HasFactory;
}
