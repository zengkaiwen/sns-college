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
        if ($this->request->isPost()) {
            $params = $this->request->param();
            $action = empty($params['action']) ? 'follow' : $params['action'];

            $validate = new IdMustValidate();
            if (!$validate->check($params)) {
                $this->error($validate->getError());
            }

            $findFollow = SnsTopicFollowModel::where([
                'topic_id'  => $params['id'],
                'user_id'   => $this->userId
            ])->find();

            if ($action == 'follow' && empty($findFollow)) {
                SnsTopicFollowModel::create([
                    'topic_id'   =>  $params['id'],
                    'user_id'    =>  $this->userId
                ]);
                $this->success('关注成功');
            } else if ($action == 'cancel' && !empty($findFollow)) {
                $findFollow->delete();
                $this->success('取消成功');
            }

        }
    }

    /**
     * 关注/取消关注帖子
     */
    public function post() {
        if ($this->request->isPost()) {
            $params = $this->request->param();
            $action = empty($params['action']) ? 'follow' : $params['action'];

            $validate = new IdMustValidate();
            if (!$validate->check($params)) {
                $this->error($validate->getError());
            }

            $findFollow = SnsPostFollowModel::where([
                'post_id'  => $params['id'],
                'user_id'   => $this->userId
            ])->find();

            if ($action == 'follow' && empty($findFollow)) {
                SnsPostFollowModel::create([
                    'post_id'   =>  $params['id'],
                    'user_id'    =>  $this->userId
                ]);
                $this->success('关注成功');
            } else if ($action == 'cancel' && !empty($findFollow)) {
                $findFollow->delete();
                $this->success('取消成功');
            }

        }
    }

    /**
     * 关注/取消关注用户
     */
    public function user() {
        if ($this->request->isPost()) {
            $params = $this->request->param();
            $action = empty($params['action']) ? 'follow' : $params['action'];

            $validate = new IdMustValidate();
            if (!$validate->check($params)) {
                $this->error($validate->getError());
            }

            $findFollow = SnsUserFollowModel::where([
                'follow_uid'  => $params['id'],
                'user_id'   => $this->userId
            ])->find();

            if ($action == 'follow' && empty($findFollow)) {
                SnsUserFollowModel::create([
                    'follow_uid'   =>  $params['id'],
                    'user_id'    =>  $this->userId
                ]);

                // 建立被关注的消息通知
                SnsNoticeFansModel::create([
                    'notice_uid'    =>  $params['id'],
                    'fans_id'       =>  $this->userId,
                    'content'       =>  "{$this->user['user_nickname']}关注了你"
                ]);

                $this->success('关注成功');
            } else if ($action == 'cancel' && !empty($findFollow)) {
                $findFollow->delete();
                $this->success('取消成功');
            }
        }
    }

    /**
     * 获取所有我关注的话题id列表
     */
    public function topicList() {
        if ($this->request->isGet()) {
            $data = SnsTopicFollowModel::where('user_id', $this->userId)
                ->select();
            $this->success('成功', $data);
        }
    }

    /**
     * 获取所有我关注的用户id列表
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    public function userList() {
        if ($this->request->isGet()) {
            $data = SnsUserFollowModel::where('user_id', $this->userId)
                ->select();
            $this->success('成功', $data);
        }
    }

}