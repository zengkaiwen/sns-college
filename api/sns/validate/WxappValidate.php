<?php
/**
 * Created by PhpStorm.
 * User: macbook
 * Date: 2020-02-20
 * Time: 21:32
 */

namespace api\sns\validate;


use think\Validate;

class WxappValidate extends Validate
{
    protected $rule = [
        'code'           => 'require',
        'encrypted_data' => 'require',
        'iv'             => 'require',
        'raw_data'       => 'require',
        'signature'      => 'require',
    ];

    protected $message = [
        'code.require'           => '缺少参数code!',
        'encrypted_data.require' => '缺少参数encrypted_data!',
        'iv.require'             => '缺少参数iv!',
        'raw_data.require'       => '缺少参数raw_data!',
        'signature.require'      => '缺少参数signature!',
    ];
}