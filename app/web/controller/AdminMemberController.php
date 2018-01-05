<?php
/**
 * Created by PhpStorm.
 * User: 胡志伟
 * Date: 2017/12/12
 * Time: 13:28
 */

namespace app\web\controller;

use cmf\controller\AdminBaseController;
use think\Db;


class AdminMemberController extends  AdminBaseController
{
    /**
     * 后台会员用户列表
     * @adminMenu(
     *     'name'   => '会员用户',
     *     'parent' => 'default1',
     *     'display'=> true,
     *     'hasView'=> true,
     *     'order'  => 10000,
     *     'icon'   => '',
     *     'remark' => '会员用户',
     *     'param'  => ''
     * )
     */
    public function index()
    {
        $where   = [];
        $request = input('request.');

        if (!empty($request['uid'])) {
            $where['id'] = intval($request['uid']);
        }
        $keywordComplex = [];
        if (!empty($request['keyword'])) {
            $keyword = $request['keyword'];

            $keywordComplex['user_login|user_nickname|user_email']    = ['like', "%$keyword%"];

        }
        $usersQuery = Db::name('user_vip');

        $list = $usersQuery
            ->field('id,user_login,user_nickname,user_name,user_email,user_QQ,mobile,last_login_ip,last_login_time,avatar,create_time,user_status')
            ->whereOr($keywordComplex)
            ->where($where)
            ->where('user_type',1)
            ->order("create_time DESC")
            ->paginate(10);
        // 获取分页显示
        $page = $list->render();
        $this->assign('list', $list);
        $this->assign('page', $page);
        // 渲染模板输出
        return $this->fetch();
    }

    /**
     * 会员用户拉黑
     * @adminMenu(
     *     'name'   => '会员用户拉黑',
     *     'parent' => 'index',
     *     'display'=> false,
     *     'hasView'=> false,
     *     'order'  => 10000,
     *     'icon'   => '',
     *     'remark' => '会员用户拉黑',
     *     'param'  => ''
     * )
     */
    public function ban()
    {
        $id = input('param.id', 0, 'intval');
        if ($id) {
            $result = Db::name("user_vip")->where(["id" => $id])->setField('user_status', 0);
            if ($result) {
                $this->success("会员拉黑成功！",'');
            } else {
                $this->error('会员拉黑失败,会员不存在,或者是管理员！');
            }
        } else {
            $this->error('数据传入失败！');
        }
    }

    /**
     * 会员用户启用
     * @adminMenu(
     *     'name'   => '会员用户启用',
     *     'parent' => 'index',
     *     'display'=> false,
     *     'hasView'=> false,
     *     'order'  => 10000,
     *     'icon'   => '',
     *     'remark' => '会员用户启用',
     *     'param'  => ''
     * )
     */
    public function cancelBan()
    {
        $id = input('param.id', 0, 'intval');
        if ($id) {
            Db::name("user_vip")->where(["id" => $id,])->setField('user_status', 1);
            $this->success("会员启用成功！", '');
        } else {
            $this->error('数据传入失败！');
        }
    }
}