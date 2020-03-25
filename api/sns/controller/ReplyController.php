<?php
/**
 * Created by PhpStorm.
 * User: macbook
 * Date: 2020-02-15
 * Time: 22:17
 */

namespace api\sns\controller;

use api\sns\model\SnsCommentModel;
use api\sns\model\SnsImagesReplyModel;
use api\sns\model\SnsNoticeReplyModel;
use api\sns\model\SnsReplyModel;
use api\sns\validate\ReplyFieldValidate;
use cmf\controller\RestUserBaseController;

class ReplyController extends RestUserBaseController
{
    /**
     * 添加回复
     */
    public function add() {
        if ($this->request->isPost()) {
            $params = $this->request->post();

            if ($this->userType !== 3) { // 不是认证学生
                $this->error('您的身份不是学生，暂时不能发表回复');
            }

            $validate = new ReplyFieldValidate();
            if (!$validate->check($params)) {
                $this->error($validate->getError());
            }

            // TODO 内容审核，验证文字和图片是否符合标准



            // TODO END

            $reply = SnsReplyModel::create([
                'content'   =>  $params['content'],
                'type'      =>  0,
                'comment_id'  =>  $params['comment_id'],
                'from_uid'  =>  $this->userId,
                'to_uid'    =>  $params['to_uid']
            ]);


            $imgModel = new SnsImagesReplyModel();
            $list = [];
            foreach ($params['images'] as $image) {
                $list[] = [
                    'from'  =>  1,
                    'url'   => $image,
                    'from_id'   =>  $reply->id,
                ];
            }
            try {
                $imgModel->saveAll($list);

                // 在评论上设置回复的数目
                $comment = SnsCommentModel::where('id', $params['comment_id'])
                    ->find();
                $comment->reply_count = ['inc', 1];
                $comment->save();
                //  添加被回复的消息通知
                SnsNoticeReplyModel::create([
                    'notice_uid'    =>  $params['to_uid'],
                    'comment_id'    =>  $params['comment_id'],
                    'reply_id'      =>  $reply->id,
                    'from_uid'      =>  $this->userId,
                    'content'       =>  $params['content']
                ]);
            } catch (\Exception $e) {
                $this->error('系统错误：图片保存失败');
            }

            $this->success('回复成功', $reply);
        }
    }

}