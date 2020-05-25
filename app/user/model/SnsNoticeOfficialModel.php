<?php
/**
 * Created by PhpStorm.
 * User: Kevin
 * Date: 2020/4/30
 * Time: 14:20
 */

namespace app\user\model;


use think\Model;

class SnsNoticeOfficialModel extends Model
{
    protected $autoWriteTimestamp = true;
    protected $createTime = 'create_at';
    protected $updateTime = 'update_at';

    public function getCreateAtAttr($value) {
        return date( "Y-m-d H:i",$value);
    }
}