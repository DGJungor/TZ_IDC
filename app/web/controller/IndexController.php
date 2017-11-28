<?php


namespace app\web\controller;

use cmf\controller\HomeBaseController;
use app\web\model\IndexModel;
use think\Db;
use think\Request;

class IndexController extends HomeBaseController
{
    public function index(Request $request)
    {

        $indexModel = new IndexModel();
        $a = $indexModel->hotInfo();

//        $b = json_encode('中文');
//        $v = json_decode($b);

        $list = $this->indexAjax();

        //从模型中取出友情链接的数据
        $friendLink = $indexModel->friendLink();

        $data['friendLink'] = $friendLink;

        dump($friendLink);

//        dump($data);

        $this->assign('data',$data);
        $this->assign('friendLink',$friendLink);
        return $this->fetch();





        //分页demo
//        $users = Db::name('portal_tag')
//            ->field('id')
//            ->paginate(2);
//        $this->assign('users',$users);
//        $this->assign('page',$users->render());



        //分页demo html部分
        /**
         *
         * <div>
         * <foreach name="users" item="vo">
         * <div>{$vo.id}</div>
         * </foreach>
         *
         * <div class="pagination">{$users->render()}</div>
         *
         */


    }

    /**
     *首页Ajax 列表标签
     *
     *
     *
     */
    public function indexAjax()
    {

        //模拟接收到的中文标签数据
        $testName = json_encode('热门资讯');

        $tagName = json_decode($testName);

        $indexModel = New IndexModel();

        $tagCode = $indexModel->matchTag($tagName);


        $str = $indexModel->ajaxTag($tagCode['id']);
//        $str =  $tagCode;


        return $str;


    }

    /**
     * 首页 友情链接
     *
     */
    public function friendLink()
    {


    }


}