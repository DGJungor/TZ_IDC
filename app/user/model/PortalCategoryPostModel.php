<?php

namespace app\user\model;

use think\Db;
use think\Model;

class PortalCategoryPostModel extends Model
{


	/**
	 * 根据文章id获取类别ID
	 *
	 * @param null $postId
	 * @return mixed
	 * @throws \think\db\exception\DataNotFoundException
	 * @throws \think\db\exception\ModelNotFoundException
	 * @throws \think\exception\DbException
	 *
	 */
	public function getCategoryId($postId = null)
	{

		$categoryData = $this
			->where('post_id', $postId)
			->field('category_id')
			->find();

		return $categoryData['category_id'];
	}
}