<?php
/**
 * Created by PhpStorm.
 * User: macbook
 * Date: 2020-02-16
 * Time: 11:00
 */

namespace api\sns\model;


use think\Model;

class SnsTopicFollowModel extends Model
{
    protected $autoWriteTimestamp = true;
    protected $createTime = 'create_at';
    protected $updateTime = 'update_at';

    public function user() {
        return $this->hasOne('UserModel', 'id', 'user_id');
    }

    public function topic() {
        return $this->hasOne('SnsTopicModel', 'id', 'topic_id');
    }
}