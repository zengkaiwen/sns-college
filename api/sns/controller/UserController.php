<?php
/**
 * Created by PhpStorm.
 * User: macbook
 * Date: 2020-02-21
 * Time: 17:24
 */

namespace api\sns\controller;


use api\sns\model\UserModel;
use cmf\controller\RestUserBaseController;
use think\Validate;

class UserController extends RestUserBaseController
{
    /**
     * 修改用户信息 field和value
     */
    public function change() {
        if ($this->request->isPut()) {
            $params = $this->request->put();

            $validate = new Validate([
               'field'  =>  'require',
               'value'  =>  'require',
            ]);
            if (!$validate->check($params)) {
                $this->error($validate->getError());
            }

            $field = $params['field'];
            $value = $params['value'];

            if ($field === 'hometown') { // 如果是居住地变更的话，value值是json字符串
                $data = json_decode($value, true);
            } else {
                $data = [ $field => $value];
            }

            $result = UserModel::update($data, [
                'id' => $this->userId
            ]);

            if ($result) {
                $this->success('更新成功');
            } else {
                $this->error('更新失败');
            }
        }
    }

    public function applyStudentAuth() {
        if ($this->request->isPost()) {
            $params = $this->request->post();

            $user = UserModel::update([
                'is_auth'   =>  1,
                'user_name' =>  $params['user_name'],
                'auth_image'=>  $params['auth_image']
            ], [
                'id'    =>  $this->userId
            ]);
            if (!empty($user)) {
                $this->success('申请成功', $user);
            } else {
                $this->error('操作失败');
            }
        }
    }
}