<?php

namespace App\Http\Controllers;

use App\Enums\CustomCode;
use App\Exceptions\CustomException;
use App\Handlers\TokenHandler;
use App\Models\WechatUser;
use Illuminate\Http\Request;
use Overtrue\LaravelWeChat\Facade as EasyWechat;

class AuthenticatedsController extends Controller
{
    //  TODO:新增小程序认证者
    public function miniProgramsStore(Request $request)
    {
        $request->validate([
            'code' => 'required',
            'encrypted_data' => 'required',
            'iv' => 'required',
            'raw_data' => 'required',
            'signature' => 'required',
        ]);

        $miniProgram = EasyWechat::miniProgram();

        //  获取用户 openid 及 session_key
        $miniProgramLoginResult = $miniProgram->auth->session($request->code);
        if (isset($miniProgramLoginResult['errcode']) && $miniProgramLoginResult['errcode'] != 0) {
            throw new CustomException('微信登陆失败，错误码: ' . $miniProgramLoginResult['errcode'] . '，错误信息: ' . $miniProgramLoginResult['errmsg']);
        }

        //  获取解密数据
        $decryptedData = $miniProgram->encryptor->decryptData($miniProgramLoginResult['session_key'], $request->iv, $request->encrypted_data);

        //  TODO:数据签名校验
        //  ......

        $wechatUser = WechatUser::firstOrCreate(
            ['openid_mini_program' => $miniProgramLoginResult['openid']],
            [
                'nickname' => $decryptedData['nickName'],
                'avatar_url' => $decryptedData['avatarUrl'],
                'gender' => $decryptedData['gender'],
                'country' => $decryptedData['country'],
                'province' => $decryptedData['province'],
                'city' => $decryptedData['city'],
                'unionid' => $decryptedData['unionId'] ?? null,
            ]
        );

        $token = TokenHandler::create($wechatUser->id, 'mini_program');

        return $this->res(CustomCode::Success, [
            'token' => $token,
            'authenticated' => $wechatUser,
        ]);
    }
}
