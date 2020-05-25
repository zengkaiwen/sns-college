<?php
/**
 * Created by PhpStorm.
 * User: macbook
 * Date: 2020-02-17
 * Time: 15:24
 */

namespace api\sns\model;


use think\Model;

class UserModel extends Model
{
    public function fans() {
        return $this->belongsTo('SnsUserFollowModel', 'id', 'user_id');
    }

    public function follow() {
        return $this->belongsTo('SnsUserFollowModel', 'id', 'follow_uid');
    }

    public function topic() {
        return $this->hasMany('SnsTopicFollowModel', 'user_id', 'id');
    }
}