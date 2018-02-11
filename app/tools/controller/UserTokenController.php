<?php
// +----------------------------------------------------------------------
// | IDCKX
// +----------------------------------------------------------------------
// | Author:
// +----------------------------------------------------------------------
namespace app\tools\controller;

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

	/**
	 * 添加Token
	 *
	 * 参数：
	 *        openid
	 *        access_token
	 *        type
	 *
	 */
	public function addToken()
	{
		//实例化模型
		$userTokenModel = new UserTokenModel();

		//获取参数
		$par = $this->request->param();

		$res = idckx_token_add($par['openid'], $par['access_token'], $par['expire_time'], $par['type']);

		if ($res == 1) {
			return idckx_ajax_echo(null, '添加成功', 1);
		} else {
			return idckx_ajax_echo(null, '添加失败', 0);
		}

	}

}
