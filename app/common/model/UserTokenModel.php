<?php

namespace app\common\model;

use think\Db;
use think\Model;

class UserTokenModel extends Model
{
	//配置主键字段位ID
	protected $pk = 'id';


	/**
	 * 通过token值查询有关信息
	 *
	 * @author 张俊
	 * @param $token
	 * @return array|false|\PDOStatement|string|Model
	 * @throws \think\db\exception\DataNotFoundException
	 * @throws \think\db\exception\ModelNotFoundException
	 * @throws \think\exception\DbException
	 *
	 */
	public function getTokenData($token)
	{
		$result = $this
			->where('token', $token)
			->find();

		return $result;
	}


	public function addUserTokenData($userId, $token, $expireTime = 3600, $deviceType = "web")
	{
		$data = [
			"user_id"     => $userId,
			"expire_time" => time() + $expireTime,
			"create_time" => time(),
			"token"       => $token,
			"device_type" => $deviceType
		];

		$result = Db::name('user_token')->insert($data);

		return $result;
	}


	/**
	 * token 储存值
	 * @param $data
	 * @return int|string
	 */
	public function tokenData($data)
	{
		$tokenData = Db::name('user_token')->insert($data);
		return $tokenData;
	}

	/**
	 * 清理过期token
	 *
	 */
	public function clearExpireToken()
	{
		//获取用户Id
		$userId = cmf_get_current_user_id();

		//执行清理多余的Token
		$result = $this
			->where('user_id', $userId)
			->where('expire_time', '<', time())
			->delete();

		return $result;

	}

}