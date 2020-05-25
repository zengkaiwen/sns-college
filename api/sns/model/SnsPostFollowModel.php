<?php
/**
 * Created by PhpStorm.
 * User: macbook
 * Date: 2020-02-18
 * Time: 17:12
 */

namespace api\sns\model;


use think\Model;

class SnsPostFollowModel extends Model
{

    protected $autoWriteTimestamp = true;
    protected $createTime = 'create_at';
    protected $updateTime = 'update_at';

    public function user() {
        return $this->hasOne('UserModel', 'id', 'user_id');
    }

    public function followed() {
        return $this->hasOne('UserModel', 'id', 'followed_uid');
    }
}