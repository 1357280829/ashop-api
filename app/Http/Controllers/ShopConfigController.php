<?php

namespace App\Http\Controllers;

use App\Enums\CustomCode;

class ShopConfigController extends Controller
{
    /**
     * showdoc
     * @catalog 接口
     * @title 商城配置
     * @description 暂无
     * @method  get
     * @url  /shop-config
     * @param
     * @return {}
     * @return_param shop_config object 商城配置
     * @return_param shop_config object 商城配置
     * @remark 暂无
     * @number 1
     */
    public function index()
    {
        return $this->res(CustomCode::Success, [
            'shop_config' => shop_config(),
        ]);
    }
}
