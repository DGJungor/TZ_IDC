<?php
// +----------------------------------------------------------------------
// | IDCKX
// +----------------------------------------------------------------------
// | Author:
// +----------------------------------------------------------------------
namespace app\tools\controller;

use cmf\controller\HomeBaseController;
use think\Db;
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

	/**
	 * 获取token数据  并判断有无绑定账号
	 *
	 * @author 张俊
	 * @throws \think\db\exception\DataNotFoundException
	 * @throws \think\db\exception\ModelNotFoundException
	 * @throws \think\exception\DbException
	 */
	public function getTokenData()
	{
		//实例化模型
		$userTokenModel = new UserTokenModel();

		//获取参数 token
		$token      = $this->request->param('token');
		$deviceType = $this->request->param('device_type');

		//获取token数据
		$tokenData = idckx_token_get($token);

		//判断token有没有过期  过期则删除
		if ($tokenData['expire_time'] < time()) {

			//删除过期token
			$res = $userTokenModel->destroy($tokenData['id']);

			return idckx_ajax_echo(null, 'token已过期', 0);

		} else {

			//根据设备类型判断有无绑定
			switch ($deviceType) {

				//微信登录
				case 'wx':
					$where = 'wechat';
					break;
				case 'qq':
					$where = 'qq';
					break;
				case 'weibo':
					$where = 'weibo';
					break;
				default:

					//未知类型
//					return "未知类型";
					break;
			}

			//查询用户有绑定
			$userInfo = Db::name('user_extension')->where($where, $tokenData['user_id'])->find();

			//判断 是否找到绑定的相关用户
			if (empty($userInfo)) {

				//空-------即没有相关绑定的账号
				$tokenData['is_bing'] = 0;

				return idckx_ajax_echo($tokenData, '无绑定', 1);

			} else {

				//不为空-----即有相关绑定的账号
				$tokenData['is_bing'] = 1;

				return idckx_ajax_echo($tokenData, '成功,并已绑定', 1);
			}

		}


	}

	public function test()
	{


	}

}
