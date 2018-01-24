<?php

namespace app\portal\model;

use think\Model;
use think\Db;

class UserFavoriteModel extends Model
{


	//配置主键字段位ID
	protected $pk = 'id';


	/**
	 *  添加用户收藏文章
	 *
	 * @param $data
	 * @return string
	 *
	 */
	public function addUserFavorite($data)
	{

		//添加用户文章收藏
		$result = $this->data($data)->save();

		return $result;
	}

	/**
	 * @param $userId
	 * @param $postId
	 * @return array|false|\PDOStatement|string|Model
	 * @throws \think\db\exception\DataNotFoundException
	 * @throws \think\db\exception\ModelNotFoundException
	 * @throws \think\exception\DbException
	 */
	public function queryFavoriteExist($userId, $postId)
	{

		$result = $this
			->where('user_id', $userId)
			->where('object_id', $postId)
			->find();

		return $result;
	}
}