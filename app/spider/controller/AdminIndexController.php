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
     * 爬虫列表 接口
     */
    public function showList()
    {

        $spiderPostModel = new SpiderPostModel();

        dump($spiderPostModel->count());

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
        }
//        $tableData[0]['date']    = '2016-05-02';
//        $tableData[0]['name']    = '王小虎';
//        $tableData[0]['address'] = '地址';

//        dump(json_encode($tableData));
        return json_encode($tableData);

//        return '{
//                    tableData: [{
//                        date: \'2016-05-02\',
//                        name: \'王小虎\',
//                        address: \'上海市普陀区金沙江路 1518 弄\'
//                    }, {
//                        date: \'2016-05-04\',
//                        name: \'王小虎\',
//                        address: \'上海市普陀区金沙江路 1517 弄\'
//                    }, {
//                        date: \'2016-05-01\',
//                        name: \'王小虎\',
//                        address: \'上海市普陀区金沙江路 1519 弄\'
//                    }, {
//                        date: \'2016-05-03\',
//                        name: \'王小虎\',
//                        address: \'上海市普陀区金沙江路 1516 弄\'
//                    }]
//                }';

    }

}