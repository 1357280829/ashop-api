<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;

class Order extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'phone', 'arrived_at', 'products', 'packing_price', 'delivery_price', 'total_price', 'remark',
    ];

    protected $casts = [
        'products' => 'json',
    ];
}
