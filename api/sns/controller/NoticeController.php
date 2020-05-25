<?php
/**
 * Created by PhpStorm.
 * User: macbook
 * Date: 2020-02-15
 * Time: 22:19
 */

namespace api\sns\controller;

use api\sns\model\SnsNoticeCommentModel;
use api\sns\model\SnsNoticeFansModel;
use api\sns\model\SnsNoticeOfficialModel;
use api\sns\model\SnsNoticeReplyModel;
use cmf\controller\RestUserBaseController;

class NoticeController extends RestUserBaseController
{
    // 获取官方全员通知内容
    public function officialAll() {

    }

    // 获取官方单独通知
    public function official() {
        if ($this->request->isPost()) {
            $params = $this->request->post();
            $page = empty($params['page']) ? 1 : $params['page'];

            if (!empty($params['id'])) { // 如果前端有传Id那么就是已读该消息
                $model = SnsNoticeOfficialModel::where([
                    'id'    =>  $params['id'],
                    'is_read'   =>  0
                ])->find();
                if ($model) {
                    $model->is_read = 1;
                    $model->save();
                    $this->success('已读', true);
                }
            }

            // 获取所有消息
            $result = SnsNoticeOfficialModel::where('notice_uid', $this->userId)
                ->page($page)
                ->order('create_at', 'desc')
                ->select();
            $this->success('成功', $result);

        }
    }

    // 获取被关注通知
    public function beFollow() {
        if ($this->request->isPost()) {
            $params = $this->request->post();
            $page = empty($params['page']) ? 1 : $params['page'];

            if (!empty($params['id'])) {
                $model = SnsNoticeFansModel::where([
                    'id'    =>  $params['id'],
                    'is_read'   =>  0
                ])->find();
                if ($model) {
                    $model->is_read = 1;
                    $model->save();
                    $this->success('已读', true);
                }
            }

            $result = SnsNoticeFansModel::where('notice_uid', $this->userId)
                ->page($page)
                ->order('create_at', 'desc')
                ->select();
            $this->success('成功', $result);

        }
    }

    // 获取被评论通知
    public function beComment() {
        if ($this->request->isPost()) {
            $params = $this->request->post();
            $page = empty($params['page']) ? 1 : $params['page'];

            if (!empty($params['id'])) {
                $model = SnsNoticeCommentModel::where([
                    'id'    =>  $params['id'],
                    'is_read'   =>  0
                ])->find();
                if ($model) {
                    $model->is_read = 1;
                    $model->save();
                    $this->success('已读', true);
                }
            }

            $result = SnsNoticeCommentModel::where('notice_uid', $this->userId)
                ->with(['comment', 'fromUser'])
                ->page($page)
                ->order('create_at', 'desc')
                ->select();
            $this->success('成功', $result);

        }
    }

    // 获取被回复通知
    public function beReply() {
        if ($this->request->isPost()) {
            $params = $this->request->post();
            $page = empty($params['page']) ? 1 : $params['page'];

            if (!empty($params['id'])) {
                $model = SnsNoticeReplyModel::where([
                    'id'    =>  $params['id'],
                    'is_read'   =>  0
                ])->find();
                if ($model) {
                    $model->is_read = 1;
                    $model->save();
                    $this->success('已读', true);
                }
            }

            $result = SnsNoticeReplyModel::where('notice_uid', $this->userId)
                ->with(['reply', 'fromUser'])
                ->page($page)
                ->order('create_at', 'desc')
                ->select();
            $this->success('成功', $result);

        }
    }

    public function count() {
        if ($this->request->isGet()) {

            $official_count = SnsNoticeOfficialModel::where([
                    'notice_uid'    =>  $this->userId,
                    'is_read'       =>  0
                ])->count();

            $follow_count = SnsNoticeFansModel::where([
                'notice_uid'    =>  $this->userId,
                'is_read'       =>  0
            ])->count();

            $comment_count = SnsNoticeCommentModel::where([
                'notice_uid'    =>  $this->userId,
                'is_read'       =>  0
            ])->count();

            $reply_count = SnsNoticeReplyModel::where([
                'notice_uid'    =>  $this->userId,
                'is_read'       =>  0
            ])->count();

            $data = [
                'official'  =>  $official_count,
                'follow'    =>  $follow_count,
                'comment'   =>  $comment_count,
                'reply'     =>  $reply_count,
            ];

            $this->success('完成', $data);
        }
    }
}