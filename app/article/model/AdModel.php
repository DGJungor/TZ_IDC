<?php

namespace app\article\model;

use think\Model;
use think\Db;

/**
 * Class AdModel
 *
 * 前台文章模块  广告模型
 *
 * @author  张俊
 * @package app\article\model
 *
 */
class AdModel extends Model
{

	//配置主键字段位ID
	protected $pk = 'id';


	/**
	 *
	 * 根据广告位id,获取最新一条状态为开启的广告数据
	 *
	 * @author 张俊
	 * @param $siteId  '广告位ID'
	 * @return array|false|\PDOStatement|string|Model
	 * @throws \think\db\exception\DataNotFoundException
	 * @throws \think\db\exception\ModelNotFoundException
	 * @throws \think\exception\DbException
	 */
	public function getAd($siteId)
	{

		//根据广告位id,获取最新一条状态为开启的广告数据
		$adData = $this
			->where('ad_site_id',$siteId)
			->where('ad_state',1)
			->order('create_time desc')
			->find();

		return $adData;
	}

}