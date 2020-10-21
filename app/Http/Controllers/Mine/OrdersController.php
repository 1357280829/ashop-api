<?php

namespace App\Http\Controllers\Mine;

use App\Enums\CustomCode;
use App\Exceptions\CustomException;
use App\Http\Controllers\Controller;
use App\Models\Order;

class OrdersController extends Controller
{
    /**
     * showdoc
     * @catalog 接口
     * @title 订单列表
     * @description 暂无
     * @method  get
     * @url  /orders
     * @param
     * @return {}
     * @return_param orders pagination 订单&nbsp;[参考](http://showdoc.deepack.top/web/#/5?page_id=84)
     * @remark 暂无
     * @number 1
     */
    public function index()
    {
        $orders = Order::query()
            ->where('admin_user_id', store()->admin_user_id)
            ->where('wechat_user_id', me()->id)
            ->where('is_paid', 1)
            ->paginate();

        return $this->res(CustomCode::Success, [
            'orders' => $orders,
        ]);
    }

    /**
     * showdoc
     * @catalog 接口
     * @title 订单详情
     * @description 暂无
     * @method  get
     * @url  /orders/{order_id}
     * @param
     * @return {}
     * @return_param order object 订单&nbsp;[参考](http://showdoc.deepack.top/web/#/5?page_id=84)
     * @remark 暂无
     * @number 1
     */
    public function show(Order $order)
    {
        if (
            $order->admin_user_id != store()->admin_user_id
            || $order->wechat_user_id != me()->id
            || !$order->is_paid
        ) {
            throw new CustomException('订单不存在');
        }

        return $this->res(CustomCode::Success, [
            'order' => $order,
        ]);
    }
}
