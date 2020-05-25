<?php
/**
 * Created by PhpStorm.
 * User: macbook
 * Date: 2020-02-20
 * Time: 21:31
 */

namespace api\sns\controller;


use api\sns\service\WechatService;
use api\sns\validate\WxappValidate;
use cmf\controller\RestBaseController;
use think\Db;

class WxappController extends RestBaseController
{

    private $appId = 'wxde392ff9614d647d';
    private $appSecret = '7a35f873b7d64be6613ff138644a59ab';

    // 所需参数如下
    //  'code'
    //  'encrypted_data'
    //  'iv'
    //  'raw_data'
    //  'signature'
    public function bind() {
        $data = $this->request->param();

        $validate = new WxappValidate();
        if (!$validate->check($data)) {
            $this->error($validate->getError());
        }

        $code   = $data['code'];

        $wechat = new WechatService();
        $response = $wechat->app->auth->session($code);
        if (!empty($response['errcode'])) {
            $this->error('操作失败!');
        }

        $openid     = $response['openid'];
        $sessionKey = $response['session_key'];

        $wxUserData = $wechat->app->encryptor->decryptData($sessionKey, $data['iv'], $data['encrypted_data']);


        $findThirdPartyUser = Db::name("third_party_user")
            ->where('openid', $openid)
            ->where('app_id', $this->appId)
            ->find();

        $currentTime = time();
        $ip          = $this->request->ip(0, true);

        $wxUserData['sessionKey'] = $sessionKey;
        unset($wxUserData['watermark']);

        if ($findThirdPartyUser) {
            $userId = $findThirdPartyUser['user_id'];
            $token  = cmf_generate_user_token($findThirdPartyUser['user_id'], 'wxapp');

            $userData = [
                'last_login_ip'   => $ip,
                'last_login_time' => $currentTime,
                'login_times'     => Db::raw('login_times+1'),
                'more'            => json_encode($wxUserData)
            ];

            if (isset($wxUserData['unionId'])) {
                $userData['union_id'] = $wxUserData['unionId'];
            }

            Db::name("third_party_user")
                ->where('openid', $openid)
                ->where('app_id', $this->appId)
                ->update($userData);

        } else {

            //TODO 使用事务做用户注册
            $userId = Db::name("user")->insertGetId([
                'create_time'     => $currentTime,
                'user_status'     => 1,
                'user_type'       => 2,
                'sex'             => $wxUserData['gender'],
                'user_nickname'   => $wxUserData['nickName'],
                'avatar'          => $wxUserData['avatarUrl'],
                'province'        => $wxUserData['province'],
                'city'            => $wxUserData['city'],
                'last_login_ip'   => $ip,
                'last_login_time' => $currentTime,
            ]);

            Db::name("third_party_user")->insert([
                'openid'          => $openid,
                'user_id'         => $userId,
                'third_party'     => 'wxapp',
                'app_id'          => $this->appId,
                'last_login_ip'   => $ip,
                'union_id'        => isset($wxUserData['unionId']) ? $wxUserData['unionId'] : '',
                'last_login_time' => $currentTime,
                'create_time'     => $currentTime,
                'login_times'     => 1,
                'status'          => 1,
                'more'            => json_encode($wxUserData)
            ]);

            $token = cmf_generate_user_token($userId, 'wxapp');

        }

        $user = Db::name('user')->where('id', $userId)->find();

        $this->success("登录成功!", ['token' => $token, 'user' => $user]);
    }


    // 参数code
    public function login() {
        $params = $this->request->param();

        if (empty($params['code'])) {
            $this->error('code参数不能为空');
        }

        $code     = $params['code'];

        $wechat = new WechatService();
        $response = $wechat->app->auth->session($code);
        if (!empty($response['errcode'])) {
            $this->error('操作失败!');
        }
        $openid     = $response['openid'];

        $findThirdPartyUser = Db::name("third_party_user")
            ->where('openid', $openid)
            ->where('app_id', $this->appId)
            ->find();
        $currentTime = time();
        $ip          = $this->request->ip(0, true);

        if ($findThirdPartyUser) {
            $userId = $findThirdPartyUser['user_id'];
            $token  = cmf_generate_user_token($userId, 'wxapp');

            $userData = [
                'last_login_ip'   => $ip,
                'last_login_time' => $currentTime,
                'login_times'     => Db::raw('login_times+1'),
            ];

            Db::name("third_party_user")
                ->where('openid', $openid)
                ->where('app_id', $this->appId)
                ->update($userData);

            $user = Db::name('user')->where('id', $userId)->find();
            $this->success('登录成功', ['token' => $token, 'user' => $user]);
        } else {
            $this->error('请先绑定微信!');
        }
    }
}