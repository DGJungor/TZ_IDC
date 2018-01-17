<?php

namespace app\user\model;

use think\Db;
use think\Model;

class PortalPostModel extends Model
{

	/**
	 * 获取用户发布的文章
	 *
	 * @param $userId
	 * @return false|\PDOStatement|string|\think\Collection
	 * @throws \think\db\exception\DataNotFoundException
	 * @throws \think\db\exception\ModelNotFoundException
	 * @throws \think\exception\DbException
	 *
	 */
	public function getUserArticle($userId, $limit = 150)
	{

		$postData = $this
			->where('user_id', $userId)
			->field('id,post_title,post_status,comment_count')
			->limit($limit)
			->select();

		return $postData;
	}

}