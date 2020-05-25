<?php
/**
 * Created by PhpStorm.
 * User: Kevin
 * Date: 2020/4/30
 * Time: 14:58
 */

namespace app\topic\controller;


use app\topic\model\SnsTopicModel;
use app\topic\model\SnsNoticeOfficialModel;
use cmf\controller\AdminBaseController;

class AdminIndexController extends AdminBaseController
{
    public function index() {
        $list = SnsTopicModel::where('type', 0)
            ->paginate(10);
        $this->assign('list', $list);
        $this->assign('page', $list->render());
        return $this->fetch();
    }

    public function resolve() {
        $id = input('param.id', 0, 'intval');
        if ($id) {
            $topic = SnsTopicModel::where('id', $id)->find();
            $topic->type = 1;
            $topic->save();

            SnsNoticeOfficialModel::create([
                'notice_uid' => $id,
                'official_uid' => cmf_get_current_admin_id(),
                'content' => '你创建的话题【'.$topic->title.'】已通过审核'
            ]);

            $this->success('审核通过');
        } else {
            $this->error('数据传入失败');
        }
    }

    // 驳回
    public function reject() {
        $id = input('param.id', 0, 'intval');
        $content = $this->request->param('content');
        if ($id) {
            $topic = SnsTopicModel::where('id', $id)->find();
            $topic->delete();

            $notice = SnsNoticeOfficialModel::create([
                'notice_uid' => $id,
                'official_uid' => cmf_get_current_admin_id(),
                'content' => '你创建的话题【'.$topic->title.'】未通过审核，原因：'.$content
            ]);

            $this->success('驳回成功', 'adminIndex/index');
        } else {
            $this->error('数据传入失败');
        }
    }
}