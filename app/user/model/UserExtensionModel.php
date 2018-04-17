<?php

namespace app\user\model;

use think\Db;
use think\Model;

class UserExtensionModel extends Model
{

	//配置主键字段位ID
	protected $pk = 'id';


	/**
	 * 用户id  为用户扩展表添加空行
	 *
	 * @param null $userId
	 * @param int $userType
	 * @return false|int
	 *
	 */
	public function addUserExtension($userId = null, $userType = 2)
	{
		$createData = [
			'user_id'   => $userId,
			'user_type' => $userType,
		];

		$result = $this
			->data($createData)
			->save();

		return $this->id;
	}

	/**
	 * 根据用户id  查询用户扩展表数据
	 *
	 * @param null $userId
	 * @return array|false|\PDOStatement|string|Model
	 * @throws \think\db\exception\DataNotFoundException
	 * @throws \think\db\exception\ModelNotFoundException
	 * @throws \think\exception\DbException
	 *
	 */
	public function getUserExtension($userId = null)
	{

		$result = $this
			->where('user_id', $userId)
			->find();

		return $result;
	}

	/**
	 * 根据用户ID与数据  修改用户个人信息
	 *
	 * @param      $userId
	 * @param null $data
	 * @return false|int
	 */
	public function setUserExtension($userId, $data = null)
	{

		//修改用户信息
		$result = $this->save($data, ['user_id' => $userId]);

		return $result;
	}

	/**
	 * 前台会员 绑定第三方账号
	 *
	 * @param null $userId
	 * @param null $deviceType
	 * @param null $openId
	 * @return false|int
	 *
	 */
	public function bindUserOpenAccount($userId = null, $deviceType = null, $openId = null)
	{

//		$userData = $this->get(['user_id' => $userId]);

		$res = $this->save([
			$deviceType => $openId,
		], ['user_id' => $userId]);


		return $res;

	}

	/**
	 * 解除第三方绑定
	 *
	 * @author ZhangJun
	 */
	public function removeBinding($deviceType)
	{


		$res = $this->save([
			$deviceType => null,
		], ['user_id' => cmf_get_current_user_id()]);


		return $res;

	}

}