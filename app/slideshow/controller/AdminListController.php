<?php


namespace app\slideshow\controller;

use app\slideshow\model\AdminListModel;
use cmf\controller\AdminBaseController;
use think\Db;
use think\Request;
use think\Validate;


/**
 * Class ListController
 *
 * 轮播图列表控制器
 *
 * @author 张俊
 * @package app\slideshow\controller
 *
 *
 */
class AdminListController extends AdminBaseController
{
    public function getList(Request $request)
    {

        //获取分页数据
        $page  = $request->param('page');
        $limit = $request->param('limit');

        //计算分页偏移量
        $offset = ($page - 1) * $limit;

        //实例化轮播图 列表模型
        $getListModel = new AdminListModel();

        //查询数据库中轮播图数据
        $dataList = $getListModel->getList($offset, $limit);

        //将数据重组成新数组
        $i = 0;
        foreach ($dataList['list'] as $k => $v) {
            $data[$k] = $v;
            switch ($data[$k]['status']) {
                case 0:
                    $data[$k]['status'] = "关闭";
                    break;
                case 1:
                    $data[$k]['status'] = "开启";
                    break;
                default:
                    break;
            }
            $i++;
        }

        //向新数组中添加AJAX需要的信息 并转成json形式  发送到视图层
        $res['code']  = 0;
        $res['msg']   = '';
        $res['count'] = $dataList['count'];
        $res['data']  = $data;
        echo json_encode($res);

    }

    /**
     * 修改轮播图控制器
     *
     * @author 张俊
     * @param Request $request
     *
     */
    public function alterStatu(Request $request)
    {

        //获取需要修改状态的轮播图的id  和需要修改成的状态的状态码
        $slideshowId = $request->param('slideshowId');
        $status      = $request->param('status');

        //实例化 轮播图列表模型  向模型发送修改信息
        $alterStateModel = new AdminListModel();
        $info            = $alterStateModel->alterState($slideshowId, $status);

        //根据模型返回的信息 判断对否修改成功
        if ($info) {
            $res['code'] = 1;
//            $res['msg']  = '成功';
        } else {
            $res['code'] = 0;
//            $res['msg']  = '失败!';
        }

        //将数据转换成jso形式 返回到视图成
        echo json_encode($res);
    }


    /**
     *
     * 删除轮播图
     *
     * @author 张俊
     *
     */
    public function delSlideshow(Request $request)
    {
        //获取要删除的商品推介id
        $slideshowId = $request->param('slideshowId');

        //实例化  产品产品推介模型
        $AdminListModel = new AdminListModel();

        //获取需要删除的图片路径
        $picPath = $AdminListModel->getPicPath($slideshowId);

        //图片文件的绝对路径
        $picPath = ROOT_PATH . 'public' . DS . 'upload' . DS . 'slideshow' . DS . $picPath['pic_address'];

        //若图片文件存在则执行删除
        if (is_file($picPath)) {
            $delInfo = unlink($picPath);
        }

        //图片文件删除成功 或者 图片文件不存在执行删除数据库中的相关产品推介信息
        if (!is_file($picPath)) {
            $delslideshow = $AdminListModel->delSlideshow($slideshowId);
        }

        //判断操作结果
        if ($delslideshow) {
            $res['code'] = 1;
            $res['msg']  = '删除成功';
        } else {
            $res['code'] = 0;
            $res['msg']  = '删除失败';
        }

        echo json_encode($res);

    }



}