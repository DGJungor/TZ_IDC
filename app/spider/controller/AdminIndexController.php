<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018/5/24
 * Time: 15:15
 */


namespace app\spider\controller;


use app\spider\model\SpiderPostModel;
use cmf\controller\AdminBaseController;
use think\Db;
use think\Loader;
use think\Request;
use think\Validate;


class AdminIndexController extends AdminBaseController
{

    /**
     * 爬虫页首页
     */
    public function index()
    {

        return $this->fetch('index');

//        return '123';
    }


    /**
     *
     */
    public function test2()
    {
        $test = $this->request->param('test');

        return $test;

    }

    /**
     * 爬虫列表 接口
     */
    public function showList()
    {

        //获取分页参数
        $page   = $this->request->param('page');
        $length = $this->request->param('length');


        //  app\spider\model\SpiderPostModel
        $spiderPostModel = new SpiderPostModel();

        //获取文章列表数据
        $postData = $spiderPostModel->showList($page, $length);

        //将文章列表页中的时间戳 转换成'Y-m-d H:i:s' 格式
        $cleaRule = array(" ", "　", "\t", "\n", "\r");
        foreach ($postData as $k => $v) {
            $postData[$k]['date']     = date('Y-m-d H:i:s', $postData[$k]['date']);
            $postData[$k]['keywords'] = explode(',', str_replace($cleaRule, '', $postData[$k]['keywords']));
        }

        //将数组数据转换成Json格式 并返回
        return $postData->toJson();
//        dump($postData);

    }


    /**
     * 查询文章总数
     *
     * @author ZhangJun
     */
    public function postCount()
    {

        $spiderPostModel = new SpiderPostModel();
        return $spiderPostModel->count();

    }


    /**
     *
     */
    public function test()
    {

//        $tableData['tableData'][0]['date']    = '2016-05-02';
//        $tableData['tableData'][0]['name']    = '王小虎';
//        $tableData['tableData'][0]['address'] = '地址';


        for ($i = 0; $i < 10; $i++) {
            $tableData[$i]['date']    = '2016-05-02';
            $tableData[$i]['name']    = '快讯';
            $tableData[$i]['address'] = '地址';
            $tableData[$i]['aa']      = '地址';
        }
//        $tableData[0]['date']    = '2016-05-02';
//        $tableData[0]['name']    = '王小虎';
//        $tableData[0]['address'] = '地址';

//        dump(json_encode($tableData));
//        return json_encode($tableData);


        $spiderPostModel = new SpiderPostModel();

        $postData = $spiderPostModel->showList();

        foreach ($postData as $k => $v) {

            $postData[$k]['date'] = date('Y-m-d H:i:s', $postData[$k]['date']);

        }

        return $postData->toJson();


    }

}