<?php
/**
 * Created by PhpStorm.
 * User: macbook
 * Date: 2020-02-15
 * Time: 22:16
 */

namespace api\sns\controller;

use api\sns\model\SnsImagesPostModel;
use api\sns\model\SnsLikesPostModel;
use api\sns\model\SnsPostModel;
use api\sns\model\SnsUserFollowModel;
use api\sns\service\PostService;
use api\sns\validate\IdMustValidate;
use api\sns\validate\PostFieldValidate;
use cmf\controller\RestUserBaseController;

class PostController extends RestUserBaseController
{

    /**
     * POST 新建一个帖子
     */
    public function add() {
        if ($this->request->isPost()) {
            $params = $this->request->post();

            if ($this->userType !== 3) { // 不是认证学生
                $this->error('您的身份不是学生，暂时不能新建帖子');
            }

            $validate = new PostFieldValidate();
            if (!$validate->check($params)) {
                $this->error($validate->getError());
            }


            // TODO 内容审核，验证文字和图片是否符合标准



            // TODO END

            $post = SnsPostModel::create([
                'content'   =>  $params['content'],
                'type'      =>  0,
                'location'  =>  empty($params['location']) ? '未知' : $params['location'],
                'topic_id'  =>  $params['topic_id'],
                'from_uid'  =>  $params['from_uid'],
            ]);


            $imgModel = new SnsImagesPostModel();
            $list = [];
            foreach ($params['images'] as $image) {
                $list[] = [
                    'from'  =>  1,
                    'url'   => $image,
                    'from_id'   =>  $post->id,
                ];
            }
            try {
                $imgModel->saveAll($list);
            } catch (\Exception $e) {
                $this->error('系统错误：图片保存失败');
            }

            $this->success('新建帖子成功', $post);
        }
    }

    /**
     * GET  推荐的帖子
     */
    public function recommend() {

    }

    /**
     * GET  获取我关注的人的帖子
     */
    public function follow() {
        if ($this->request->isGet()) {
            $params = $this->request->get();

            $userId = $this->userId;

            try {
                // 先获取所有我关注的用户id
                $followedUsers = SnsUserFollowModel::where('user_id', $userId)
                    ->select();

                $followeds = [];
                foreach ($followedUsers as $users) {
                    $followeds[] = $users['follow_uid'];
                }

                $params['followeds'] = $followeds;
                $service = new PostService();
                $result = $service->postList($params);
                $this->success('成功', $result);
            } catch (\Exception $e) {
                $this->error('系统错误');
            }
        }
    }

    /**
     * 关注的人的帖子推荐
     */
    public function followRecommend() {

    }


    /**
     * DELETE 删除我的某个帖子
     */
    public function remove() {
        if ($this->request->isDelete()) {
            $param = $this->request->delete();

            $validate = new IdMustValidate();
            if (!$validate->check($param)) {
                $this->error($validate->getError());
            }

            try {
                $post = SnsPostModel::where('from_uid', $this->userId)
                    ->where('id', $param['id'])
                    ->find();
                $result = $post->delete();

                $this->success('删除成功', $result);
            } catch (\Exception $e) {
                $this->error('系统错误');
            }
        }
    }

    /**
     * 点赞
     */
    public function like() {
        if ($this->request->isPost()) {
            $params = $this->request->post();

            $validate = new IdMustValidate();
            if (!$validate->check($params)) {
                $this->error($validate->getError());
            }

            $saveResult = false;
            try {
                $post = SnsPostModel::where('id', $params['id'])
                    ->find();
                $post->likes = ['inc', 1];
                $saveResult = $post->save();
            } catch (\Exception $e) {
                $this->error('系统错误');
            }

            try {
                if ($saveResult) {
                    SnsLikesPostModel::create([
                        'from_uid'   =>  $this->userId,
                        'post_id'    =>  $params['id']
                    ]);
                }
            } catch (\Exception $e) {
                $this->error('已经点过赞了');
            }
        }
    }
}