<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;

class ShopConfig extends Model
{
    use HasFactory;

    protected $table = 'shop_config';

    protected $fillable = [
        'key', 'name', 'value', 'admin_user_id',
    ];
}
