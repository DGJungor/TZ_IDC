<?php

namespace app\tools\model;

use think\Db;
use think\Model;

class PluginReptilePostModel extends Model
{

	/**
	 * 记录已提交
	 */
	public function editPushState($postId)
	{

		$res = $this
			->where('post_id', $postId)
			->failException(false)
			->update([
				'is_post' => 1,
			]);

		return $res;
	}


	/**
	 * 查询表中有无字段
	 */
	public function queryPush($postId)
	{

	}


}