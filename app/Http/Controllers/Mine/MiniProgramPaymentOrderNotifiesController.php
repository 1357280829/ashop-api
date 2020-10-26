<?php

namespace App\Http\Controllers\Mine;

use App\Http\Controllers\Controller;
use App\Models\MiniProgramPaymentOrderNotify;
use App\Models\Order;
use App\Models\Product;
use App\Models\Store;
use App\Models\WechatUser;
use Illuminate\Support\Facades\DB;
use Overtrue\LaravelWeChat\Facade as EasyWechat;

class MiniProgramPaymentOrderNotifiesController extends Controller
{
    public function store()
    {
        $response = EasyWechat::payment()->handlePaidNotify(function ($message, $fail) {

            $isSuccess = $message['return_code'] == 'SUCCESS' && $message['result_code'] == 'SUCCESS' ? 1 : 0;
            $failMessage = $isSuccess ? null :
                ($message['err_code_des'] ?? (isset($message['return_msg']) && $message['return_msg'] != 'OK' ? $message['return_msg'] : null));

            $store = Store::where('key', json_decode($message['attach'], true)['store_key'])->first();

            $order = Order::where('no', $message['out_trade_no'])->first();

            MiniProgramPaymentOrderNotify::create([
                'admin_user_id'  => $store ? $store->admin_user_id : 0,
                'wechat_user_id' => WechatUser::where('openid_mini_program', $message['openid'])->value('id') ?: 0,
                'order_id'       => $order ? $order->id : 0,
                'is_success'     => $isSuccess,
                'fail_code'      => $result['err_code'] ?? null,
                'fail_message'   => $failMessage,
                'result'         => $message,
            ]);

            if ($isSuccess && $store && $order) {

                $transactionFunction = function () use ($message, $store, $order) {
                    DB::beginTransaction();

                    $order->is_paid = 1;
                    $order->taking_code = Order::createTakingCode($store ? $store->admin_user_id : 0);
                    $order->save();

                    foreach ($order->carts as $cart) {
                        $product = Product::find($cart['product_id']);
                        if (!$product) {
                            DB::rollBack();
                            return false;
                        }
                        $product->stock = ($product->stock - $cart['number'] >= 0) ? $product->stock - $cart['number'] : 0;
                        $product->save();
                    }

                    DB::commit();

                    return true;
                };

                $transactionFunction();
            }

            return true;
        });

        return $response;
    }
}
