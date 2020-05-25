<?php
/**
 * Created by PhpStorm.
 * User: Kevin
 * Date: 2020/4/30
 * Time: 10:37
 */

namespace app\user\controller;


use app\user\model\SnsNoticeOfficialModel;
use app\user\model\UserModel;
use cmf\controller\AdminBaseController;

class AdminStudentController extends AdminBaseController
{
    public function index() {
        $list = UserModel::where('is_auth', 1)
            ->order('create_time', 'desc')
            ->paginate(10);
        $this->assign('list', $list);
        $this->assign('page', $list->render());
        return $this->fetch();
    }

    public function resolve() {
        $id = input('param.id', 0, 'intval');
        if ($id) {
            $user = UserModel::where('id', $id)->find();
            $user->is_auth = 2;
            $user->user_type = 3;
            $user->save();

            SnsNoticeOfficialModel::create([
                'notice_uid' => $id,
                'official_uid' => cmf_get_current_admin_id(),
                'content' => '学生认证成功'
            ]);

            $this->success('认证成功');
        } else {
            $this->error('数据传入失败');
        }
    }

    // 驳回
    public function reject() {
        $id = input('param.id', 0, 'intval');
        $content = $this->request->param('content');
        if ($id) {
            $user = UserModel::where('id', $id)->find();
            $user->is_auth = 0;
            $user->save();

            $notice = SnsNoticeOfficialModel::create([
                'notice_uid' => $id,
                'official_uid' => cmf_get_current_admin_id(),
                'content' => '学生认证失败，失败原因：'.$content
            ]);

            $this->success('驳回成功', 'adminStudent/index');
        } else {
            $this->error('数据传入失败');
        }
    }
}