<?php
/**
 * Created by PhpStorm.
 * User: macbook
 * Date: 2020-02-19
 * Time: 21:34
 */

namespace api\sns\controller;


use api\sns\model\SnsTopicModel;
use api\sns\model\UserModel;
use api\sns\service\CommentService;
use api\sns\service\PostService;
use api\sns\service\ReplyService;
use api\sns\service\TopicService;
use api\sns\validate\IdMustValidate;
use cmf\controller\RestBaseController;

class PublicController extends RestBaseController
{
    // 话题相关
    /**
     * GET  获取话题列表，默认按时间排序
     *      或 搜索，搜索含title参数，
     *      或搜索某个用户下的所有话题，带create_uid参数
     *      或 排行榜 带参数rank: true
     */
    public function topicList() {
        if ($this->request->isGet()) {
            $params = $this->request->get();

            $topicService = new TopicService();
            $result = $topicService->topicList($params);

            $this->success('成功', $result);
        }
    }

    /**
     * GET  获取话题详情，id
     */
    public function topicDetail() {
        if ($this->request->isGet()) {
            $params = $this->request->get();

            $validate = new IdMustValidate();
            if (!$validate->check($params)) {
                $this->error($validate->getError());
            }

            $findTopic = SnsTopicModel::withCount(['follow'])->where([
                'id' => $params['id']
            ])->find();
            $this->success('获取成功', $findTopic);
        }
    }

    /**
     * GET  获取某话题的所有关注者，需要该话题的Id和页数
     *      或 获取某个时间区域的，参数为start_time和end_time
     *      或 获取关注者在该话题下的排行榜
     */
    public function topicFollow() {
        if ($this->request->isGet()) {
            $params = $this->request->get();

            $validate = new IdMustValidate();
            if (!$validate->check($params)) {
                $this->error($validate->getError());
            }

            $topicFollowService = new TopicService();
            $result = $topicFollowService->followList($params);

            $this->success('成功', $result);
        }
    }


    // 帖子相关
    /**
     * GET  获取最新的帖子列表
     *      获取精选的帖子 ?type=1
     *      获取某个话题下的所有帖子 topic_id
     *      获取某个用户的帖子 from_uid
     *      按点赞量排序 order=likes
     *      按评论数排序 order=comment
     */
    public function postList() {

        if ($this->request->isGet()) {
            $params = $this->request->get();

            $postService = new PostService();
            $data = $postService->postList($params);
            if (!empty($data)) {
                $this->success('success', $data);
            }
            $this->error('获取失败');
        }
    }

    /**
     * 根据id获取帖子详情
     */
    public function post() {
        if ($this->request->isGet()) {
            $id = $this->request->get('id');
            $postService = new PostService();
            $data = $postService->getDetailById($id);
            if (!empty($data)) {
                $this->success('success', $data);
            }
            $this->error('fail');
        }
    }



    // 评论相关
    /**
     * GET  获取所有评论
     *      获取神评 type=1
     *      获取某个帖子的所有评论 post_id
     *      获取某人的所有评论   from_uid
     *      按点赞量/时间排序  order=likes/create_at
     */
    public function commentList() {
        if ($this->request->isGet()) {
            $params = $this->request->get();

            $comment = new CommentService();
            $result = $comment->commentList($params);
            $this->success('成功', $result);
        }
    }

    public function comment() {
        if ($this->request->isGet()) {
            $id = $this->request->get('id');
            $commentService = new CommentService();
            $data = $commentService->getDetailById($id);
            if (!empty($data)) {
                $this->success('success', $data);
            }
            $this->error('fail');
        }
    }



    // 回复相关
    /**
     * GET  获取所有回复
     *      获取某个评论的回复 comment_id
     *      获取某个用户的回复 from_uid
     *      获取发给谁的回复  to_uid
     */
    public function replyList() {
        if ($this->request->isGet()) {
            $params = $this->request->get();


            $reply = new ReplyService();
            $result = $reply->replyList($params);
            if ($result) {
                $this->success('成功', $result);
            } else {
                $this->error('失败');
            }

        }
    }

    public function reply() {
        if ($this->request->isGet()) {
            $id = $this->request->get('id');
            $commentService = new ReplyService();
            $data = $commentService->getDetailById($id);
            if (!empty($data)) {
                $this->success('success', $data);
            }
            $this->error('fail');
        }
    }

    // 用户相关
    /**
     * 根据用户id获取用户信息
     */
    public function getUserInfo() {
        if ($this->request->isGet()) {
            $params = $this->request->get();

            $usermodel = new UserModel();
            $user = $usermodel->withCount(['fans', 'follow', 'topic'])->get($params['id']);
            if (!empty($user)) {
                $this->success('success', $user);
            }
            $this->error('查找失败');
        }
    }

}