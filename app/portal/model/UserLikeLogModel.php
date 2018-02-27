<?php

namespace app\portal\model;

use think\Model;
use think\Db;

class UserLikeLogModel extends Model
{

	//配置主键字段位ID
	protected $pk = 'id';

	protected $autoWriteTimestamp = true;


	public function addUserLikeLog($ip = null, $postId = null)
	{

		//封装函数
		$data = [
			'ip'      => $ip,
			'post_id' => $postId,
		];

		//添加点赞记录
		$result = $this->data($data)->save();

		return $result;
	}


}
