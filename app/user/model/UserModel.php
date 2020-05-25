<?php
/**
 * Created by PhpStorm.
 * User: Kevin
 * Date: 2020/4/30
 * Time: 10:57
 */

namespace app\user\model;


use think\Model;

class UserModel extends Model
{
    public function getDegreeAttr($value) {
        $types = [
            0 => '未知',
            1 => '专科',
            2 => '本科',
            3 => '硕士',
            4 => '博士'
        ];
        return $types[$value];
    }

    public function getUserTypeAttr($value) {
        $types = [
            1 => '管理员',
            2 => '普通用户',
            3 => '认证学生',
            4 => '官方',
            5 => '特邀机构'
        ];
        return $types[$value];
    }

    public function getAuthImageAttr($value) {
        return 'http://sns-file.zeuswk.com/'.$value;
    }
}