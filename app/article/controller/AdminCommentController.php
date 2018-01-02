<?php

namespace app\article\controller;

use app\article\model\CommentModel;
use cmf\controller\AdminBaseController;
use think\Request;

/**
 * Class IndexController
 *
 * 控制器
 *
 * @author 张俊
 * @package app\article\controller
 *
 */
class AdminCommentController extends AdminBaseController
{


	public function index()
	{

		$commentModel = new CommentModel();
//		$test         = $commentModel->getAllComment();
//		dump($this->approve(1));
		$test = $commentModel->getDeleteTime(1);
		$test2 = $test['delete_time'];
		dump($test2);
	}


	/**
	 *
	 * 根据评论ID通过评论审核
	 *
	 * @author 张俊
	 * @param $commentId
	 * @param $result 1:修改状态成功 0:修改状态失败
	 * @return $this
	 */
	public function approve($commentId)
	{
		$commentModel = new CommentModel();
		$result       = $commentModel->approve($commentId);

		return $result;
	}


	/**
	 * 删除评论
	 * 添加删除时间
	 *
	 * @author 张俊
	 * @param $commentId
	 * @param $result 1:删除成功 0:删除失败
	 * @return $this|int
	 */
	public function delete($commentId)
	{
		$commentMode = new CommentModel();

		//获取当前时间作为删除时间
		$deleteTime = time();
		//根据评论ID 为评论添加删除时间
		$result = $commentMode->deleteComment($commentId,$deleteTime);

		return $result;
	}


}