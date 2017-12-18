<?php

namespace app\article\model;

use think\Model;
use think\Db;

/**
 * Class ReferralsModel
 *
 * 前台产品推荐 模型
 *
 * @author 张俊
 * @package app\article\model
 */
class ReferralsModel extends Model
{


	//配置主键字段位ID
	protected $pk = 'id';

	/**
	 *前台文章页面 产品推荐按照数量 状态获取
	 *
	 * @param int $num '查询数量'
	 * @return false|\PDOStatement|string|\think\Collection
	 * @throws \think\db\exception\DataNotFoundException
	 * @throws \think\db\exception\ModelNotFoundException
	 * @throws \think\exception\DbException
	 *
	 */
	public function getReferrals($num=3)
	{
		//数据库查询数据
		$referralsData = $this
			->where('state',1)
			->limit($num)
			->order('create_time desc')
			->select();

		return $referralsData;
	}


}