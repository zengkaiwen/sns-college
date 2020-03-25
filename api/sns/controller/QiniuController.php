<?php
/**
 * Created by PhpStorm.
 * User: macbook
 * Date: 2020-02-22
 * Time: 18:20
 */

namespace api\sns\controller;

use cmf\controller\RestUserBaseController;
use Qiniu\Auth;
use think\App;

class QiniuController extends RestUserBaseController
{
    private $accessKey = 'QZO_KHlAWg_VYVu1wj_P4p1y4NHeO3u97MNvpppe';
    private $secretKey = 'CafUtmJFZxs62xZNE5yHaV04YSOwHlyENueONC74';
    private $auth = null;
    private $bucket = 'sc-college';

    public function __construct(App $app = null)
    {
        parent::__construct($app);
        $this->auth = new Auth($this->accessKey, $this->secretKey);
    }

    public function token() {
        $token = $this->auth->uploadToken($this->bucket);
        $this->success('成功', $token);
    }
}