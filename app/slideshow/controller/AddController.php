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

    /**
     * @param Request $request
     * 添加轮播图 上传图片AJAX操作
     *
     *
     */
    public function upPic(Request $request)
    {


        //获取添加上传的信息
        $file = $request->file('file');

        // 将上传的图片移动到 指定的文件加   /public/upload/slideshow/日期/文件名字
        $info = $file->move(ROOT_PATH . 'public' . DS . 'upload' . DS . 'slideshow');
        //通过移动文件的信息判断是否上传成功  并添加返回信息和返回码
        if ($info) {
            $res['code']     = 1;
            $res['msg']      = '上传成功!';
            $res['saveName'] = $info->getSaveName();
        } else {
            $res['0']   = 2;
            $res['msg'] = '上传失败!';
        }

        //上传结果的状态码和信息 转化成json格式 返回到视图层
        echo json_encode($res);
    }


    /**
     * @param Request $request
     * 添加轮播图 提交表单AJAX操作
     *
     */
    public function upPost(Request $request)
    {
        //获取POST过来的 表单信息
        $data = $request->param();

        //实例化 验证规则
        $validate = Loader::validate('AddSlideshow');

        //判断表单信息是否正确 并将结果添加到数组
        if(!$validate->check($data['data'])){
//            dump($validate->getError());
            $res['code'] = 0;
            $res['msg']  = '请勿重复提交';
            echo json_encode($res);
            die();
        }

        //实例化  轮播图添加 模型
        $addModel = new AddModel();

        //判断 表单内容是否全
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

        //将提交结果的信息和状态码 转换成 json格式返回到视图层
        echo json_encode($res);

    }

}