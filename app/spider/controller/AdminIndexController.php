<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018/5/24
 * Time: 15:15
 */


namespace app\spider\controller;

use app\slideshow\model\AdminAddModel;
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
    public function listPost()
    {

//        $tableData['tableData'][0]['date']    = '2016-05-02';
//        $tableData['tableData'][0]['name']    = '王小虎';
//        $tableData['tableData'][0]['address'] = '地址';


        for ($i=0; $i<100; $i++) {
            $tableData[$i]['date']    = '2016-05-02';
            $tableData[$i]['name']    = '王小虎';
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