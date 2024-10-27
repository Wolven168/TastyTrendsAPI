<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Shop extends Model
{
    use HasFactory;
    protected $fillable = [
        'shop_id',
        'shop_name',
        'shop_owner_id',
        'shop_image',
        'expenses',
        'sales',
    ];
    
}
