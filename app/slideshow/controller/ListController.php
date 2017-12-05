<?php


namespace app\slideshow\controller;

use app\slideshow\model\ListModel;
use cmf\controller\AdminBaseController;
use think\Db;
use think\Request;
use think\Validate;


class ListController extends AdminBaseController
{
    public function getList()
    {

        $getListModel = new ListModel();

        $dataList = $getListModel->getList();


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

        $res['code']  = 0;
        $res['msg']   = '';
        $res['count'] = $dataList['count'];
        $res['data']  = $data;

        echo json_encode($res);

    }

    public function alterStatu(Request $request)
    {


        $slideshowId = $request->param('slideshowId');
        $status      = $request->param('status');

        $alterStateModel = new ListModel();
        $info            = $alterStateModel->alterState($slideshowId, $status);
        if ($info) {
            $res['code'] = 1;
//            $res['msg']  = '成功';
        } else {
            $res['code'] = 0;
//            $res['msg']  = '失败!';
        }

        echo json_encode($res);
    }


}