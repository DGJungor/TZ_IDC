<?php

namespace app\tools\model;

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
	public function getUserId($token)
	{
		$result = $this
			->where('token',$token)
			->find();

		return $result;
	}


}