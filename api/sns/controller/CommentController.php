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
use api\sns\model\SnsNoticeCommentModel;
use api\sns\model\SnsPostModel;
use api\sns\validate\CommentFieldValidate;
use cmf\controller\RestUserBaseController;

class CommentController extends RestUserBaseController
{
    private $findCheckedQuery;

    public function initialize()
    {
        $this->findCheckedQuery = SnsCommentModel::where('check_result', 1);
    }

    /**
     * 添加评论
     */
    public function add() {
        if ($this->request->isPost()) {
            $params = $this->request->post();

            if ($this->userType !== 3) { // 不是认证学生
                $this->error('您的身份不是学生，暂时不能发表评论');
            }

            $validate = new CommentFieldValidate();
            if (!$validate->check($params)) {
                $this->error($validate->getError());
            }

            // TODO 内容审核，验证文字和图片是否符合标准



            // TODO END

            $comment = SnsCommentModel::create([
                'content'   =>  $params['content'],
                'type'      =>  0,
                'post_id'  =>  $params['post_id'],
                'from_uid'  =>  $this->userId,
                'to_uid'    =>  $params['to_uid'],
            ]);


            $imgModel = new SnsImagesCommentModel();
            $list = [];
            foreach ($params['images'] as $image) {
                $list[] = [
                    'from'  =>  1,
                    'url'   => $image,
                    'from_id'   =>  $comment->id,
                ];
            }
            try {
                $imgModel->saveAll($list);
                // 被评论的帖子上comment_count+1
                $post = SnsPostModel::where('post_id', $params['post_id'])
                    ->find();
                $post->comment_count = ['inc', 1];
                $post->save();
                // 建立被评论消息通知
                SnsNoticeCommentModel::create([
                    'notice_uid'    =>  $params['to_uid'],
                    'post_id'       =>  $params['post_id'],
                    'comment_id'    =>  $comment->id,
                    'from_uid'      =>  $this->userId,
                    'content'       =>  $params['content']
                ]);
            } catch (\Exception $e) {
                $this->error('系统错误：图片保存失败');
            }

            $this->success('评论成功', $comment);
        }
    }


}