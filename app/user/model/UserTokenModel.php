<?php

namespace app\user\model;

use think\Db;
use think\Model;

class UserTokenModel extends Model
{


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