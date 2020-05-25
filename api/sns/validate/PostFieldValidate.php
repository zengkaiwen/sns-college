<?php
/**
 * Created by PhpStorm.
 * User: macbook
 * Date: 2020-02-17
 * Time: 19:53
 */

namespace api\sns\validate;


use think\Validate;

class PostFieldValidate extends Validate
{
    protected $rule = [
        'content'       =>  'require',
        'topic_id'      =>  'require|number',
    ];
}