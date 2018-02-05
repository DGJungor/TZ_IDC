<?php

namespace app\common\model;

use think\Db;
use think\Model;

/**
 * 系统消息模型
 *
 * Class MessageModel
 * @author 张俊
 * @package app\common\model
 *
 */
class MessageModel extends Model
{

	//配置主键字段位ID
	protected $pk = 'id';

	//开始时间戳
	protected $autoWriteTimestamp = true;


	public function addUserSystemMessage()
	{



	}


}