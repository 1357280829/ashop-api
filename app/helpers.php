<?php

use App\Enums\CustomCode;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

/**
 * 统一响应
 *
 * @param int $customCode
 * @param array $data
 * @param string $message
 * @return \Illuminate\Http\JsonResponse
 */
function res($customCode = CustomCode::Success, $data = [], $message = '')
{
    $message = $message ?: CustomCode::getDescription($customCode);

    return response()->json([
        'custom_code' => $customCode,
        'data' => $data,
        'message' => $message
    ]);
}

/**
 * 获取所有商城配置
 *
 * @return \Illuminate\Config\Repository|mixed
 */
function shop_configs()
{
    $shopConfigs = config('shopconfig.default');

    $currentShopConfigs = DB::table('shop_config')
        ->whereIn('key', config('shopconfig.keys'))
        ->pluck('value', 'key')
        ->toArray();

    foreach ($shopConfigs as $key => $value) {
        $shopConfigs[$key] = $currentShopConfigs[$key] ?? $value;
        if ($key == 'shop_cover_url') {
            $shopConfigs[$key] = admin_url($shopConfigs[$key]);
        }
    }

    return $shopConfigs;
}

/**
 * 获取单个商城配置
 *
 * @param $key
 * @return \Illuminate\Config\Repository|mixed
 */
function shop_config($key)
{
    $shopConfig = DB::table('shop_config')->where('key', $key)->first();

    $shopConfigValue = $shopConfig ? $shopConfig->value : config('shopconfig.default.' . $key);
    if ($key == 'shop_cover_url') {
        $shopConfigValue = admin_url($shopConfigValue);
    }

    return $shopConfigValue;
}

/**
 * 补全资源路径
 *
 * @param $url
 * @return mixed
 */
function admin_url($url)
{
    return $url ? (url()->isValidUrl($url) ? $url : Storage::disk('admin')->url($url)) : $url;
}