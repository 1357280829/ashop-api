<?php

namespace App\Http\Controllers;

use App\Enums\CustomCode;
use App\Models\Category;

class CategoriesController extends Controller
{
    public function index()
    {
        $categories = Category::query()
            ->with([
                'products' => function ($query) {
                    return $query->where('is_on', 1)->orderByDesc('sort');
                },
            ])
            ->orderByDesc('sort')
            ->get();

        return $this->res(CustomCode::Success, [
            'categories' => $categories,
        ]);
    }
}
