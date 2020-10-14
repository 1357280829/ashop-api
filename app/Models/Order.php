<?php

namespace App\Models;

use App\Models\Traits\Order\CreateNo;
use App\Models\Traits\Order\CreateTakingCode;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;

class Order extends Model
{
    use HasFactory, SoftDeletes, CreateNo, CreateTakingCode;

    protected $fillable = [
        'no', 'taking_code', 'phone', 'arrived_time', 'carts', 'total_price', 'is_paid', 'remark', 'wechat_user_id',
        'admin_user_id',
    ];

    protected $casts = [
        'carts' => 'json',
    ];
}
