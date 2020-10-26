<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;

class MiniProgramPaymentOrderNotify extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'admin_user_id', 'wechat_user_id', 'order_id', 'is_success', 'fail_code', 'fail_message', 'result',
    ];

    protected $casts = [
        'result' => 'json',
    ];
}
