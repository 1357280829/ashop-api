<?php

namespace App\Http\Controllers\Mine;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Log;
use Overtrue\LaravelWeChat\Facade as EasyWechat;

class MiniProgramPaymentOrderNotifiesController extends Controller
{
    public function store()
    {
        $response = EasyWechat::payment()->handlePaidNotify(function ($message, $fail) {

            Log::info($message);

            return true;
        });

        return $response;
    }
}
