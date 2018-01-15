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
			"expire_time" => time()+$expireTime,
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

}