<?php

namespace App\Models;

use App\Models\Traits\Authenticated\CreateToken;
use App\Models\Traits\Authenticated\LoadAuthenticatedUser;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Authenticated extends Model
{
    use HasFactory, CreateToken, LoadAuthenticatedUser;

    protected $fillable = [
        'authenticate_type', 'authenticated_user_model', 'authenticated_user_id', 'token', 'ttl', 'expired_at',
        'admin_user_id',
    ];
}
