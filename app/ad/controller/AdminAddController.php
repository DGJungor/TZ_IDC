<?php

namespace app\ad\controller;

use app\ad\model\AdminAddModel;
use app\ad\model;
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
class AdminAddController extends AdminBaseController
{


    /**
     * @param Request $request
     * 广告添加 上传图片的AJAX操作
     *
     *
     */
    public function upPic(Request $request)
    {

        //获取上传文件信息
        $file = $request->file('file');
        //将上传的图片移动到指定的文件夹    /public/upload/ad/日期/图片文件名
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


    /**
     * @param Request $request
     * 广告添加AJAX操作
     *
     *
     */
    public function upPost(Request $request)
    {

        //获取表单信息
        $data = $request->param();

        //判断表单信息是否正确
        $validate = Loader::validate('AddAd');
        if(!$validate->check($data['data'])){
//            dump($validate->getError());
            $res['code'] = 0;
            $res['msg']  = '请勿重复提交';
            echo json_encode($res);
            die();
        }

        //实例化 广告添加模型
        $addModel = new AdminAddModel();

        //将表单信息发送至模型层  并更加返回信息 判断是否添加成功
        $info = $addModel->addAd($data);
        if ($info) {
            $res['code'] = 1;
            $res['msg']  = '提交成功';
        } else {
            $res['code'] = 0;
            $res['msg']  = '提交失败';
        }

        //将添加结果信息 转成json格式返回视图层
        echo json_encode($res);


    }


}
