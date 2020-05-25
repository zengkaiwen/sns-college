<?php
/**
 * Created by PhpStorm.
 * User: Kevin
 * Date: 2020/4/30
 * Time: 15:13
 */

namespace app\topic\model;


use think\Model;
use think\model\concern\SoftDelete;

class SnsTopicModel extends Model
{

    use SoftDelete;
    protected $deleteTime = 'delete_at';
    protected $defaultSoftDelete = 0;

    public function getIconPathAttr($value) {
        return 'http://sns-file.zeuswk.com/'.$value;
    }
}