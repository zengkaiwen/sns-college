<?php
/**
 * Created by PhpStorm.
 * User: macbook
 * Date: 2020-02-20
 * Time: 11:04
 */

namespace api\sns\model;


use think\Model;

class SnsNoticeCommentModel extends Model
{

    protected $autoWriteTimestamp = true;
    protected $createTime = 'create_at';
    protected $updateTime = 'update_at';

    public function getCreateAtAttr($value) {
        return date( "Y-m-d H:i",$value);
    }

    public function comment()
    {
        return $this->hasOne('SnsCommentModel', 'id', 'comment_id');
    }

    public function fromUser() {
        return $this->hasOne('UserModel', 'id', 'from_uid');
    }
}