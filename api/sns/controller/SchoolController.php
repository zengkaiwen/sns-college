<?php
/**
 * Created by PhpStorm.
 * User: macbook
 * Date: 2020-02-24
 * Time: 16:49
 */

namespace api\sns\controller;


use cmf\controller\RestBaseController;
use think\Db;

class SchoolController extends RestBaseController
{
    // 获取四川各高校名称
    public function getSichuan() {
        $result = Db::table('cmf_school')
            ->where('cid', 15)
            ->select();
        $this->success('成功', $result);
    }
}