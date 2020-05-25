<?php
/**
 * Created by PhpStorm.
 * User: macbook
 * Date: 2020-02-15
 * Time: 22:16
 */

namespace api\sns\controller;


use api\sns\model\SnsCommentModel;
use api\sns\model\SnsImagesCommentModel;
use api\sns\model\SnsLikesCommentModel;
use api\sns\model\SnsNoticeCommentModel;
use api\sns\model\SnsPostModel;
use api\sns\service\CommentService;
use api\sns\validate\CommentFieldValidate;
use api\sns\validate\IdMustValidate;
use cmf\controller\RestUserBaseController;
use api\sns\service\WechatService;

class CommentController extends RestUserBaseController
{

    /**
     * 添加评论
     */
    public function add() {
        if ($this->request->isPost()) {
            $params = $this->request->post();

            // 进行权限认证，判断是否具有权限
            if ($this->userType !== 3) { // 不是认证学生
                $this->error('您的身份不是学生，暂时不能发表评论');
            }
            $validate = new CommentFieldValidate();
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

            // 新建评论数据
            $comment = SnsCommentModel::create([
                'content'   =>  $params['content'],
                'type'      =>  0,
                'post_id'  =>  $params['post_id'],
                'from_uid'  =>  $this->userId,
                'to_uid'    =>  $params['to_uid'],
                'is_secret' => $params['is_secret'],
                'check_result' => $check_result,
            ]);

            // 将图片列表进行关联
            $imgModel = new SnsImagesCommentModel();
            $list = [];
            foreach ($params['images'] as $image) {
                $list[] = [
                    'from'  =>  1,
                    'url'   => $image['path'],
                    'index' => $image['index'],
                    'from_id'   =>  $comment->id,
                ];
            }
            $imgModel->saveAll($list);

            // 建立被评论消息通知
            SnsNoticeCommentModel::create([
                'notice_uid'    =>  $params['to_uid'],
                'post_id'       =>  $params['post_id'],
                'comment_id'    =>  $comment->id,
                'from_uid'      =>  $this->userId,
                'content'       =>  $params['content']
            ]);

            if ($check_result) {
                $this->success('评论成功', $comment);
            } else {
                $this->error('评论包含敏感信息', $comment);
            }
        }
    }

    public function getListWithSecret() {
        if ($this->request->isGet()) {
            $params = $this->request->get();

            $post_id = $params['post_id'];
            $post = SnsPostModel::where('id', $post_id)->find();
            if ($this->userId == $post->from_uid) {
                $params['curr_user'] = true;
                $comment = new CommentService();
                $result = $comment->commentList($params);
                $this->success('成功', $result);
            } else {
                $this->error('失败');
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

            $findModel = SnsLikesCommentModel::where([
                'from_uid'   =>  $this->userId,
                'comment_id'    =>  $params['id']
            ])->find();
            if (!$findModel) {
                $data = SnsLikesCommentModel::create([
                    'from_uid'   =>  $this->userId,
                    'comment_id'    =>  $params['id']
                ]);
                $this->success('点赞成功', $data);
            } else {
                $this->error('已经点过赞了');
            }
        }
    }


}