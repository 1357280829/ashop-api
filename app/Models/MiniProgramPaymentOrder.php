<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;

class MiniProgramPaymentOrder extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'admin_user_id', 'wechat_user_id', 'openid', 'is_success', 'fail_code', 'fail_message', 'prepay_id', 'result',
    ];

    protected $casts = [
        'result' => 'json',
    ];
}
