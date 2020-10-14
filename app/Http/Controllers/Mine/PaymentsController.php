<?php

namespace App\Http\Controllers\Mine;

use App\Exceptions\CustomException;
use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\Product;
use App\Rules\ValidPhone;
use Illuminate\Http\Request;

class PaymentsController extends Controller
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

        $validatedData['no'] = Order::createNo();
        $validatedData['taking_code'] = Order::createTakingCode();
        $validatedData['total_price'] = $totalPrice;
        $validatedData['wechat_user_id'] = me()->id;
        $validatedData['admin_user_id'] = store()->admin_user_id;

        $order = Order::create($validatedData);
    }
}
