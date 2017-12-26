<?php

namespace app\article\model;

use think\Model;
use think\Db;


/**
 * Class PortalCategoryPost
 *
 * 前台内容页  专题分类模型
 *
 * @author 张俊
 * @package app\article\model
 *
 */
class PortalCategoryPostModel extends Model
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
	 * 根据文章ID 查询相关的专题ID
	 *
	 * @author 张俊
	 * @param $postId '文章ID'
	 * @return false|\PDOStatement|string|\think\Collection
	 * @throws \think\db\exception\DataNotFoundException
	 * @throws \think\db\exception\ModelNotFoundException
	 * @throws \think\exception\DbException
	 */
	public function getSpecialId($postId)
	{
		//获取文章相关的专题ID
		$specialId = $this
			->field('category_id')
			->where('post_id', $postId)
			->select();

			return $specialId;
	}

}