<?php
/**
 * Created by PhpStorm.
 * User: macbook
 * Date: 2020-02-16
 * Time: 11:48
 */

namespace api\sns\controller;


use cmf\controller\RestBaseController;

class UPYunController extends RestBaseController
{
    private $operator = 'snsweapp';
    private $password = 'lahbfqgP1K8rsBBoxuF4ZJKpJAMSZZts';
    private $method = 'GET';
    private $uri = '/v1/apps/';


    public function token() {
        return upyun_sign(
            $this->operator,
            md5($this->password),
            $this->method,
            $this->uri,
            gmdate('D, d M Y H:i:s \G\M\T')
        );
    }
}