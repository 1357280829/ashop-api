<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;

class Store extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'key', 'admin_user_id',
    ];

    protected $hidden = [
        'deleted_at',
    ];

    public function AdminUser()
    {
        return $this->belongsTo(AdminUser::class, 'admin_user_id', 'id');
    }
}
