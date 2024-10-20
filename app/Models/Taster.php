<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Taster extends Model
{
    use HasFactory;
    protected $fillable = [
        'username',
        'user_id',
        'email',
        'password',
        'user_type',
        'store_id',
        'remember_me'
    ];
}
