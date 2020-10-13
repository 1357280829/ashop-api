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
 * 获取单个或所有商城配置
 *
 * @param null $key
 * @return \Illuminate\Config\Repository|mixed
 */
function shop_config($key = null)
{
    $urlKeys = config('shopconfig.url_keys');

    if ($key) {
        $shopConfigItem = DB::table('shop_config')->where('key', $key)->first();

        $shopConfigValue = $shopConfigItem ? $shopConfigItem->value : config('shopconfig.default.' . $key);
        if (in_array($key, $urlKeys)) {
            $shopConfigValue = admin_url($shopConfigValue);
        }

        return $shopConfigValue;
    }

    $shopConfig = config('shopconfig.default');

    $currentShopConfig = DB::table('shop_config')
        ->whereIn('key', config('shopconfig.keys'))
        ->pluck('value', 'key')
        ->toArray();

    array_walk($shopConfig, function (&$value, $key) use ($currentShopConfig, $urlKeys) {
        $value = $currentShopConfig[$key] ?? $value;
        if (in_array($key, $urlKeys)) {
            $value = admin_url($value);
        }
    });

    return $shopConfig;
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