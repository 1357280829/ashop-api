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
     * @return_param shop_config.shop_cover_url string 商城封面图url
     * @return_param shop_config.shop_name      string 商城店铺名
     * @return_param shop_config.shop_desc      string 商城简介
     * @return_param shop_config.business_hours string 商城营业时间
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
