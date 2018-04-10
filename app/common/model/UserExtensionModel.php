<?php

namespace app\common\model;

use think\Db;
use think\Model;


class UserExtensionModel extends Model
{
	/**
	 * 查询用户绑定信息
	 *
	 * @param $type //类型
	 * @param $openId //openId
	 * @return array|false|\PDOStatement|string|Model
	 * @throws \think\db\exception\DataNotFoundException
	 * @throws \think\db\exception\ModelNotFoundException
	 * @throws \think\exception\DbException
	 */
	public function queryBinding($type, $openId)
	{

		$result = $this
			->where($type, $openId)
			->find();

		return $result;
	}
}