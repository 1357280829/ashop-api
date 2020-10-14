<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;

class Category extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'name', 'cover_url', 'sort', 'admin_user_id',
    ];

    public function products()
    {
        return $this->belongsToMany(Product::class, 'category_product_relations');
    }
}
