<?php

namespace App\Http\Controllers\Mine;

use App\Enums\CustomCode;
use App\Exceptions\CustomException;
use App\Http\Controllers\Controller;
use App\Models\Order;

class OrdersController extends Controller
{
    public function index()
    {
        $orders = Order::query()
            ->where('admin_user_id', store()->admin_user_id)
            ->where('wechat_user_id', me()->id)
            ->where('is_paid', 1)
            ->get();

        return $this->res(CustomCode::Success, [
            'orders' => $orders,
        ]);
    }

    public function show(Order $order)
    {
        if (
            $order->admin_user_id != store()->admin_user_id
            || $order->wechat_user_id != me()->id
            || $order->is_paid == 0
        ) {
            throw new CustomException('订单不存在');
        }

        return $this->res(CustomCode::Success, [
            'order' => $order,
        ]);
    }
}
