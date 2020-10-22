<?php

namespace App\Http\Controllers;

use App\Enums\CustomCode;
use App\Exceptions\CustomException;
use App\Models\Authenticated;
use App\Models\WechatUser;
use Illuminate\Http\Request;
use Overtrue\LaravelWeChat\Facade as EasyWechat;

class AuthenticatedsController extends Controller
{
    /**
     * showdoc
     * @catalog 接口
     * @title 新增小程序认证者
     * @description 暂无
     * @method  post
     * @url  /authenticated/mini-program
     * @param code           必选 string 小程序客户端code
     * @param encrypted_data 必选 string 小程序客户端encrypted_data
     * @param iv             必选 string 小程序客户端iv
     * @param raw_data       必选 string 小程序客户端raw_data
     * @param signature      必选 string 小程序客户端signature
     * @return {}
     * @return_param token              string 登陆凭证token
     * @return_param authenticated_user array  微信用户认证者&nbsp;[参考](http://showdoc.deepack.top/web/#/5?page_id=82)
     * @remark 暂无
     * @number 1
     */
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

        //  数据签名校验
        if (sha1($request->raw_data . $miniProgramLoginResult['session_key']) != $request->signature) {
            throw new CustomException('微信登陆失败');
        }

        $wechatUser = WechatUser::firstOrCreate(
            [
                'openid_mini_program' => $miniProgramLoginResult['openid'],
                'admin_user_id' => store()->admin_user_id,
            ],
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

        $ttl = config('authenticated-token.ttl');

        $authenticated = Authenticated::updateOrCreate(
            [
                'authenticate_type' => 'mini_program',
                'authenticated_user_model' => WechatUser::class,
                'authenticated_user_id' => $wechatUser->id,
                'admin_user_id' => store()->admin_user_id,
            ],
            [
                'token' => Authenticated::createToken(),
                'ttl' => $ttl,
                'expired_at' => $ttl ? now()->addSeconds($ttl) : null,
            ]
        );

        return $this->res(CustomCode::Success, [
            'token' => $authenticated->token,
            'authenticated_user' => $wechatUser,
        ]);
    }
}
