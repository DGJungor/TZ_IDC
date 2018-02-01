<?php
// +----------------------------------------------------------------------
// | IDCKX
// +----------------------------------------------------------------------
// | Author:
// +----------------------------------------------------------------------
namespace app\common\controller;

use cmf\controller\HomeBaseController;
use think\Loader;
use app\tools\model\UserTokenModel;

class UserTokenController extends HomeBaseController
{

	/**
	 *对token进行判断
	 *
	 * @author 张俊
	 * @param null $token
	 * @return array
	 * @throws \think\db\exception\DataNotFoundException
	 * @throws \think\db\exception\ModelNotFoundException
	 * @throws \think\exception\DbException
	 *
	 */
	function byTokenGetUser($token = null)
	{
		//实例化模型
		$userTokenModel = new UserTokenModel();

		//用token 获取相关的用户ID 以及相关信息
		$result = $userTokenModel->getUserId($token);

		//对token值进行判断
		if (!is_null($result)) {

			//判断是否过期
			if (!$result["expire_time"] < time()) {
				return ["userId" => $result["user_id"], "msg" => "获取成功"];
			} else {
				return ["userId" => -1, "msg" => "token已过期"];
			}

		} else {
			return ["userId" => -1, "msg" => "token不存在"];
		}
	}

}
