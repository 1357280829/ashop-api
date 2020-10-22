<?php

namespace App\Console\Commands;

use App\Models\Authenticated;
use App\Models\WechatUser;
use Illuminate\Console\Command;

class GenerateToken extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'make:token {adminUserId=2} {userId=1} {userModel=wechat}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $wechatUser = WechatUser::find($this->argument('userId'));
        if (!$wechatUser) {
            $this->info('用户不存在');
            exit();
        }

        $ttl = 86400 * 365;

        $authenticated = Authenticated::updateOrCreate(
            [
                'authenticate_type' => 'mini_program',
                'authenticated_user_model' => WechatUser::class,
                'authenticated_user_id' => $wechatUser->id,
                'admin_user_id' => $this->argument('adminUserId'),
            ],
            [
                'token' => Authenticated::createToken(),
                'ttl' => $ttl,
                'expired_at' => $ttl ? now()->addSeconds($ttl) : null,
            ]
        );

        $this->info($authenticated->token);
        $this->info('token已创建');
    }
}
