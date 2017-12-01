<?php


namespace app\slideshow\controller;

use app\slideshow\model\AddModel;
use cmf\controller\AdminBaseController;
use think\Db;
use think\Loader;
use think\Request;
use think\Validate;


class AddController extends AdminBaseController
{
    public function upPic(Request $request)
    {

        $file = $request->file('file');
        $info = $file->move(ROOT_PATH . 'public' . DS . 'upload' . DS . 'slideshow');

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


    public function upPost(Request $request)
    {

        $data = $request->param();

        $validate = Loader::validate('AddSlideshow');

        if(!$validate->check($data['data'])){
//            dump($validate->getError());
            $res['code'] = 0;
            $res['msg']  = '请勿重复提交';
            echo json_encode($res);
            die();
        }


        $addModel = new AddModel();


        if (empty($data['data']['title'])) {
            $res['code'] = 0;
            $res['msg']  = '标题不能为空';
        }
        if (empty($data['data']['postID'])) {
            $res['code'] = 0;
            $res['msg']  = '文章ID不能为空';
        }
        if (empty($data['data']['pic_address'])) {
            $res['code'] = 0;
            $res['msg']  = '没有上传图片';
        }

        if (empty($data['data']['title'] || empty($data['data']['postID']) || empty($data['data']['pic_address']))) {

        } else {
            $postInfo = $addModel->addPost($data);

            if (empty($postInfo)) {
                $res['code'] = 0;
                $res['msg']  = '提交失败';
            } else {
                $res['code'] = 1;
                $res['msg']  = '提交成功';
            }
            
        }


        echo json_encode($res);

    }

}