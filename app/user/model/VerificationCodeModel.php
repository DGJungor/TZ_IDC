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
	public function queryCode($account)
	{

//		$code = $this->


	}


	/**
	 * 添加验证码
	 *
	 * 默认  1000 到 9999   4位数的随机验证码    默认过期时间   为一天后
	 * @author ZhangJun
	 */
	public function addCode($account = null, $code = null)
	{
		$data = [
			'account'     => $account,
			'count'       => 1,
			'send_time'   => time(),
			'code'        => $code,
			'expire_time' => strtotime('+1day'),
		];
		$res  = $this->save($data);

		return $res;

	}


	/**
	 * 更新验证码
	 *
	 * @author ZhangJun
	 */
	public function updateCode($account = null, $code = null)
	{
		$data = $this->where('account', $account)->find();

		if ($data['send_time'] > strtotime('-1day')) {


		}

		$t = $this->where('account', $account)
			->setInc('count');

		return $t;

	}


	/**
	 * @param $userId
	 * @param $token
	 * @param int $expireTime
	 * @param string $deviceType
	 * @return int|string
	 */
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