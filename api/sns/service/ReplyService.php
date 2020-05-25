<?php
/**
 * Created by PhpStorm.
 * User: macbook
 * Date: 2020-02-19
 * Time: 16:11
 */

namespace api\sns\service;


use api\sns\model\SnsReplyModel;
use think\db\Query;

class ReplyService
{
    public function replyList($params) {
        $page = empty($params['page']) ? 1 : $params['page'];

        $comment = new SnsReplyModel();
        $findQuery = $comment->where('check_result', 1)
            ->with(['user', 'toUser'])
            ->withCount(['likes'])
            ->where(function (Query $query) use ($params) {
                if (!empty($params['type'])) {
                    $query->where('type', $params['type']);
                }
                if (!empty($params['comment_id'])) { // 获取某个评论的所有回复
                    $query->where('comment_id', $params['comment_id']);
                }
                if (!empty($params['from_uid'])) { // 获取某个用户的所有回复
                    $query->where('from_uid', $params['from_uid']);
                }
            })
            ->page($page);
        if (!empty($params['order'])) {
            if ($params['order'] == 'likes') {
                $findQuery->order('likes', 'desc');
            } elseif ($params['order'] == 'create_at') {
                $findQuery->order('create_at', 'desc');
            }
        }

        $result = $findQuery->select();
        return $result;
    }

    public function getDetailById($id) {
        $reply = new SnsReplyModel();
        $res = $reply->with(['user', 'toUser'])
            ->withCount(['likes'])
            ->where([
                'check_result'  => 1,
                'id'            => $id,
            ])
            ->find();
        return $res;
    }
}