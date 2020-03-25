<?php
/**
 * Created by PhpStorm.
 * User: macbook
 * Date: 2020-02-16
 * Time: 16:32
 */

namespace api\sns\validate;


use think\Validate;

class IdMustValidate extends Validate
{
    protected $rule = [
        'id'    =>  'require',
    ];

    protected $message = [
        'id.require'    =>  'id参数不存在'
    ];
}