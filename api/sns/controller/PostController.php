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
use api\sns\service\WechatService;
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

            if ($this->user['is_auth'] !== 2) {
                $this->error('请先认证身份之后再操作');
            }

            $validate = new PostFieldValidate();
            if (!$validate->check($params)) {
                $this->error($validate->getError());
            }


            // TODO 内容审核，验证文字和图片是否符合标准
            $wechat = new WechatService();
            $result = $wechat->app->content_security->checkText($params['content']);
            $check_result = 0;
            if ($result['errcode'] == 0) {
                $check_result = 1;
            }


            // TODO END
            $from_uid = $this->userId;
            $post = SnsPostModel::create([
                'content'   =>  $params['content'],
                'type'      =>  0, // 普通帖子
                'location'  =>  empty($params['location']) ? '未知' : $params['location'],
                'topic_id'  =>  $params['topic_id'],
                'from_uid'  =>  $from_uid,
                'check_result' => $check_result
            ]);


            $imgModel = new SnsImagesPostModel();
            $list = [];
            foreach ($params['images'] as $image) {
                $list[] = [
                    'from'  =>  1,
                    'url'   => $image['path'],
                    'index' => $image['index'],
                    'from_id'   =>  $post->id,
                ];
            }
            try {
                $imgModel->saveAll($list);
            } catch (\Exception $e) {
                $this->error('系统错误：图片保存失败');
            }

            if ($check_result) {
                $this->success('新建帖子成功', $post);
            } else {
                $this->error('帖子包含敏感信息', $post);
            }
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

            $findModel = SnsLikesPostModel::where([
                'from_uid'   =>  $this->userId,
                'post_id'    =>  $params['id']
            ])->find();
            if (!$findModel) {
                $data = SnsLikesPostModel::create([
                    'from_uid'   =>  $this->userId,
                    'post_id'    =>  $params['id']
                ]);
                $this->success('点赞成功', $data);
            } else {
                $this->error('已经点过赞了');
            }
        }
    }

}