<?php

namespace App\Http\Controllers;

use App\Enums\CustomCode;
use App\Models\Category;

class CategoriesController extends Controller
{
    /**
     * showdoc
     * @catalog 接口
     * @title 分类列表
     * @description 暂无
     * @method  get
     * @url  /categories
     * @param
     * @return {}
     * @return_param categories             array 分类&nbsp;[参考](http://showdoc.deepack.top/web/#/5?page_id=77)
     * @return_param categories.\*.products array 商品&nbsp;[参考](http://showdoc.deepack.top/web/#/5?page_id=78)
     * @remark 暂无
     * @number 1
     */
    public function index()
    {
        $categories = Category::query()
            ->with([
                'products' => function ($query) {
                    return $query->where('is_on', 1)->orderByDesc('sort');
                },
            ])
            ->orderByDesc('sort')
            ->get();

        return $this->res(CustomCode::Success, [
            'categories' => $categories,
        ]);
    }
}
