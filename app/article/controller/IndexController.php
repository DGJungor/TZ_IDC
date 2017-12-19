<?php

namespace app\article\controller;

use app\article\model\AdModel;
use app\article\model\PortalCategoryPostModel;
use app\article\model\PortalPostModel;
use app\article\model\ReferralsModel;
use cmf\controller\HomeBaseController;
use think\Request;

/**
 * Class IndexController
 *
 * 前台文章页首页面控制器
 *
 * @author 张俊
 * @package app\article\controller
 *
 */
class IndexController extends HomeBaseController
{
	public function index(Request $request)
	{


//		http://www.idckxj.com/article/index?id=13&type=post

//		$this->error("文章不存在");
//		die();
		//实例化模型
		$portalPostModel         = new PortalPostModel();
		$adModel                 = new AdModel();
		$referralsModel          = new ReferralsModel();
		$portalCategoryPostModel = new PortalCategoryPostModel();

		//获取文章id
		$postId = $request->param('id');

		//测试数据
//		$postId = 13;

		//实例化文章模型
		$portalPostModel = new PortalPostModel();

		//根据文章id取出文章数据
		$postData = $portalPostModel->get($postId);
		if (!$postData) {
			$this->error("文章不存在","/");
		}

		//将字符串与图片地址 转化成html代码和  绝对路径的图片地址
		$postHtmlData                  = $portalPostModel->getPostContentAttr($postData['post_content']);
		$postData->post_content_toHtml = $postHtmlData;

		//将时间戳转化成中文时间
		$timeToStr                           = date("Y年m月d日 H:i:s", $postData['published_time']);
		$postData->post_published_time_toStr = $timeToStr;

		//获取关键词(标签),并将关键词以英文逗号切割成数组
		$keywords = explode(',', $postData['post_keywords']);

		//获取热门文章
		$hotPost = $portalPostModel->getHotPost();

		//向热门文章中添加排名次序
		foreach ($hotPost as $key => $value) {
			$hotPost[$key]->rank = $key + 1;
		}

		//获取推荐文章
		$recommendedData = $portalPostModel->getRecommendPost($postId);

		//获取3号广告位的广告数据
		$adData = $adModel->getAd(3);

		//获取产品推荐数据
		$referralsData = $referralsModel->getReferrals();

		//根据文章id获取文章下方相关推荐文章
		$specialId = $portalCategoryPostModel->getSpecialId(5);
		foreach ($specialId as $item => $value) {
			$where[] = $value['category_id'];
		}
		$relatePost = $portalPostModel->getRelatePost(6, $where);

		//调试数据
//		dump($relatePost);

		//赋值变量  并渲染模板
		$this->assign('article', $postData);
		$this->assign('relatePost', $relatePost);
		$this->assign('recommended', $recommendedData);
		$this->assign('referrals', $referralsData);
		$this->assign('ad', $adData);
		$this->assign('hotPost', $hotPost);
		$this->assign('keywords', $keywords);
		return $this->fetch();
//        return $this->fetch(':index');
	}
}
