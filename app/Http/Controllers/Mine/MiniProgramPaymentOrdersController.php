<?php

namespace App\Http\Controllers\Mine;

use App\Exceptions\CustomException;
use App\Http\Controllers\Controller;
use App\Models\MiniProgramPaymentOrder;
use App\Models\Order;
use App\Models\Product;
use App\Rules\ValidPhone;
use Illuminate\Http\Request;
use Overtrue\LaravelWeChat\Facade as EasyWechat;

class MiniProgramPaymentOrdersController extends Controller
{
    public function store(Request $request)
    {
        $validatedData = $request->validate([
            'carts'              => 'required|array',
            'carts.*.product_id' => 'required|integer',
            'carts.*.number'     => 'required|integer|min:1',
            'total_price'        => 'required|numeric',

            'phone'              => ['required', new ValidPhone()],
            'arrived_time'       => 'required',
            'remark'             => '',
        ]);

        $productsDictionary = Product::query()
            ->whereIn('id', array_column($request->carts, 'product_id'))
            ->where('admin_user_id', store()->admin_user_id)
            ->get()
            ->keyBy('id')
            ->toArray();

        foreach ($validatedData['carts'] as &$cart) {
            if (!isset($productsDictionary[$cart['product_id']])) {
                throw new CustomException('商品不存在');
            }

            $product = $productsDictionary[$cart['product_id']];
            if (!$product['is_on']) {
                throw new CustomException('商品未上架');
            }
            if ($cart['number'] > $product['stock']) {
                throw new CustomException('商品库存不足');
            }

            $cart['total_price'] = ($product['sale_price'] + $product['packing_price']) * $cart['number'];
            $cart['product'] = $product;
        }

        $totalPrice = array_sum(array_column($validatedData['carts'], 'total_price'));
        if ($totalPrice != $request->total_price) {
            throw new CustomException('商品总价有误');
        }

        $minimumPrice = shop_config('minimum_price');
        if ($minimumPrice && $totalPrice < $minimumPrice) {
            throw new CustomException('商品总价不能低于最低消费');
        }

        $validatedData['no'] = Order::createNo();
        $validatedData['total_price'] = $totalPrice;
        $validatedData['wechat_user_id'] = me()->id;
        $validatedData['admin_user_id'] = store()->admin_user_id;

        $order = Order::create($validatedData);

        $result = EasyWechat::payment()->order->unify([
            'body'         => '聪航餐饮店-烧鹅店',
            'out_trade_no' => $order->no,
            'total_fee'    => $totalPrice,
            'notify_url'   => route('mini-program-payment-order-notifies.store'),
            'trade_type'   => 'JSAPI',
            'openid'       => me()->openid_mini_program,
            'attach'       => json_encode(['store_key' => store()->key]),
        ]);

        $isSuccess = $result['return_code'] == 'SUCCESS' && $result['result_code'] == 'SUCCESS' ? 1 : 0;
        $failMessage = $isSuccess ? null :
            ($result['err_code_des'] ?? ($result['return_msg'] && $result['return_msg'] != 'OK' ? $result['return_msg'] : null));

        MiniProgramPaymentOrder::create([
            'admin_user_id'  => store()->admin_user_id,
            'wechat_user_id' => me()->id,
            'openid'         => me()->openid_mini_program,
            'is_success'     => $isSuccess,
            'fail_code'      => $result['err_code'] ?? null,
            'fail_message'   => $failMessage,
            'prepay_id'      => $result['prepay_id'] ?? null,
            'result'         => $result,
        ]);

        if (!$isSuccess) {
            throw new CustomException('创建支付失败');
        }

        return $this->res();
    }
}
