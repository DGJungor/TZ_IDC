<?php

namespace app\article\controller;

use app\article\model\CommentModel;
use cmf\controller\HomeBaseController;
use think\Request;

/**
 * Class CommentController
 * 前台评论可控制器
 *
 * @author 张俊
 * @package app\article\controller
 *
 */
class CommentController extends HomeBaseController
{

	public function index()
	{
		$commentMode= new CommentModel();
		$test = $commentMode->getComment(1);

		dump($test);
	}

	/**
	 * 根据内容ID 获取有关的评论信息
	 *
	 * @author 张俊
	 * @param $objectId
	 *
	 */
	public function getComment($objectId)
	{
		$commentModel= new CommentModel();
		$commentData = $commentModel->getComment($objectId);

		dump($commentData);

	}

}
