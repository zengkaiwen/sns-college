<?php
/**
 * Created by PhpStorm.
 * User: macbook
 * Date: 2020-02-19
 * Time: 16:08
 */

namespace api\sns\validate;


use think\Validate;

class ReplyFieldValidate extends Validate
{
    protected $rule = [
        'content'   =>  'require',
        'comment_id'    =>  'require|number',
        'to_uid'    =>  'require|number',
    ];
}