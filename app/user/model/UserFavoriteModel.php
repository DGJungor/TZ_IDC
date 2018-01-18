<?php
// +----------------------------------------------------------------------
// | ThinkCMF [ WE CAN DO IT MORE SIMPLE ]
// +----------------------------------------------------------------------
// | Copyright (c) 2013-2017 http://www.thinkcmf.com All rights reserved.
// +----------------------------------------------------------------------
// | Licensed ( http://www.apache.org/licenses/LICENSE-2.0 )
// +----------------------------------------------------------------------
// | Author: Powerless < wzxaini9@gmail.com>
// +----------------------------------------------------------------------
namespace app\user\model;

use think\Db;
use think\Model;

class UserFavoriteModel extends Model
{
	public function favorites()
	{
		$userId        = cmf_get_current_user_id();
		$userQuery     = Db::name("UserFavorite");
		$favorites     = $userQuery->where(['user_id' => $userId])->order('id desc')->paginate(10);
		$data['page']  = $favorites->render();
		$data['lists'] = $favorites->items();
		return $data;
	}

	public function deleteFavorite($id)
	{
		$userId           = cmf_get_current_user_id();
		$userQuery        = Db::name("UserFavorite");
		$where['id']      = $id;
		$where['user_id'] = $userId;
		$data             = $userQuery->where($where)->delete();
		return $data;
	}

	/**
	 *根据登录的用户id获取收藏文章数据
	 *
	 * @author 张俊
	 * @param int $limit
	 * @return false|\PDOStatement|string|\think\Collection
	 * @throws \think\db\exception\DataNotFoundException
	 * @throws \think\db\exception\ModelNotFoundException
	 * @throws \think\exception\DbException
	 *
	 */
	public function getUserFavorite($limit = 150)
	{
		//获取前台登录的id
		$userId = cmf_get_current_user_id();

		//获取文章数据
		$postData = $this
			->where('user_id', $userId)
			->limit($limit)
			->select();

		return $postData;

	}

}