<?php
/**
 * Created by VCode.
 * User: 李子梁
 * Date: 2017/12/12
 * Time: 14:06
 */

namespace app\web\controller;

include_once(dirname(dirname(dirname(__FILE__))) . '\\tools\\ajaxEcho.php');

include_once(dirname(dirname(dirname(__FILE__))) . '\\tools\\cookie_session.php');
/**
 * 前端对象输出（凡是通过ajax请求的接口都必须调用此函数输出）
 * 方法名: ajaxEcho
 * 参数：$data=[],$info="",$code=0
 * $data为要输出的数据，默认是空数组
 * $info提示信息，默认是空
 * $code错误代码，默认是0
 * */

/**
 * 获取token中的用户ID
 * 方法名：byTokenGetUser
 * 参数：$token
 */

use app\web\model\MemberModel;
use cmf\controller\HomeBaseController;
use think\Db;
use think\Request;
use think\Cookie;
use think\Session;
use think\Validate;

class MemberController extends HomeBaseController
{
	/**
	 * 初始化配置
	 */
	function __construct()
	{
		parent::__construct();
		cookie(["prefix" => "think_", "domain" => "www.newidckx.com", "expire" => 3600]);

		$this->memberModel = new MemberModel();

	}

	/**
	 * 会员前端页面渲染
	 * */
	public function index(Request $request)
	{
		return $this->fetch();
	}

	/**
	 *获取会员信息
	 *接口地址：http://www.newidckx.com/web/Member/getMemberData
	 *请求类型：get
	 *请求参数：
	 */
	public function getMemberData()
	{
		if (byTokenGetUser(Request::instance()->header()["token"])["userId"] == -1) {
			return ajaxEcho([], byTokenGetUser(Request::instance()->header()["token"])["msg"], 5000);
		}
		$result = $this->memberModel->getMemberData(byTokenGetUser(Request::instance()->header()["token"])["userId"]);


		if (count($result)) {
			return ajaxEcho($result, "获取成功", 1);
		} else {
			return ajaxEcho($result, "错误id");
		}

	}

	/**
	 *设置会员信息
	 *接口地址：http://www.newidckx.com/web/Member/setMemberData
	 *请求类型：post
	 *请求参数：
	 *nickname: 用户昵称
	 *mobile: 手机号码
	 *microblog: 微博
	 *WeChat: 微信
	 *name：用户名
	 */
	public function setMemberData()
	{
		$rule       = [
			'mobile' => 'require|number|min:11'
		];
		$msg        = [
			'mobile.number'  => '手机号只能是数字',
			'mobile.min'     => '手机号至少11位',
			'mobile.require' => '手机号不能为空'
		];
		$updateData = [
			"user_nickname" => input("post.nickname"),
			"mobile"        => input("post.phone"),
			"microblog"     => input("post.weibo"),
			"WeChat"        => input("post.weChat"),
			"user_login"    => input("post.name")
		];
		$validate   = new Validate($rule, $msg);//手机号码验证
		$check      = $validate->check($updateData);
		if ($check) {
			$result = $this->memberModel->setMemberData(input("post.id"), $updateData);
			if ($result) {
				return ajaxEcho(["editData" => $updateData], "修改成功", 1);
			} else {
				return ajaxEcho(["editData" => $updateData], "修改失败");
			}
		} else {
			return ajaxEcho(["editData" => $updateData], $validate->getError());
		}

	}

	/**
	 *修改密码
	 *接口地址：http://www.newidckx.com/web/Member/changePassword
	 *请求类型：post
	 *请求参数：
	 *id: 用户ID
	 *oldPassword: 旧密码
	 *password: 新密码
	 */
	public function changePassword()
	{
		if (input("?post.id")) {
			$oldPassword = $this->memberModel->getPassword(input("post.id"));
		} else {
			return ajaxEcho([], "id不存在");
		}
		if ($oldPassword) {
			if ($oldPassword == cmf_password(input("post.oldPassword"))) {
				$result = $this->memberModel->setPassword(input("post.id"), input("post.password"));
				if ($result) {
					cookie(null, 'think_');
					return ajaxEcho([], "密码修改成功", 1);
				} else {
					return ajaxEcho([], "密码修改失败");
				}
			} else {
				return ajaxEcho([], "原始密码错误");
			}
		} else {
			return ajaxEcho([], "id错误");
		}
	}

	/**
	 *发布文章
	 *接口地址：http://www.newidckx.com/web/Member/postArticle
	 *请求类型：post
	 *请求参数：
	 *typeid: 栏目ID
	 *title: 文章标题
	 *content: 文章内容
	 *descriptions: 文章描述
	 */
	public function postArticle()
	{
		if (byTokenGetUser(Request::instance()->header()["token"])["userId"] == -1) {
			return ajaxEcho([], byTokenGetUser(Request::instance()->header()["token"])["msg"], 5000);
		}
		$result = $this->memberModel->postArticle([
			"cid"          => input("post.typeid"),
			"title"        => input("post.title"),
			"content"      => input("post.content"),
			"descriptions" => input("post.descriptions"),
			"user_id"      => byTokenGetUser(Request::instance()->header()["token"])["userId"]
		]);
		if ($result) {
			return ajaxEcho(["result" => $result], "发布成功", 1);
		} else {
			return ajaxEcho([], "发布失败");
		}
	}

	/**
	 *获取文章
	 *接口地址：http://www.newidckx.com/web/Member/getArticle
	 *请求类型：post
	 *请求参数：
	 */
	public function getArticle()
	{
		if (byTokenGetUser(Request::instance()->header()["token"])["userId"] == -1) {
			return ajaxEcho([], byTokenGetUser(Request::instance()->header()["token"])["msg"], 5000);
		}
		$result = [];
		$data   = $this->memberModel->getArticle(byTokenGetUser(Request::instance()->header()["token"])["userId"]);
		for ($i = 0; $i < count($data); $i++) {
			array_push($result, ["aid" => $data[$i]["article_id"], "title" => $data[$i]["title"], "status" => $data[$i]["status"], "comment_count" => count($this->memberModel->getComment($data[$i]["article_id"]))]);
		}
		return ajaxEcho($result, "获取成功", 1);
	}

	/**
	 *获取文章分类
	 *接口地址：http://www.newidckx.com/web/Member/getCategory
	 *请求类型：post
	 *请求参数：
	 *name: 栏目名称
	 *sub: 是否为下级栏目
	 */
	public function getCategory()
	{
		if (input("?post.name")) {
			$result = $this->memberModel->getCategory(input("post.name"), input("post.sub"));
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
	 *获取评论
	 *接口地址：http://www.newidckx.com/web/Member/getComment
	 *请求类型：get
	 *请求参数：
	 *type： 如果此参数是user则是获取用户评论过的文章，如果没有此参数就是用户发布的文章的评论
	 */
	public function getComment()
	{
		if (byTokenGetUser(Request::instance()->header()["token"])["userId"] == -1) {
			return ajaxEcho([], byTokenGetUser(Request::instance()->header()["token"])["msg"], 5000);
		}
		if (input("get.type") == "user") {
			$result = $this->memberModel->getComment(byTokenGetUser(Request::instance()->header()["token"])["userId"], input("get.type"));
		} else {
			$result     = [];
			$parentData = $this->memberModel->getArticle(byTokenGetUser(Request::instance()->header()["token"])["userId"]);
			foreach ($parentData as $k => $v) {
				$data = $this->memberModel->getComment($v["article_id"]);
				foreach ($data as $c_k => $c_v) {
					$c_v["from"] = $v["title"];
					$c_v["link"] = $v["article_id"];
					array_push($result, $c_v);
				}
			}
		}

		if ($result) {
			return ajaxEcho($result, "获取成功", 1);
		} else {
			return ajaxEcho(["user_id" => byTokenGetUser(Request::instance()->header()["token"])["userId"]], "获取失败");
		}
	}

	/**
	 *获取收藏列表
	 *接口地址：http://www.newidckx.com/web/Member/getCollection
	 *请求类型：get
	 *请求参数：
	 */
	public function getCollection()
	{
		if (byTokenGetUser(Request::instance()->header()["token"])["userId"] == -1) {
			return ajaxEcho([], byTokenGetUser(Request::instance()->header()["token"])["msg"], 5000);
		}
		$result = $this->memberModel->getCollection(byTokenGetUser(Request::instance()->header()["token"])["userId"]);
		return ajaxEcho($result, "获取成功", 1);
	}

	/**
	 *获取消息列表（此功能还没实现）
	 *接口地址：http://www.newidckx.com/web/Member/getCollection
	 *请求类型：get
	 *请求参数：
	 */
	public function getMessages()
	{
		if (byTokenGetUser(Request::instance()->header()["token"])["userId"] == -1) {
			return ajaxEcho([], byTokenGetUser(Request::instance()->header()["token"])["msg"], 5000);
		}
		$result = $this->memberModel->messages();
		return ajaxEcho($result, "获取成功", 1);
	}
}