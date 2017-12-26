<?php
/**
 * Created by VCode.
 * User: 李子梁
 * Date: 2017/12/14
 * Time: 13:42
 */

namespace app\web\controller;

include_once(dirname(dirname(dirname(__FILE__))) . '\\tools\\ajaxEcho.php');
include_once(dirname(dirname(dirname(__FILE__))) . '\\tools\\cookie_session.php');


/**
 * 方法名: ajaxEcho
 * 参数：$data=[],$info="",$code=0
 * */

/**
 * 获取token中的用户ID
 * 方法名：byTokenGetUser
 * 参数：$token
 */

use app\web\model\ArticleModel;
use cmf\controller\HomeBaseController;
use think\Db;
use think\Request;
use think\Cookie;
use think\Session;
use think\Validate;

class ArticleController extends HomeBaseController
{
	/**
	 * 初始化配置
	 */
	function __construct()
	{
		parent::__construct();
		cookie(["prefix" => "think_", "domain" => "www.newidckx.com", "expire" => 3600]);
		$this->articleModel = new ArticleModel();
	}

	/**
	 * 文章前端页面渲染
	 * */
	public function index(Request $request)
	{
		return $this->fetch();
	}

	/**
	 *发布评论
	 *接口地址：http://www.newidckx.com/web/Article/postComment
	 *请求类型：post
	 *请求参数：
	 *aid: 文章ID
	 *content: 文章内容
	 */
	public function postComment()
	{
		if (byTokenGetUser(Request::instance()->header()["token"])["userId"] == -1) {
			return ajaxEcho([], byTokenGetUser(Request::instance()->header()["token"])["msg"], 5000);
		}
		$data   = [
			"content"      => input("post.content"),
			"time"         => time(),
			"article_id"   => input("post.aid"),
			"user_id"      => byTokenGetUser(Request::instance()->header()["token"])["userId"],
			"article_type" => input("post.type")
		];
		$result = $this->articleModel->postComment(input("post.aid"), byTokenGetUser(Request::instance()->header()["token"])["userId"], $data);


		if ($result) {
			return ajaxEcho(["result" => $result], "添加成功", 1);
		} else {
			return ajaxEcho([], "添加失败");
		}
	}

	/**
	 *回复评论
	 *接口地址：http://www.newidckx.com/web/Article/postReply
	 *请求类型：post
	 *请求参数：
	 *comment_id: 评论ID
	 *content: 回复内容
	 */
	public function postReply()
	{
		if (byTokenGetUser(Request::instance()->header()["token"])["userId"] == -1) {
			return ajaxEcho([], byTokenGetUser(Request::instance()->header()["token"])["msg"], 5000);
		}
		$data   = [
			"comment_id" => input("post.comment_id"),
			"content"    => input("post.content"),
			"time"       => time(),
			"user_id"    => byTokenGetUser(Request::instance()->header()["token"])["userId"]
		];
		$result = $this->articleModel->postReply($data);
		if ($result) {
			return ajaxEcho(["result" => $result], "发布成功", 1);
		} else {
			return ajaxEcho([], "发布失败");
		}
	}

	/**
	 *收藏文章
	 *接口地址：http://www.newidckx.com/web/Article/collection
	 *请求类型：post
	 *请求参数：
	 *id: 文章ID
	 *type: 类型，user是指用户发布的文章，post就是系统后台发布的文章
	 */
	public function collection()
	{
		if (byTokenGetUser(Request::instance()->header()["token"])["userId"] == -1) {
			return ajaxEcho([], byTokenGetUser(Request::instance()->header()["token"])["msg"], 5000);
		}
		$data   = [
			"article_id" => input("post.id"),
			"user_id"    => byTokenGetUser(Request::instance()->header()["token"])["userId"],
			"type"       => input("post.type")
		];
		$result = $this->articleModel->postCollection($data);
		if ($result) {
			return ajaxEcho([], "收藏成功", 1);
		} else {
			return ajaxEcho([], "收藏失败,你已经收藏过了");
		}
	}

	/**
	 *获取评论
	 *接口地址：http://www.newidckx.com/web/Article/getComment
	 *请求类型：get
	 *请求参数：
	 *id: 文章ID
	 */
	public function getComment()
	{
		if (byTokenGetUser(Request::instance()->header()["token"])["userId"] == -1) {
			return ajaxEcho([], byTokenGetUser(Request::instance()->header()["token"])["msg"], 5000);
		}
		$result = $this->articleModel->getComment(input("get.id"));
		if ($result) {
			return ajaxEcho($result, "获取成功", 1);
		} else {
			return ajaxEcho([], "获取失败");
		}
	}
}