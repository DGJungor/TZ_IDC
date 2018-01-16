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
	 * @param int  $userType
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

}