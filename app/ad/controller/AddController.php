<?php

namespace app\ad\controller;

use app\add\model\AddModel;
use cmf\controller\AdminBaseController;
use think\Loader;
use think\Request;
use think\Validate;

/**
 * Class AdminIndexController
 * @package app\Ad\controller
 * @adminMenuRoot(
 *     'name'   =>'广告管理',
 *     'action' =>'default',
 * )
 */
class AddController extends AdminBaseController
{


    public function upPic(Request $request)
    {

        $file = $request->file('file');
        $info = $file->move(ROOT_PATH . 'public' . DS . 'upload' . DS . 'ad');

        if ($info) {
            $res['code']     = 1;
            $res['msg']      = '上传成功!';
            $res['saveName'] = $info->getSaveName();
        } else {
            $res['0']   = 2;
            $res['msg'] = '上传失败!';
        }

        echo json_encode($res);
    }


    public  function  upPost(Request $request)
    {

        $data = $request->param();
//        $validate = Loader::validate('AddSlideshow');


        $addModel = new AddModel();






        dump($data);

    }



}
