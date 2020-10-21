<?php

namespace App\Http\Controllers\Mine;

use App\Http\Controllers\Controller;
use Overtrue\LaravelWeChat\Facade as EasyWechat;

class MiniProgramPaymentOrderNotifiesController extends Controller
{
    public function store()
    {
        $response = EasyWechat::payment()->handlePaidNotify(function ($message, $fail) {

            //  TODO

            return true;
        });

        return $response;
    }
}
