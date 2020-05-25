<?php
/**
 * Created by PhpStorm.
 * User: macbook
 * Date: 2020-02-19
 * Time: 14:06
 */

namespace api\sns\service;


use api\sns\model\SnsCommentModel;
use think\db\Query;

class CommentService
{
    public function commentList($params) {
        $page = empty($params['page']) ? 1 : $params['page'];

        $comment = new SnsCommentModel();
        $findQuery = $comment->where('check_result', 1)
            ->with(['user', 'images'])
            ->withCount(['reply', 'likes'])
            ->where(function (Query $query) use ($params) {
                if (empty($params['curr_user'])) { // 如果不是当前用户
                    $query->where('is_secret', 0);
                }
                if (!empty($params['type'])) {
                    $query->where('type', $params['type']);
                }
                if (!empty($params['post_id'])) { // 获取某个帖子的所有评论
                    $query->where('post_id', $params['post_id']);
                }
                if (!empty($params['from_uid'])) { // 获取某个用户的所有评论
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
        $comment = new SnsCommentModel();
        $res = $comment->with(['user', 'images'])
            ->withCount(['reply', 'likes'])
            ->where([
                'check_result'  => 1,
                'id'            => $id,
            ])
            ->find();
        return $res;
    }
}