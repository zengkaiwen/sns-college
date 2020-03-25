<?php
/**
 * Created by PhpStorm.
 * User: macbook
 * Date: 2020-02-16
 * Time: 16:27
 */

namespace api\sns\service;


use api\sns\model\SnsTopicFollowModel;
use api\sns\model\SnsTopicModel;
use think\db\Query;

class TopicService
{
    public function topicList($params) {
        $topic  =   new SnsTopicModel();
        $findQuery =   $topic
            ->where('type', 1)
            ->where(function (Query $query) use ($params) {
                if (!empty($params['keyword'])) { // 模糊查询
                    $query->where('title', 'like', "%{$params['keyword']}%");
                }
                if (!empty($params['create_uid'])) { // 获取某个用户的
                    $query->where('create_uid', $params['create_uid']);
                }
            });
        if (!empty($params['page'])) {
            $findQuery->page($params['page']);
        }
        if (!empty($params['order'])) { // 根据话题的关注度排序
            $findQuery->order('follow_count', 'desc');
        } else {
            $findQuery->order('create_at', 'desc');
        }
        $result = $findQuery->select();
        return $result;
    }

    public function followList($params) {
        $page   =   empty($params['page']) ? '1' : $params['page'];
        $topicFollow = new SnsTopicFollowModel();
        $findQuery = $topicFollow
            ->where('topic_id', $page['id'])
            ->where(function (Query $query) use ($params) {
                if (!empty($params['start_time'] && !empty($params['end_time']))) {
                    $query->whereBetweenTime('create_at', $params['start_time'], $params['end_time']);
                }
            })
            ->page($page);
        if (!empty($params['rank'])) {
            $findQuery->order('post_count', 'desc');
        } else {
            $findQuery->order('create_at', 'desc');
        }
        $result = $findQuery->select();
        return $result;
    }
}