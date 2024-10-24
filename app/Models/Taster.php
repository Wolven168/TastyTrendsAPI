<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Taster extends Model
{
    use HasFactory;
    protected $fillable = [
        'user_name',
        'user_id',
        'email',
        'password',
        'user_type',
        'store_id',
        'user_img',
        'remember_me'
    ];
}
