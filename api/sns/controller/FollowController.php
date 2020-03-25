<?php
/**
 * Created by PhpStorm.
 * User: macbook
 * Date: 2020-02-15
 * Time: 22:18
 */

namespace api\sns\controller;

use api\sns\model\SnsNoticeFansModel;
use api\sns\model\SnsPostFollowModel;
use api\sns\model\SnsPostModel;
use api\sns\model\SnsTopicFollowModel;
use api\sns\model\SnsTopicModel;
use api\sns\model\SnsUserFollowModel;
use api\sns\validate\IdMustValidate;
use cmf\controller\RestUserBaseController;

class FollowController extends RestUserBaseController
{

    /**
     * PUT  关注/取消关注话题
     */
    public function topic() {
        if ($this->request->isPut()) {
            $params = $this->request->param();
            $action = empty($params['action']) ? 'follow' : $params['action'];

            $validate = new IdMustValidate();
            if (!$validate->check($params)) {
                $this->error($validate->getError());
            }

            try {
                $findFollow = SnsTopicFollowModel::withTrashed()
                    ->where('topic_id', $params['id'])
                    ->where('user_id', $this->userId)
                    ->find();

                if ($action == 'follow') {
                    // 判断是否已经存在关注项了
                    if (!empty($findFollow)) {
                        $findFollow->delete_at = 0;
                        $findFollow->save();
                    } else {
                        SnsTopicFollowModel::create([
                            'topic_id'   =>  $params['id'],
                            'user_id'    =>  $this->userId
                        ]);
                        $topic = SnsTopicModel::get($params['id']);
                        $topic->follow_count = ['inc', 1];
                        $topic->save();
                    }
                    $this->success('关注成功');
                } else if ($action == 'cancel') {
                    if (!empty($findFollow)) {
                        $findFollow->delete();
                        $this->success('取消成功');
                    } else {
                        $this->error('请先关注');
                    }
                }
            } catch (\Exception $e) {
                $this->error('系统错误');
            }

        }
    }

    /**
     * 关注/取消关注帖子
     */
    public function post() {
        if ($this->request->isPut()) {
            $params = $this->request->param();
            $action = empty($params['action']) ? 'follow' : $params['action'];

            $validate = new IdMustValidate();
            if (!$validate->check($params)) {
                $this->error($validate->getError());
            }

            try {
                $findFollow = SnsPostFollowModel::withTrashed()
                    ->where('post_id', $params['id'])
                    ->where('user_id', $this->userId)
                    ->find();

                if ($action == 'follow') {
                    // 判断是否已经存在关注项了
                    if (!empty($findFollow)) {
                        $findFollow->delete_at = 0;
                        $findFollow->save();
                    } else {
                        SnsPostFollowModel::create([
                            'post_id'   =>  $params['id'],
                            'user_id'    =>  $this->userId
                        ]);
                    }
                    $this->success('关注成功');
                } else if ($action == 'cancel') {
                    if (!empty($findFollow)) {
                        $findFollow->delete();
                        $this->success('取消成功');
                    } else {
                        $this->error('请先关注');
                    }
                }
            } catch (\Exception $e) {
                $this->error('系统错误');
            }

        }
    }

    /**
     * 关注/取消关注用户
     */
    public function user() {
        if ($this->request->isPut()) {
            $params = $this->request->param();
            $action = empty($params['action']) ? 'follow' : $params['action'];

            $validate = new IdMustValidate();
            if (!$validate->check($params)) {
                $this->error($validate->getError());
            }

            try {
                $findFollow = SnsUserFollowModel::withTrashed()
                    ->where('follow_id', $params['id'])
                    ->where('user_id', $this->userId)
                    ->find();

                if ($action == 'follow') {
                    // 判断是否已经存在关注项了
                    if (!empty($findFollow)) {
                        $findFollow->delete_at = 0;
                        $findFollow->save();
                    } else {
                        SnsUserFollowModel::create([
                            'follow_id'   =>  $params['id'],
                            'user_id'    =>  $this->userId
                        ]);
                        // 建立被关注的消息通知
                        SnsNoticeFansModel::create([
                            'notice_uid'    =>  $params['id'],
                            'fans_id'       =>  $this->userId,
                            'content'       =>  "{$this->user['user_nickname']}关注了你"
                        ]);
                    }
                    $this->success('关注成功');
                } else if ($action == 'cancel') {
                    if (!empty($findFollow)) {
                        $findFollow->delete();
                        $this->success('取消成功');
                    } else {
                        $this->error('请先关注');
                    }
                }
            } catch (\Exception $e) {
                $this->error('系统错误');
            }

        }
    }

}