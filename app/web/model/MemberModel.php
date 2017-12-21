<?php

namespace app\web\model;
include_once(dirname(dirname(dirname(__FILE__))) . '\\tools\\filter.php');

/**
 * 过滤查询出来的数据字段的
 * 方法名：filter
 * 参数：$arr,$filter=[],$myKey=false
 * $arr 是要过滤的数据
 * $filter 要筛选出来的字段
 * $myKey 是否要自定义输出的字段名称
 * 例如：
 *  filter(["a"=>10,"b"=>20,"c"=>30],["a","c"]) 它就会返回 ["a"=>10,"c"=>30]
 */

use think\Model;
use think\Db;

class MemberModel extends Model
{
	/**
	 * 获取用户信息
	 */
	public function getMemberData($id)
	{
		$data         = [];
		$result       = [];
		$data         = Db::name('user_vip')->where('id', $id)->find();
		$result       = filter($data, ["id", "sex", "avatar", "user_email", "user_nickname", "user_login", "mobile", "microblog", "WeChat"]);
		$result["qq"] = "";
		return $result;
	}

	/**
	 * 设置用户信息
	 */
	public function setMemberData($id, $updateData)
	{
		$result = Db::name('user_vip')->where('id', $id)->update($updateData);
		return $result;
	}

	/**
	 * 设置新密码
	 */
	public function setPassword($id, $password)
	{
		$result = Db::name('user_vip')->where('id', $id)->update([
			"user_pass" => cmf_password($password)
		]);
		return $result;
	}

	/**
	 * 获取用户密码
	 */
	public function getPassword($id)
	{
		$result = Db::name('user_vip')->where('id', $id)->find();
		return $result["user_pass"];
	}

	/**
	 * 获取用户发布的文章
	 */
	public function getArticle($user_id)
	{
		$result = [];
		$data   = Db::name('user_article')->where('user_id', $user_id)->select();
		if ($data) {
			foreach ($data as $k => $v) {
				array_push($result, filter($v, ["article_id", "title", "user_id", "status"]));
			}
			return $result;
		} else {
			return "未找到";
		}


	}

	/**
	 * 获取用户评论
	 */
	public function getComment($id, $type = "article")
	{
		if ($type === "user") {
			$result = [];
			$data   = Db::name('user_comment')->where("user_id", $id)->select();
			foreach ($data as $k => $v) {
				array_push($result, filter($v, ["content", "time", "comment_id"]));
				$result[$k]["user"]   = filter(Db::name("user_vip")->where("id", $v["user_id"])->find(), ["avatar", "user_nickname", "user_email", "id"]);
				$result[$k]["replys"] = [];
				foreach (Db::name("user_reply")->where("comment_id", $v["comment_id"])->select() as $r_k => $r_v) {
					array_push($result[$k]["replys"], filter($r_v, ["content", "time", "id"]));
					$result[$k]["replys"][$r_k]["user"] = filter(Db::name("user_vip")->where("id", $r_v["user_id"])->find(), ["avatar", "user_nickname", "user_email", "id"]);
				}
			}
			return $result;
		}
		if ($type === "article") {
			$result = [];
			$data   = Db::name('user_comment')->where('article_id', $id)->select();
			foreach ($data as $k => $v) {
				array_push($result, filter($v, ["content", "time", "comment_id"]));
				$result[$k]["user"]   = filter(Db::name("user_vip")->where("id", $v["user_id"])->find(), ["avatar", "user_nickname", "user_email", "id"]);
				$result[$k]["replys"] = [];
				foreach (Db::name("user_reply")->where("comment_id", $v["comment_id"])->select() as $r_k => $r_v) {
					array_push($result[$k]["replys"], filter($r_v, ["content", "time", "id"]));
					$result[$k]["replys"][$r_k]["user"] = filter(Db::name("user_vip")->where("id", $r_v["user_id"])->find(), ["avatar", "user_nickname", "user_email", "id"]);
				}
			}
			return $result;
		}


	}

	/**
	 * 发布用户文章
	 */
	public function postArticle($data)
	{
		$result = Db::name('user_article')->insert($data);
		if ($result) {
			Db::name('user_index')->insert([
				"user_id"    => $data["user_id"],
				"article_id" => Db::name('user_article')->getLastInsID()
			]);
		}
		return $result;
	}

	/**
	 * 获取文章分类
	 */
	public function getCategory($name, $sub)
	{
		$result = [];
		if ($sub) {
			$parentData = Db::name('portal_category')->where('name', $name)->find();
			$data       = Db::name('portal_category')->where('parent_id', $parentData["id"])->select();
		} else {
			$data = Db::name('portal_category')->where('name', $name)->find();
		}
		foreach ($data as $k => $v) {
			array_push($result, filter($v, ["id", "name", "status"]));
		}

		return $result;
	}

	/**
	 * 获取用户收藏的文章
	 */
	public function getCollection($user_id)
	{
		$result = [];
		$data   = Db::name('user_collection')->where("user_id", $user_id)->select();
		foreach ($data as $k => $v) {
			if ($v["type"] == "user") {
				$art_result         = Db::name('user_article')->where("article_id", $v["article_id"])->find();
				$art_result["date"] = time();
				$art_result["type"] = $v["type"];
				array_push($result, filter($art_result, ["article_id", "title", "date", "type"]));
			}
			if ($v["type"] == "post") {
				$art_result         = Db::name('portal_post')->where("id", $v["article_id"])->find();
				$art_result["date"] = time();
				$art_result["type"] = $v["type"];
				array_push($result, filter($art_result, ["article_id" => "id", "title" => "post_title", "date" => "date", "type" => "type"], true));
			}
		}
		return $result;
	}

	/**
	 * 获取信息（此功能还未实现）
	 */
	public function messages()
	{
		return [[
					"messages_id"      => 1,
					"messages_title"   => "你回复了，xxx用户",
					"messages_content" => "用户收到消息了",
					"date"             => time()
				]];
	}
}