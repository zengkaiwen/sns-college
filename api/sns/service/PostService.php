<?php
/**
 * Created by PhpStorm.
 * User: macbook
 * Date: 2020-02-18
 * Time: 18:02
 */

namespace api\sns\service;


use api\sns\model\SnsPostModel;
use think\db\Query;

class PostService
{
    public function postList($params) {
        $page = empty($params['page']) ? 1 : $params['page'];
        $post = new SnsPostModel();
        $findQuery = $post->where('check_result', 1)
            ->with(['user', 'images', 'topic'])
            ->withCount(['comment', 'likes'])
            ->where(function (Query $query) use ($params) {
                // 获取某个话题下的帖子
                if (!empty($params['topic_id'])) {
                    $query->where('topic_id', $params['topic_id']);
                }
                // 获取某个用户的帖子
                if (!empty($params['from_uid'])) {
                    $query->where('from_uid', $params['from_uid']);
                }
                // 获取精选的帖子
                if (!empty($params['type'])) {
                    $query->where('type', $params['type']);
                }
                // 获取我关注的人的帖子
                if (!empty($params['followeds'])) {
                    $query->whereIn('from_uid', $params['followeds']);
                }
            })
            ->page($page);
        if (!empty($params['order'])) {
            if ($params['order'] == 'likes') {
                $findQuery->order('likes', 'desc');
            } elseif ($params['order'] == 'comment') {
                $findQuery->order('likes', 'desc');
            } elseif ($params['order'] == 'create_at') {
                $findQuery->order('create_at', 'desc');
            }
        }
        $result = $findQuery->select();
        return $result;

    }

    public function getDetailById($id) {
        $post = new SnsPostModel();
        $res = $post->with(['user', 'images', 'topic'])
            ->withCount(['comment', 'likes'])
            ->where([
                'check_result'  => 1,
                'id'            => $id,
            ])
            ->find();
        return $res;
    }
}