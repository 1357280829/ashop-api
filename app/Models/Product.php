<?php

namespace App\Models;

use App\Casts\AdminUrl;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;

class Product extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'name', 'cover_url', 'sale_price', 'packing_price', 'is_on', 'sort', 'unit_name', 'admin_user_id',
    ];

    protected $casts = [
        'cover_url' => AdminUrl::class,
    ];
}
