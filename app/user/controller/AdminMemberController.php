<?php
// +----------------------------------------------------------------------
// | ThinkCMF [ WE CAN DO IT MORE SIMPLE ]
// +----------------------------------------------------------------------
// | Copyright (c) 2013-2017 http://www.thinkcmf.com All rights reserved.
// +----------------------------------------------------------------------
// | Licensed ( http://www.apache.org/licenses/LICENSE-2.0 )
// +----------------------------------------------------------------------
// | Author: Powerless < wzxaini9@gmail.com>
// +----------------------------------------------------------------------

namespace app\user\controller;

use app\tools\controller\AjaxController;
use app\user\model\UserExtensionModel;
use app\user\model\UserModel;
use cmf\controller\AdminBaseController;
use think\Db;

class AdminMemberController extends AdminBaseController
{

	/**
	 * 管理员获取用户数据
	 *
	 * @author 张俊
	 * @return \think\response\Json
	 * @throws \think\db\exception\DataNotFoundException
	 * @throws \think\db\exception\ModelNotFoundException
	 * @throws \think\exception\DbException
	 *
	 * 接口地址：user/AdminMember/getMemberData
	 * 参数：
	 *     user_id
	 * 返回参数
	 *      id用户ID
	 *      avatar用户头像
	 *      user_nickname用户昵称
	 *      user_name用户账号
	 *      microblog用户微博
	 *      user_email用户邮箱
	 *      user_truename真实姓名
	 *      mobile用户手机号码
	 *      qq用户QQ号码
	 */
	public function getMemberData()
	{

		//实例化ajax工具
		$ajaxTools = new AjaxController();

		//实例化模型
		$userExtensionModel = new UserExtensionModel();
		$userModel          = new UserModel();

		//从session 中获取用户ID
		$userId = $this->request->param('user_id');

		//根据用户ID 查询用户信息
		$userData          = $userModel->get($userId);
		$userExtensionData = $userExtensionModel->getUserExtension($userId);

		//拼装数据
		$data = [

			'id'            => $userId,
			'avatar'        => $userData['avatar'],
			'user_nickname' => $userData['user_nickname'],
			'user_name'     => $userData['user_login'],
			'user_email'    => $userData['user_email'],
			'mobile'        => $userData['mobile'],
			'microblog'     => $userExtensionData['weibo'],
			'user_truename' => $userExtensionData['user_truename'],
			'qq'            => $userExtensionData['qq'],
			'weChat'        => $userExtensionData['wechat']
		];

		//返回数据
		$info = $ajaxTools->ajaxEcho($data, '成功获取用户信息', 1);
		return $info;

	}


	/**
	 * 编辑用户信息
	 *
	 * @author 张俊
	 * @return \think\response\Json
	 *
	 * 接口地址：user/Member/setMemberData
	 * 参数：
	 *      nickname用户昵称
	 *      weibo用户微博
	 *      mailbox用户邮箱
	 *      name用户名称（真实姓名）
	 *      weChat用户微信
	 *      phone用户手机号码
	 *
	 */
	public function setMemberData()
	{
		//实例化ajax工具
		$ajaxTools = new AjaxController();

		//实例化模型
		$userModel          = new UserModel();
		$userExtensionModel = new UserExtensionModel();

		//获取用户id
		$userId = $this->request->param('user_id');

		//将需要修改的数据拼装成数组
		$userData          = [
			'user_email'    => $this->request->post('mailbox'),
			'mobile'        => $this->request->post('phone'),
			'user_nickname' => $this->request->post('nickname'),
		];
		$userExtensionData = [
			'user_truename' => $this->request->post('name'),
			'weibo'         => $this->request->post('weibo'),
			'wechat'        => $this->request->post('weChat'),
			//						'qq'            => 1231123,
		];

//		//启动事务处理
//		Db::startTrans();

		//修改数据库中的数据
		$userExtensionRes = $userExtensionModel->setUserExtension($userId, $userExtensionData);
		$userUserRes      = $userModel->setUser($userId, $userData);

//		if ($userExtensionRes && $userUserRes) {
//
//			//提交事务
//			Db::commit();
//		} else {
//
//			//事务回滚
//			Db::rollback();
//		}

		$info = $ajaxTools->ajaxEcho(null, '已修改', 1);
		return $info;

	}

}