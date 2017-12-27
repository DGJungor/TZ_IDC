<?php


namespace app\web\controller;

use app\web\model\AdModel;
use app\web\model\PortalPostModel;
use cmf\controller\HomeBaseController;
use app\web\model\IndexModel;
use think\Db;
use think\Request;

include_once(dirname(dirname(dirname(__FILE__))) . '\\tools\\ajaxEcho.php');
include_once(dirname(dirname(dirname(__FILE__))) . '\\tools\\cookie_session.php');

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
		//实例化首页模型
		$indexModel = new IndexModel();

		//实例化首页文章模型
		$portalPostModel = new PortalPostModel();

		//从模型中取出轮播图
		$slideshowInfo = $indexModel->getSlideshow();

		//从模型中获得热门资讯
		$hotInfo = $indexModel->hotInfo();

		//从模型中取出友情链接的数据
		$friendLink = $indexModel->friendLink();

		//从模型中去取出产品推介信息
		$referralsInfo = $indexModel->getReferrals();

		//从模型中取出广告信息数据
		$adInfo = $indexModel->getAd();

		//从文章模型中获取相关专题的文章信息
		$specialInfo[0] = $portalPostModel->getSpecial('IDC政策');
		$specialInfo[1] = $portalPostModel->getSpecial('行业解读');
		$specialInfo[2] = $portalPostModel->getSpecial('政策解读');

		//获取行业风向标信息
		$industryDriven =  $portalPostModel->getSpecial('行业风向标',4);

		// 数据调试
//        dump($industryDriven);

		//模板赋值
//        $this->assign('data', $data);
		$this->assign('special', $specialInfo);
		$this->assign('slideshow', $slideshowInfo);
		$this->assign('ad', $adInfo);
		$this->assign('hotInfo', $hotInfo);
		$this->assign('referralsInfo', $referralsInfo);
		$this->assign('friendLink', $friendLink);
		$this->assign('industryDriven', $industryDriven);

		return $this->fetch();


		//获取图片链接方法 {:cmf_get_image_preview_url($vo.more.thumbnail)}


		/*
		 *
		 *         分页demo
		 *   $users = Db::name('portal_tag')
		 *   ->field('id')
		 *   ->paginate(2);
		 *   $this->assign('users',$users);
		 *   $this->assign('page',$users->render());
		 *
		 */

		//分页demo html部分
		/*
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