<?php

namespace app\user\model;

use think\Db;
use think\Model;


class VerificationCodeModel extends Model
{


	/**
	 *   查询验证码
	 *
	 */
	public function queryCode()
	{


	}


	/**
	 * 添加验证码
	 *
	 * @author ZhangJun
	 */
	public function addCode($account = null, $code = null, $expire_time = null)
	{
		$data = [
			'count'     => 0,
			'send_time' => time(),
		];


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
	 * 清理所有过期code
	 *
	 * @author ZhangJun
	 */
	public function cleanExpireCode()
	{

		$res = $this
			->where('expire_time', '<', time())
			->delete();

		return $res;


	}

}