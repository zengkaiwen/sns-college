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
use api\sns\model\SnsLikesReplyModel;
use api\sns\model\SnsNoticeReplyModel;
use api\sns\model\SnsReplyModel;
use api\sns\validate\IdMustValidate;
use api\sns\validate\ReplyFieldValidate;
use cmf\controller\RestUserBaseController;
use api\sns\service\WechatService;

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
            $wechat = new WechatService();
            $result = $wechat->app->content_security->checkText($params['content']);
            $check_result = 0;
            if ($result['errcode'] == 0) {
                $check_result = 1;
            }


            // TODO END

            $reply = SnsReplyModel::create([
                'content'   =>  $params['content'],
                'type'      =>  0,
                'comment_id'  =>  $params['comment_id'],
                'from_uid'  =>  $this->userId,
                'to_uid'    =>  $params['to_uid'],
                'check_result' => $check_result
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
            $imgModel->saveAll($list);


            //  添加被回复的消息通知
            SnsNoticeReplyModel::create([
                'notice_uid'    =>  $params['to_uid'],
                'comment_id'    =>  $params['comment_id'],
                'reply_id'      =>  $reply->id,
                'from_uid'      =>  $this->userId,
                'content'       =>  $params['content']
            ]);

            if ($check_result) {
                $this->success('回复成功', $reply);
            } else {
                $this->error('回复包含敏感信息', $reply);
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

            $findModel = SnsLikesReplyModel::where([
                'from_uid'   =>  $this->userId,
                'reply_id'    =>  $params['id']
            ])->find();
            if (!$findModel) {
                $data = SnsLikesReplyModel::create([
                    'from_uid'   =>  $this->userId,
                    'reply_id'    =>  $params['id']
                ]);
                $this->success('点赞成功', $data);
            } else {
                $this->error('已经点过赞了');
            }
        }
    }

}