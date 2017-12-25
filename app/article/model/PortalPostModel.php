<?php

namespace app\article\model;

use think\Model;
use think\Db;

/**
 * Class AdModel
 *
 * 前台文章页 文章模型
 *
 * @author 张俊
 * @package app\web\model
 *
 */
class PortalPostModel extends Model
{
	//配置主键字段位ID
	protected $pk = 'id';

	//配置more字段取数组类型
	protected $type = [

		'more' => 'array'
	];

	// 开启自动写入时间戳字段
	protected $autoWriteTimestamp = true;

	/**
	 *
	 * 获取文章页右侧他推荐内容标题
	 *
	 * @author 张俊
	 * @param int $num
	 * @return false|\PDOStatement|string|\think\Collection
	 * @throws \think\db\exception\DataNotFoundException
	 * @throws \think\db\exception\ModelNotFoundException
	 * @throws \think\exception\DbException
	 */
	public function getRecommendPost($num = 5)
	{

		$getRecommendPost = $this
			->field('post_title,id')
			->where('post_status', 1)
			->where('recommended', 1)
			->order('post_hits desc')
			->limit($num)
			->select();

		return $getRecommendPost;
	}


	/**
	 *
	 * 根据根据多个分类id 从数据库中获取  有关类型的文章  按照发布时间排序
	 *
	 * @author 张俊
	 * @param int $limit  '获取文章的数量'
	 * @param     $where '又关文章的所有的 类型id'
	 * @return false|\PDOStatement|string|\think\Collection
	 * @throws \think\db\exception\DataNotFoundException
	 * @throws \think\db\exception\ModelNotFoundException
	 * @throws \think\exception\DbException
	 */
	public function getRelatePost($limit=6,$where)
	{

		//根据专题ID查询相关的文章信息
		$postData = $this->alias('pp')
			->join('idckx_portal_category_post pcp', 'pp.id=pcp.post_id')
			->field('pp.id,pp.post_title,pp.more')
			->where('post_status', '1')
			->where('pcp.category_id', 'in',$where)
			->limit($limit)
			->order('published_time desc')
			->select();


		return $postData;
	}

	/**
	 * 首页专题显示
	 *
	 * 根据专题名称查询专题id 再根据专题id查询相关的文章
	 *
	 * @author 张俊
	 * @param     $specialName '专题名称'
	 * @param int $limit '获取的数量'
	 * @return false|\PDOStatement|string|\think\Collection
	 * @throws \think\db\exception\DataNotFoundException
	 * @throws \think\db\exception\ModelNotFoundException
	 * @throws \think\exception\DbException
	 *
	 */
	public function getSpecial($specialName, $limit = 5)
	{

		//获取专题名的ID
		$specialId = Db::name('portal_category')
			->field('id')
			->where('name', $specialName)
			->find();

		//根据专题ID查询相关的文章信息
		$getPost = $this->alias('pp')
			->join('idckx_portal_category_post pcp', 'pp.id=pcp.post_id')
			->field('pp.id,pp.post_title,pp.more')
			->where('post_status', '1')
			->where('pcp.category_id', $specialId['id'])
			->limit($limit)
			->order('published_time desc')
			->select();

		return $getPost;

	}

	/**
	 *
	 *根据根据时间,查询这个时间内点击数前几名的文章
	 *
	 * @author 张俊
	 * @param int $beforeDay '设置多少天前'
	 * @param int $limit '查询数据的条数'
	 * @return false|\PDOStatement|string|\think\Collection
	 * @throws \think\db\exception\DataNotFoundException
	 * @throws \think\db\exception\ModelNotFoundException
	 * @throws \think\exception\DbException
	 *
	 */
	public function getHotPost($beforeDay = 15, $limit = 6)
	{

		//拼和成函数需要的字符串
		$beforeDayStr = '-' . $beforeDay . ' day';

		//获得这个时间点的时间戳
		$beforeTime = strtotime($beforeDayStr);

		//查询数据库
		$postData = $this
			->field('id,post_title,more')
			->where('published_time', '>', $beforeTime)
			->where('post_status', '1')
			->order('post_hits desc')
			->limit($limit)
			->select();

		return $postData;

	}




	/**
	 * post_content 自动转化
	 *
	 * 可以将原后台添加文章的html代码 与 图片地址链接转化成端显示的html代码与地址链接
	 *
	 * @author 张俊
	 * @param $value
	 * @return string
	 */
	public function getPostContentAttr($value)
	{
		return cmf_replace_content_file_url(htmlspecialchars_decode($value));
	}

	/**
	 * post_content 自动转化
	 * @param $value
	 * @return string
	 */
	public function setPostContentAttr($value)
	{
		return htmlspecialchars(cmf_replace_content_file_url(htmlspecialchars_decode($value), true));
	}

	/**
	 * published_time 自动完成
	 * @param $value
	 * @return false|int
	 */
	public function setPublishedTimeAttr($value)
	{
		return strtotime($value);
	}
}