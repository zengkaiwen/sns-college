<?php
/**
 * Created by PhpStorm.
 * User: macbook
 * Date: 2020-02-19
 * Time: 12:20
 */

namespace api\sns\validate;


use think\Validate;

class CommentFieldValidate extends Validate
{
    protected $rule = [
        'content'   =>  'require',
        'post_id'   =>  'require|number',
        'to_uid'  =>  'require|number',
    ];
}