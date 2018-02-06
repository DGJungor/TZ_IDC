<?php


namespace app\web\controller;

use cmf\controller\HomeBaseController;
use app\web\model\IndexModel;
use think\Db;
use think\Request;
use think\Cookie;
use think\Session;

include_once(dirname(dirname(dirname(__FILE__))) . '/tools/ajaxEcho.php');
include_once(dirname(dirname(dirname(__FILE__))) . '/tools/cookie_session.php');

class IndexController extends HomeBaseController
{
	/**
	 * 初始化配置
	 */
	function __construct()
	{
		parent::__construct();
		cookie(["prefix" => "think_", "domain" => "www.newidckx.com", "expire" => 3600]);
		//实例化首页模型
		$this->indexModel = new IndexModel();

	}

	public function index(Request $request)
	{

		$indexModel = new IndexModel();

		//从模型中取出轮播图
		$slideshowInfo = $this->indexModel->getSlideshow();
//        $test = time();

//        dump($slideshowInfo);

		//从模型中获得热门资讯
		$hotInfo = $this->indexModel->hotInfo();

		//从模型中取出友情链接的数据
		$friendLink = $this->indexModel->friendLink();
		//模板赋值
//        $this->assign('data', $data);
		$this->assign('slideshow', $slideshowInfo);
		$this->assign('hotInfo', $hotInfo);
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
	 *获取热门推荐栏目
	 *接口地址：http://www.newidckx.com/web/Index/hot_recommended_type
	 *请求类型：get
	 *请求参数：name：要获取的栏目名称，sub：是否为下级栏目
	 */
	public function hot_recommended_type()
	{
		if (input("?get.name")) {
			$result = $this->indexModel->getCategory(input("get.name"), input("get.sub"));
			if ($result) {
				return ajaxEcho($result, "获取成功", 1);
			} else {
				return ajaxEcho([], "获取失败没有这个分类");
			}
		} else {
			return ajaxEcho([], "请传入name分类名称");
		}
	}

	/**
	 *获取栏目内容
	 *接口地址：http://www.newidckx.com/web/Index/getTypeContent
	 *请求类型：get
	 *请求参数：id 栏目ID 没有则获取全部
	 */
	public function getTypeContent()
	{
		if (input("?get.id")) {
			$result = $this->indexModel->getTypeContent(input("get.id"));
			if ($result) {
				return ajaxEcho($result, "获取成功", 1);
			} else {
				return ajaxEcho([], "获取失败没有这个分类");
			}
		} else {
			$result = $this->indexModel->getTypeContent();
			if ($result) {
				return ajaxEcho($result, "获取成功", 1);
			} else {
				return ajaxEcho([], "获取失败没有这个分类");
			}
		}
	}

	/**
	 * 首页 友情链接
	 *
	 */
	public function friendLink()
	{

//		$test = ajaxEcho(null, '123123');


//		dump($test);
	}


	/**
	 * 测试控制器
	 */
	public function test()
	{
		$urls[] = "http://www.example.com/";

		$res = idckx_api_baidupush($urls);

		dump($res);
	}


}