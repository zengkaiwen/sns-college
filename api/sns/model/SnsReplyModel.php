<?php
/**
 * Created by PhpStorm.
 * User: macbook
 * Date: 2020-02-16
 * Time: 10:59
 */

namespace api\sns\model;


use think\Model;
use think\model\concern\SoftDelete;

class SnsReplyModel extends Model
{
    use SoftDelete;
    protected $deleteTime = 'delete_at';
    protected $defaultSoftDelete = 0;

    protected $autoWriteTimestamp = true;
    protected $createTime = 'create_at';
    protected $updateTime = 'update_at';

    public function user() {
        return $this->hasOne('UserModel', 'id', 'from_uid');
    }

    public function toUser() {
        return $this->hasOne('UserModel', 'id', 'to_uid');
    }

    public function likes() {
        return $this->hasMany('SnsLikesReplyModel', 'reply_id', 'id');
    }

    public function getCreateAtAttr($value) {
        return uc_time_ago($value);
    }
}