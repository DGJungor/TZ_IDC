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
        //实例化首页模型
        $indexModel = new IndexModel();

        //从模型中取出轮播图
        $slideshowInfo = $indexModel->getSlideshow();
//        $test = time();

//        dump($slideshowInfo);

        //从模型中获得热门资讯
        $hotInfo = $indexModel->hotInfo();

        //从模型中取出友情链接的数据
        $friendLink = $indexModel->friendLink();

        //从模型中去取出产品推介信息
        $referralsInfo = $indexModel->getReferrals();

        dump($referralsInfo);


//        $data['friendLink'] = $friendLink;
//        dump($friendLink);
//        dump($data);


        //模板赋值
//        $this->assign('data', $data);
        $this->assign('slideshow', $slideshowInfo);
        $this->assign('hotInfo', $hotInfo);
        $this->assign('referralsInfo', $referralsInfo);
        $this->assign('friendLink', $friendLink);
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