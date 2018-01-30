<?php

namespace app\user\model;

use think\Db;
use think\Model;

class PortalPostModel extends Model
{
	//开始时间戳
	protected $autoWriteTimestamp = true;

	/**
	 * 根据用户id获取用户发布的文章
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
			->where('delete_time', 0)
			->field('id,post_title,post_status,comment_count')
			->limit($limit)
			->select();

		return $postData;
	}

	/**
	 *   用户发布文章  返回文章id
	 */
	public function postArticle($postData)
	{

		$this->data($postData)->save();

		return $this->id;
	}


}