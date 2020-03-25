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

            if (!empty($params['id'])) {
                SnsNoticeOfficialModel::destroy($params['id']);
                $this->success('已读');
            }

            try {
                $result = SnsNoticeOfficialModel::where('notice_uid', $this->userId)
                    ->page($page)
                    ->order('create_at', 'desc')
                    ->select();
                $this->success('成功', $result);
            } catch (\Exception $e) {
                $this->error('系统错误');
            }

        }
    }

    // 获取被关注通知
    public function beFollow() {
        if ($this->request->isPost()) {
            $params = $this->request->post();
            $page = empty($params['page']) ? 1 : $params['page'];

            if (!empty($params['id'])) {
                SnsNoticeFansModel::destroy($params['id']);
                $this->success('已读');
            }

            try {
                $result = SnsNoticeFansModel::where('notice_uid', $this->userId)
                    ->page($page)
                    ->order('create_at', 'desc')
                    ->select();
                $this->success('成功', $result);
            } catch (\Exception $e) {
                $this->error('系统错误');
            }

        }
    }

    // 获取被评论通知
    public function beComment() {
        if ($this->request->isPost()) {
            $params = $this->request->post();
            $page = empty($params['page']) ? 1 : $params['page'];

            if (!empty($params['id'])) {
                SnsNoticeCommentModel::destroy($params['id']);
                $this->success('已读');
            }

            try {
                $result = SnsNoticeCommentModel::where('notice_uid', $this->userId)
                    ->page($page)
                    ->order('create_at', 'desc')
                    ->select();
                $this->success('成功', $result);
            } catch (\Exception $e) {
                $this->error('系统错误');
            }

        }
    }

    // 获取被回复通知
    public function beReply() {
        if ($this->request->isPost()) {
            $params = $this->request->post();
            $page = empty($params['page']) ? 1 : $params['page'];

            if (!empty($params['id'])) {
                SnsNoticeReplyModel::destroy($params['id']);
                $this->success('已读');
            }

            try {
                $result = SnsNoticeReplyModel::where('notice_uid', $this->userId)
                    ->page($page)
                    ->order('create_at', 'desc')
                    ->select();
                $this->success('成功', $result);
            } catch (\Exception $e) {
                $this->error('系统错误');
            }

        }
    }
}