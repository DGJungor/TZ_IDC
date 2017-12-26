<?php

namespace app\web\model;

use think\Model;
use think\Db;

include_once(dirname(dirname(dirname(__FILE__))) . '\\tools\\filter.php');

class IndexModel extends Model
{


	protected $type = [
		'more' => 'array',
	];

	/**
	 * 获取首页热门信息
	 *
	 * @author 张俊
	 * @return false|\PDOStatement|string|\think\Collection
	 * @throws \think\db\exception\DataNotFoundException
	 * @throws \think\db\exception\ModelNotFoundException
	 * @throws \think\exception\DbException
	 */
	public function hotInfo()
	{
		//获取15天前的时间戳
		$timeAgo = strtotime("-15 day");

		//获取15天内点击数前7条文章数据
		$hotInfo = Db::name('portal_post')
			->field('id,post_title,create_time,post_hits')
			->where('create_time', '>', $timeAgo)
			->where('post_status', '1')
			->order('post_hits desc')
			->limit(7)
			->select();


		return $hotInfo;
	}


	/**
	 * 获取分类
	 * */
	public function getCategory($name, $sub)
	{
		$result = [];
		if ($sub) {
			$parentData = Db::name('portal_category')->where('name', $name)->find();
			$data       = Db::name('portal_category')->where('parent_id', $parentData["id"])->select();
			foreach ($data as $k => $v) {
				array_push($result, filter($v, ["id", "name", "status"]));
			}
		} else {
			$data = Db::name('portal_category')->where('name', $name)->find();
			array_push($result, filter($data, ["id", "name", "status"]));
		}
		return $result;
	}

	/**
	 * 获取分类内容
	 * */
	public function getTypeContent($id = "all")
	{
		$result = [];
		if ($id == "all") {
			$data = Db::name('portal_category_post')->alias('a')->join('idckx_portal_category t', 'a.category_id = t.id')->join('idckx_portal_post w', 'a.post_id = w.id')->order('create_time desc')->limit(7)->column("w.id,t.name,w.create_time,w.post_title,w.post_excerpt,w.post_hits,w.comment_count,w.more");
			foreach ($data as $k => $v) {
				$v["typeid"] = Db::name('portal_category_post')->alias('a')->where('post_id', $v["id"])->join('idckx_portal_category t', 'a.category_id = t.id')->find()["id"];
				array_push($result, filter($v, ["id", "create_time", "post_title", "post_excerpt", "post_hits", "comment_count", "name", "more", "typeid"]));
			}
			return $result;
		} else {
			$data = Db::name('portal_category_post')->alias('a')->where('category_id', $id)->join('idckx_portal_category t', 'a.category_id = t.id')->join('idckx_portal_post w', 'a.post_id = w.id')->order('create_time desc')->limit(7)->column("w.id,t.name,w.create_time,w.post_title,w.post_excerpt,w.post_hits,w.comment_count,w.more");

			foreach ($data as $k => $v) {
				$v["typeid"] = Db::name('portal_category_post')->alias('a')->where('post_id', $v["id"])->join('idckx_portal_category t', 'a.category_id = t.id')->find()["id"];
				array_push($result, filter($v, ["id", "create_time", "post_title", "post_excerpt", "post_hits", "comment_count", "name", "more", "typeid"]));
			}
			return $result;
		}
	}


	/**
	 *
	 *
	 *
	 */
	public function matchTag($tagName)
	{
		$tagCode = Db::name('portal_category')
			->field('id')
			->where('name', $tagName)
			->find();

		return $tagCode;

	}

	/**
	 *
	 * 前台ajax分类显示
	 *
	 * @author 张俊
	 * @param $tagCode
	 * @return false|\PDOStatement|string|\think\Collection
	 * @throws \think\db\exception\DataNotFoundException
	 * @throws \think\db\exception\ModelNotFoundException
	 * @throws \think\exception\DbException
	 *
	 */
	public function ajaxTag($tagCode)
	{

		$str = Db::table('idckx_portal_post')
			->alias('p')
			->join(['idckx_portal_category_post' => 'cp'], 'cp.post_id=p.id')
			->join(['idckx_portal_category' => 'c'], 'cp.category_id=c.id')
			->field('p.post_title,p.more,p.post_like,p.comment_count,p.post_excerpt,p.published_time,c.status,c.name,c.id')
			->select();

		return $str;
	}


	/**
	 * 获取友情链接
	 *
	 *
	 * @author 张俊
	 * @return false|\PDOStatement|string|\think\Collection
	 * @throws \think\db\exception\DataNotFoundException
	 * @throws \think\db\exception\ModelNotFoundException
	 * @throws \think\exception\DbException
	 *
	 */
	public function friendLink()
	{
		//查询状态为开启的友情链接
		$str = Db::name('link')
			->where('status', '=', '1')
			->select();

		return $str;
	}

	/**
	 * 获取首页轮播图
	 *
	 * @author 张俊
	 * @return false|\PDOStatement|string|\think\Collection
	 * @throws \think\db\exception\DataNotFoundException
	 * @throws \think\db\exception\ModelNotFoundException
	 * @throws \think\exception\DbException
	 *
	 */
	public function getSlideshow()
	{
		//取出按时间排序 的前3条数据
		$getSlideshow = Db::name('slideshow')
			->where('status', '=', '1')
			->limit(3)
			->order('create_time desc')
			->select();

		return $getSlideshow;

	}

	/**
	 * 获取手而已产品推介
	 *
	 * @author 张俊
	 * @return false|\PDOStatement|string|\think\Collection
	 * @throws \think\db\exception\DataNotFoundException
	 * @throws \think\db\exception\ModelNotFoundException
	 * @throws \think\exception\DbException
	 */
	public function getReferrals()
	{

		$getReferrals = Db::name('referrals')
			->where('state', '=', '1')
			->limit(1)
			->order('create_time desc')
			->select();

		return $getReferrals;

	}


	/**
	 * 获取首页广告信息
	 *
	 * @author 张俊
	 * @return mixed
	 * @throws \think\db\exception\DataNotFoundException
	 * @throws \think\db\exception\ModelNotFoundException
	 * @throws \think\exception\DbException
	 */
	public function getAd()
	{

		//在广告表中取出相关表不同广告位的最新一条数据
		for ($i = 1; $i <= 4; $i++) {
			$data      = Db::name('ad')
				->where('ad_site_id', $i)
				->where('ad_state', '1')
				->order('create_time desc')
				->limit(1)
				->select();
			$getAd[$i] = $data[0];
		}

		return $getAd;

	}

}