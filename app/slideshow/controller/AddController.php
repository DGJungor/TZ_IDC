<?php


namespace app\slideshow\controller;

use app\slideshow\model\AddModel;
use cmf\controller\AdminBaseController;
use think\Db;
use think\Request;

class AddController extends AdminBaseController
{
    public function upPic(Request $request)
    {

        $file = $request->file('file');
        $info = $file->move(ROOT_PATH.'public'.DS.'upload'.DS.'slideshow');

        if ($info){
            $res['code'] = 1;
            $res['msg'] = '上传成功!';
            $res['saveName'] = $info->getSaveName();
        }else{
            $res['0'] = 2;
            $res['msg']='上传失败!';
        }

        echo json_encode($res);
    }


    public function upPost(Request $request)
    {

        $info = $request->param();

        $addModel = new AddModel();

        $info2 = $addModel->addPost();

        dump($info2);
        dump($info);
//        $res['test'] = 'abc';
//        echo json_encode($res);

    }

}