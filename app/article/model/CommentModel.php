<?php

namespace app\article\model;

use think\Model;
use think\Db;

/**
 * Class CommentModel
 *
 * 评论模型
 *
 * @author 张俊
 * @package app\article\model
 *
 */
class CommentModel extends Model
{

	/**
	 *
	 * 分页查询所有数据
	 *
	 * @author 张俊
	 * @param int $limit '开始的行数'
	 * @param int $num '获取数量'
	 * @return false|\PDOStatement|string|\think\Collection
	 * @throws \think\db\exception\DataNotFoundException
	 * @throws \think\db\exception\ModelNotFoundException
	 * @throws \think\exception\DbException
	 *
	 */
	public function getAllComment($limit = 0, $num = 10)
	{
		$allCommentData = $this
			->limit($limit, $num)
			->select();

		return $allCommentData;
	}

	/**
	 *
	 * 通过审核评论内容
	 *
	 * @param  int $commentId '要通过审核的评论id'
	 * @return $this
	 *
	 */
	public function approve($commentId)
	{
		$result = $this
			->where('id', $commentId)
			->update(['status' => 1]);

		return $result;
	}

	/**
	 * 删除评论
	 * 在评论中增加删除时间
	 *
	 * @author 张俊
	 * @param $commentId
	 * @param $deleteTime
	 * @return $this|int
	 */
	public function deleteComment($commentId, $deleteTime)
	{
		$result = $this
			->where('id', $commentId)
			->update(['delete_time' => $deleteTime]);

		return $result;
	}

	/**
	 * 查询与内容ID关联的 状态为已审阅(可修改,可传参)的评论数据
	 *
	 * @author 张俊
	 * @param       $objectId '内容ID'
	 * @param array $status
	 * @return false|\PDOStatement|string|\think\Collection
	 * @throws \think\db\exception\DataNotFoundException
	 * @throws \think\db\exception\ModelNotFoundException
	 * @throws \think\exception\DbException
	 *
	 */
	public function getComment($objectId,$status=[1])
	{
		$commentData = $this
			->where('object_id',$objectId)
			->where('status','in',$status)
			->select();

		return $commentData;
	}

	/**
	 * 根据评论id 获取删除时间
	 *
	 * @author 张俊
	 * @param $commentId
	 * @return array|false|\PDOStatement|string|Model
	 * @throws \think\db\exception\DataNotFoundException
	 * @throws \think\db\exception\ModelNotFoundException
	 * @throws \think\exception\DbException
	 */
	public function getDeleteTime($commentId)
	{
		$deleteTime = $this
			->field('id,delete_time')
			->where('id', $commentId)
			->find();

		return $deleteTime;
	}


}