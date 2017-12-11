<?php

namespace app\referrals\controller;

use app\referrals\model\AdminListModel;
use app\referrals\model;
use cmf\controller\AdminBaseController;
use think\Loader;
use think\Request;
use think\Validate;

/**
 * Class AdminListController
 *
 * @author 张俊
 * @package app\referrals\controller
 *
 *
 */
class AdminListController extends AdminBaseController
{

    /**
     * 产品推介列表
     *
     * AJAX获取获列表
     *
     * @author 张俊
     *
     */
    public function getList(Request $request)
    {

        //获取分页数据
        $page  = $request->param('page');
        $limit = $request->param('limit');

        //计算分页偏移量
        $offset = ($page - 1) * $limit;

        //实例化  产品推介列表模型
        $getListModel = new AdminListModel();

        //从产品推介列表模型中取出 产品推介列表数据
        $dataList = $getListModel->getList($offset, $limit);

        //将获取的广告列表数据重组成新数组
        $i = 0;
        foreach ($dataList['list'] as $k => $v) {
            $data[$k] = $v;
            switch ($data[$k]['state']) {
                case 0:
                    $data[$k]['state'] = "关闭";
                    break;
                case 1:
                    $data[$k]['state'] = "开启";
                    break;
                default:
                    break;
            }
            $i++;
        }

        //将数据 与 状态码 和消息重组成数组并转成json 数据返回到视图层的AJAX中
        $res['code']  = 0;
        $res['msg']   = '';
        $res['count'] = $dataList['count'];
        $res['data']  = $data;
        echo json_encode($res);

    }

    /**
     * 产品推介列表
     *
     * 修改状态
     *
     * @author 张俊
     * @param Request $request
     *
     */
    public function alterStatu(Request $request)
    {

        //从请求信息中获得  数据
        $referralsId = $request->param('referralsId');
        $status      = $request->param('status');

        $alterStateModel = new AdminListModel();
        $info            = $alterStateModel->alterState($referralsId, $status);
        if ($info) {
            $res['code'] = 1;
//            $res['msg']  = '成功';
        } else {
            $res['code'] = 0;
//            $res['msg']  = '失败!';
        }

        echo json_encode($res);
    }

    /**
     *
     * 删除产品推介
     *
     * @author 张俊
     *
     */
    public function delReferrals(Request $request)
    {
        //获取要删除的商品推介id
        $referralsId = $request->param('referralsId');

        //实例化  产品产品推介模型
        $AdminListModel = new AdminListModel();

        //获取需要删除的图片路径
        $picPath = $AdminListModel->getPicPath($referralsId);

        //图片文件的绝对路径
        $picPath = ROOT_PATH . 'public' . DS . 'upload' . DS . 'referrals' . DS . $picPath['pic_address'];

        //若图片文件存在则执行删除
        if (is_file($picPath)) {
            $delInfo = unlink($picPath);
        }

        //图片文件删除成功 或者 图片文件不存在执行删除数据库中的相关产品推介信息
        if (!is_file($picPath)) {
            $delReferrals = $AdminListModel->delReferrals($referralsId);
        }

        //判断操作结果
        if ($delReferrals) {
            $res['code'] = 1;
            $res['msg']  = '删除成功';
        } else {
            $res['code'] = 0;
            $res['msg']  = '删除失败';
        }

        echo json_encode($res);

    }


}

