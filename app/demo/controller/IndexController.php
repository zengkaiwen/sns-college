<?php
/**
 * Created by PhpStorm.
 * User: Kevin
 * Date: 2020/4/30
 * Time: 11:06
 */

namespace app\demo\controller;


use cmf\controller\HomeBaseController;

class IndexController extends HomeBaseController
{
    public function index() {
        echo cmf_password('123456');
    }
}