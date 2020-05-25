<?php
/**
 * Created by PhpStorm.
 * User: macbook
 * Date: 2020-02-15
 * Time: 22:17
 */

namespace api\sns\controller;


use api\sns\model\SnsTopicFollowModel;
use api\sns\model\SnsTopicModel;
use api\sns\service\TopicService;
use api\sns\validate\IdMustValidate;
use api\sns\validate\TopicFiledValidate;
use cmf\controller\RestUserBaseController;

class TopicController extends RestUserBaseController
{
    /**
     * POST 添加
     */
    public function add() {
        if ($this->request->isPost()) {
            $params = $this->request->post();

            $validate = new TopicFiledValidate();
            if (!$validate->scene('add')->check($params)) {
                $this->error($validate->getError());
            }

            // 先判断话题名称是否已经存在
            $findTopic = SnsTopicModel::where('title', $params['title'])->find();
            if ($findTopic) {
                $this->error('该话题已存在');
            }

            $topic = SnsTopicModel::create([
                'title' =>  $params['title'],
                'des'   =>  $params['des'],
                'icon_path' =>  $params['icon_path'],
                'follow_name'   =>  $params['follow_name'],
                'create_uid'=>  $this->userId,
                'type'  =>  0, // 未审核
            ]);

            if ($topic) {
                $this->success('添加成功，等待审核', $topic);
            }
            $this->error('添加失败');
        }
    }

    /**
     * GET  获取所有我创建的话题
     */
    public function myTopics() {
        if ($this->request->isGet()) {
            $params = [
                'create_uid' => $this->userId,
            ];

            $topicService = new TopicService();
            $result = $topicService->topicList($params);

            $this->success('成功', $result);
        }
    }

    /**
     * GET  获取所有我关注的话题
     */
    public function follow() {
        if ($this->request->isGet()) {
            $topicFollow = new SnsTopicFollowModel();

            $result = $topicFollow
                ->with(['topic'])
                ->where('user_id', $this->userId)
                ->order('create_at', 'desc')
                ->select();

            $this->success('获取成功', $result);
        }
    }

    /**
     * 获取为我推荐的话题
     */
    public function recommend() {
        // 1、我没有关注的
        // 2、我可能关注的
    }
}