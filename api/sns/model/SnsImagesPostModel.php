<?php
/**
 * Created by PhpStorm.
 * User: macbook
 * Date: 2020-02-16
 * Time: 10:57
 */

namespace api\sns\model;


use think\Model;

class SnsImagesPostModel extends Model
{
    protected $autoWriteTimestamp = true;
    protected $createTime = 'create_at';
    protected $updateTime = false;

    public function getUrlAttr($value, $data) {
        if ($data['from'] == 1) {
            return 'http://sns-file.zeuswk.com/'.$value;
        }
        return $value;
    }
}