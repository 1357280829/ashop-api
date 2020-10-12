<?php

namespace App\Http\Controllers;

use App\Enums\CustomCode;

class ShopConfigController extends Controller
{
    public function index()
    {
        return $this->res(CustomCode::Success, [
            'shop_configs' => shop_configs(),
        ]);
    }
}
