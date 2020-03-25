<?php
/**
 * Created by PhpStorm.
 * User: macbook
 * Date: 2020-02-25
 * Time: 11:38
 */

namespace api\sns\service;

use EasyWeChat\Factory;

class WechatService
{
    public $app = null;
    private $config = [
        'app_id' => 'wxde392ff9614d647d',
        'secret' => '7a35f873b7d64be6613ff138644a59ab',
        'response_type' => 'array',
        'log' => [
            'level' => 'debug',
            'file' => __DIR__.'/../wechat.log',
        ],
    ];

    public function __construct()
    {
        $this->app = Factory::miniProgram($this->config);
    }
}