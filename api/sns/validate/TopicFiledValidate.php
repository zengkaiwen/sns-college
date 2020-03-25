<?php
/**
 * Created by PhpStorm.
 * User: macbook
 * Date: 2020-02-16
 * Time: 16:34
 */

namespace api\sns\validate;


use think\Validate;

class TopicFiledValidate extends Validate
{
    protected $rule = [
        'title'     =>  'require|max:8',
        'des'       =>  'require|max:50',
        'icon_path' =>  'require',
    ];

    protected $scene = [
        'add' => ['title', 'des', 'icon_path']
    ];
}