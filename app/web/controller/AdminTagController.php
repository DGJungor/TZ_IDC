<?php
/**
 * Created by PhpStorm.
 * User: 胡志伟
 * Date: 2017/12/14
 * Time: 15:55
 */
namespace app\web\controller;

use app\web\model\BaoliaoTagModel;
use cmf\controller\AdminBaseController;
use think\Db;

/**
 * Class AdminTagController 标签管理控制器
 * @package app\portal\controller
 */
class AdminTagController extends AdminBaseController
{
    /**
     * 爆料标签管理
     * @adminMenu(
     *     'name'   => '爆料标签',
     *     'parent' => 'portal/AdminIndex/default',
     *     'display'=> true,
     *     'hasView'=> true,
     *     'order'  => 10000,
     *     'icon'   => '',
     *     'remark' => '爆料标签',
     *     'param'  => ''
     * )
     */
    public function index()
    {
        $portalTagModel = new BaoliaoTagModel();
        $tags           = $portalTagModel->paginate();

        $this->assign("arrStatus", $portalTagModel::$STATUS);
        $this->assign("tags", $tags);
        $this->assign('page', $tags->render());
        return $this->fetch();
    }

    /**
     * 添加爆料标签
     * @adminMenu(
     *     'name'   => '添加爆料标签',
     *     'parent' => 'index',
     *     'display'=> false,
     *     'hasView'=> true,
     *     'order'  => 10000,
     *     'icon'   => '',
     *     'remark' => '添加爆料标签',
     *     'param'  => ''
     * )
     */
    public function add()
    {
        $portalTagModel = new BaoliaoTagModel();
        $this->assign("arrStatus", $portalTagModel::$STATUS);
        return $this->fetch();
    }

    /**
     * 添加爆料标签提交
     * @adminMenu(
     *     'name'   => '添加爆料标签提交',
     *     'parent' => 'index',
     *     'display'=> false,
     *     'hasView'=> false,
     *     'order'  => 10000,
     *     'icon'   => '',
     *     'remark' => '添加爆料标签提交',
     *     'param'  => ''
     * )
     */
    public function addPost()
    {
        $arrData = $this->request->param();
        if(!empty($arrData['name'])){
            $portalTagModel = new BaoliaoTagModel();
            $portalTagModel->isUpdate(false)->allowField(true)->save($arrData);
            $this->success(lang("保存成功"));
        }
        $this->success(lang("保存失败"));

    }

    /**
     * 更新爆料标签状态
     * @adminMenu(
     *     'name'   => '更新标签状态',
     *     'parent' => 'index',
     *     'display'=> false,
     *     'hasView'=> false,
     *     'order'  => 10000,
     *     'icon'   => '',
     *     'remark' => '更新标签状态',
     *     'param'  => ''
     * )
     */
    public function upStatus()
    {
        $intId     = $this->request->param("id");
        $intStatus = $this->request->param("status");
        $intStatus = $intStatus ? 1 : 0;
        if (empty($intId)) {
            $this->error(lang("NO_ID"));
        }

        $portalTagModel = new BaoliaoTagModel();
        $portalTagModel->isUpdate(true)->save(["status" => $intStatus], ["id" => $intId]);

        $this->success(lang("保存成功"));

    }

    /**
     * 删除爆料标签
     * @adminMenu(
     *     'name'   => '删除爆料标签',
     *     'parent' => 'index',
     *     'display'=> false,
     *     'hasView'=> false,
     *     'order'  => 10000,
     *     'icon'   => '',
     *     'remark' => '删除爆料标签',
     *     'param'  => ''
     * )
     */
    public function delete()
    {
        $intId = $this->request->param("id", 0, 'intval');

        if (empty($intId)) {
            $this->error(lang("NO_ID"));
        }
        $portalTagModel = new BaoliaoTagModel();

        $portalTagModel->where(['id' => $intId])->delete();
        Db::name('baoliao_tag_post')->where('tag_id', $intId)->delete();
        $this->success(lang("DELETE_SUCCESS"));
    }
}
