<?php
/**
 * Created by PhpStorm.
 * User: macbook
 * Date: 2020-02-16
 * Time: 10:39
 */

namespace api\sns\model;


use think\Model;
use think\model\concern\SoftDelete;

class SnsCommentModel extends Model
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

    public function images() {
        return $this->hasMany('SnsImagesCommentModel', 'from_id', 'id');
    }

    public function reply() {
        return $this->hasMany('SnsReplyModel', 'comment_id', 'id');
    }

    public function likes() {
        return $this->hasMany('SnsLikesCommentModel', 'comment_id', 'id');
    }

    public function getCreateAtAttr($value) {
        return uc_time_ago($value);
    }
}