<?php
/**
 * Created by PhpStorm.
 * User: macbook
 * Date: 2020-02-20
 * Time: 11:09
 */

namespace api\sns\model;


use think\Model;
use think\model\concern\SoftDelete;

class SnsNoticeReplyModel extends Model
{
    use SoftDelete;
    protected $deleteTime = 'delete_at';
    protected $defaultSoftDelete = 0;

    protected $autoWriteTimestamp = true;
    protected $createTime = 'create_at';
    protected $updateTime = 'update_at';

    public function getCreateAtAttr($value) {
        return date( "Y-m-d H:i",$value);
    }

    public function reply()
    {
        return $this->hasOne('SnsReplyModel', 'id', 'reply_id');
    }

    public function fromUser() {
        return $this->hasOne('UserModel', 'id', 'from_uid');
    }
}