<?php
/**
 * Created by PhpStorm.
 * User: macbook
 * Date: 2020-02-16
 * Time: 11:00
 */

namespace api\sns\model;


use think\Model;
use think\model\concern\SoftDelete;

class SnsTopicModel extends Model
{
    use SoftDelete;
    protected $deleteTime = 'delete_at';
    protected $defaultSoftDelete = 0;

    protected $autoWriteTimestamp = true;
    protected $createTime = 'create_at';
    protected $updateTime = 'update_at';

    public function follow() {
        return $this->hasMany('SnsTopicFollow', 'topic_id', 'id');
    }
}