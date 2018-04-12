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
namespace app\user\model;

use think\Db;
use think\Model;

class UserModel extends Model
{
	//配置主键字段位ID
	protected $pk = 'id';

	public function doMobile($user)
	{
		$userQuery = Db::name("user");

		$result = $userQuery->where('mobile', $user['mobile'])->find();


		if (!empty($result)) {
			$comparePasswordResult = cmf_compare_password($user['user_pass'], $result['user_pass']);
			$hookParam             = [
				'user'                    => $user,
				'compare_password_result' => $comparePasswordResult
			];
			hook_one("user_login_start", $hookParam);
			if ($comparePasswordResult) {
				//拉黑判断。
				if ($result['user_status'] == 0) {
					return 3;
				}
				session('user', $result);
				$data = [
					'last_login_time' => time(),
					'last_login_ip'   => get_client_ip(0, true),
				];
				$userQuery->where('id', $result["id"])->update($data);
				return 0;
			}
			return 1;
		}
		$hookParam = [
			'user'                    => $user,
			'compare_password_result' => false
		];
		hook_one("user_login_start", $hookParam);
		return 2;
	}

	public function doName($user)
	{
		$userQuery = Db::name("user");

		$result = $userQuery->where('user_login', $user['user_login'])->find();
		if (!empty($result)) {
			$comparePasswordResult = cmf_compare_password($user['user_pass'], $result['user_pass']);
			$hookParam             = [
				'user'                    => $user,
				'compare_password_result' => $comparePasswordResult
			];
			hook_one("user_login_start", $hookParam);
			if ($comparePasswordResult) {
				//拉黑判断。
				if ($result['user_status'] == 0) {
					return 3;
				}
				session('user', $result);
				$data = [
					'last_login_time' => time(),
					'last_login_ip'   => get_client_ip(0, true),
				];
				$userQuery->where('id', $result["id"])->update($data);
				return 0;
			}
			return 1;
		}
		$hookParam = [
			'user'                    => $user,
			'compare_password_result' => false
		];
		hook_one("user_login_start", $hookParam);
		return 2;
	}

	public function doEmail($user)
	{

		$userQuery = Db::name("user");

		$result = $userQuery->where('user_email', $user['user_email'])->find();


		if (!empty($result)) {
			$comparePasswordResult = cmf_compare_password($user['user_pass'], $result['user_pass']);
			$hookParam             = [
				'user'                    => $user,
				'compare_password_result' => $comparePasswordResult
			];
			hook_one("user_login_start", $hookParam);
			if ($comparePasswordResult) {

				//拉黑判断。
				if ($result['user_status'] == 0) {
					return 3;
				}
				session('user', $result);
				$data = [
					'last_login_time' => time(),
					'last_login_ip'   => get_client_ip(0, true),
				];
				$userQuery->where('id', $result["id"])->update($data);
				return 0;
			}
			return 1;
		}
		$hookParam = [
			'user'                    => $user,
			'compare_password_result' => false
		];
		hook_one("user_login_start", $hookParam);
		return 2;
	}

	public function registerEmail($user)
	{
		$userQuery = Db::name("user");
		$result    = $userQuery->where('user_email', $user['user_email'])->find();

		$userStatus = 1;

		if (cmf_is_open_registration()) {
			$userStatus = 2;
		}

		if (empty($result)) {
			$data   = [
				'user_login'      => '',
				'user_email'      => $user['user_email'],
				'mobile'          => '',
				'user_nickname'   => '',
				'user_pass'       => cmf_password($user['user_pass']),
				'last_login_ip'   => get_client_ip(0, true),
				'create_time'     => time(),
				'last_login_time' => time(),
				'user_status'     => $userStatus,
				"user_type"       => 2,
			];
			$userId = $userQuery->insertGetId($data);
			$date   = $userQuery->where('id', $userId)->find();
			cmf_update_current_user($date);
			return 0;
		}
		return 1;
	}

	public function registerMobile($user)
	{
		$result = Db::name("user")->where('mobile', $user['mobile'])->find();

		$userStatus = 1;

		if (cmf_is_open_registration()) {
			$userStatus = 2;
		}

		if (empty($result)) {
			$data   = [
				'user_login'      => '',
				'user_email'      => '',
				'mobile'          => $user['mobile'],
				'user_nickname'   => '',
				'user_pass'       => cmf_password($user['user_pass']),
				'last_login_ip'   => get_client_ip(0, true),
				'create_time'     => time(),
				'last_login_time' => time(),
				'user_status'     => $userStatus,
				"user_type"       => 2,//会员
			];
			$userId = Db::name("user")->insertGetId($data);
			$data   = Db::name("user")->where('id', $userId)->find();
			cmf_update_current_user($data);
			return 0;
		}
		return 1;
	}

	/**
	 * 通过邮箱重置密码
	 * @param $email
	 * @param $password
	 * @return int
	 */
	public function emailPasswordReset($email, $password)
	{
		$result = $this->where('user_email', $email)->find();
		if (!empty($result)) {
			$data = [
				'user_pass' => cmf_password($password),
			];
			$this->where('user_email', $email)->update($data);
			return 0;
		}
		return 1;
	}

	/**
	 * 通过手机重置密码
	 * @param $mobile
	 * @param $password
	 * @return int
	 */
	public function mobilePasswordReset($mobile, $password)
	{
		$userQuery = Db::name("user");
		$result    = $userQuery->where('mobile', $mobile)->find();
		if (!empty($result)) {
			$data = [
				'user_pass' => cmf_password($password),
			];
			$userQuery->where('mobile', $mobile)->update($data);
			return 0;
		}
		return 1;
	}

	public function editData($user)
	{
		$userId                = cmf_get_current_user_id();
		$data['user_nickname'] = $user['user_nickname'];
		$data['sex']           = $user['sex'];
		$data['birthday']      = strtotime($user['birthday']);
		$data['user_url']      = $user['user_url'];
		$data['signature']     = $user['signature'];
		$userQuery             = Db::name("user");
		if ($userQuery->where('id', $userId)->update($data)) {
			$userInfo = $userQuery->where('id', $userId)->find();
			cmf_update_current_user($userInfo);
			return 1;
		}
		return 0;
	}

	/**
	 * 用户密码修改
	 * @param $user
	 * @return int
	 */
	public function editPassword($user)
	{
		$userId    = cmf_get_current_user_id();
		$userQuery = Db::name("user");
		if ($user['password'] != $user['repassword']) {
			return 1;
		}
		$pass = $userQuery->where('id', $userId)->find();
		if (!cmf_compare_password($user['old_password'], $pass['user_pass'])) {
			return 2;
		}
		$data['user_pass'] = cmf_password($user['password']);
		$userQuery->where('id', $userId)->update($data);
		return 0;
	}

	public function comments()
	{
		$userId               = cmf_get_current_user_id();
		$userQuery            = Db::name("Comment");
		$where['user_id']     = $userId;
		$where['delete_time'] = 0;
		$favorites            = $userQuery->where($where)->order('id desc')->paginate(10);
		$data['page']         = $favorites->render();
		$data['lists']        = $favorites->items();
		return $data;
	}

	public function deleteComment($id)
	{
		$userId              = cmf_get_current_user_id();
		$userQuery           = Db::name("Comment");
		$where['id']         = $id;
		$where['user_id']    = $userId;
		$data['delete_time'] = time();
		$userQuery->where($where)->update($data);
		return $data;
	}

	/**
	 * 绑定用户手机号
	 */
	public function bindingMobile($user)
	{
		$userId          = cmf_get_current_user_id();
		$data ['mobile'] = $user['username'];
		Db::name("user")->where('id', $userId)->update($data);
		$userInfo = Db::name("user")->where('id', $userId)->find();
		cmf_update_current_user($userInfo);
		return 0;
	}

	/**
	 * 绑定用户邮箱
	 */
	public function bindingEmail($user)
	{
		$userId              = cmf_get_current_user_id();
		$data ['user_email'] = $user['username'];
		Db::name("user")->where('id', $userId)->update($data);
		$userInfo = Db::name("user")->where('id', $userId)->find();
		cmf_update_current_user($userInfo);
		return 0;
	}

	/**
	 * 添加用户模型
	 *
	 * @author 张俊
	 * @param     $data
	 * @param int $userType
	 * @param int $userStatus
	 * @return false|int
	 */
	public function addUser($data, $userType = 2, $userStatus = 2)
	{
		$createData = [
			'user_type'     => $userType,
			'user_login'    => $data['username'],
			'user_pass'     => cmf_password($data['password']),
			'mobile'        => $data['mobile'],
			'user_nickname' => $data['nickname'],
			'user_email'    => $data['email'],
			'user_status'   => $userStatus,
			'avatar'        => '/avatar.jpg',
		];

		$result = $this
			->data($createData)
			->save();

		return $this->id;


	}

	/**
	 * 查询是否有重复的账户名
	 *
	 * @author 张俊
	 * @param $username '要查询的账户名'
	 * @return array|false|\PDOStatement|string|Model
	 * @throws \think\db\exception\DataNotFoundException
	 * @throws \think\db\exception\ModelNotFoundException
	 * @throws \think\exception\DbException
	 *
	 */
	public function existUserLogin($username)
	{
		$result = $this
			->where('user_login', $username)
			->field('id,user_login')
			->find();

		return $result;
	}


	/**
	 * 根据用户的登录
	 *
	 * @author 张俊
	 * @param $username
	 * @return array|false|\PDOStatement|string|Model
	 * @throws \think\db\exception\DataNotFoundException
	 * @throws \think\db\exception\ModelNotFoundException
	 * @throws \think\exception\DbException
	 *
	 */
	public function queryUser($username)
	{
		$result = $this
			->where('user_login', $username)
			->find();

		return $result;
	}


	/**
	 * 根据用户ID数据  修改个人数据
	 *
	 * @param      $userId
	 * @param null $data
	 * @return false|int
	 *
	 */
	public function setUser($userId, $data = null)
	{

		//修改用户信息
		$result = $this->save($data, ['id' => $userId]);

		return $result;
	}


	/**
	 * 修改当前用户密码
	 *
	 * @param $newPassword
	 * @return false|int
	 *
	 */
	public function changePassword($newPassword)
	{

		$userId = cmf_get_current_user_id();

		$result = $this->save([
			'user_pass' => cmf_password($newPassword)
		], ['id' => $userId]);

		return $result;
	}


	/**
	 * 查询用户是否有绑定相关平台帐号
	 *
	 * 有绑定的就返回本站帐号id  没有则返回false
	 *
	 * @author ZhangJun
	 * @param null $openId
	 * @param string $platform 平台名称   默认:微信     微信:wechat  qq:qq  新浪微博: weibo
	 * @return array|bool|false|\PDOStatement|string|Model
	 * @throws \think\db\exception\DataNotFoundException
	 * @throws \think\db\exception\ModelNotFoundException
	 * @throws \think\exception\DbException
	 */
	public function queryBinding($openId = null, $platform = 'wechat')
	{

		// 查询数据库
		$res = Db::name("user_extension")->where($platform, $openId)->field('user_id')->find();

		//  判断查询结果  有数据返回本站帐号ID  查不到数据返回空
		if ($res) {
			return $res;
		} else {
			return false;
		}

	}

	/**
	 * 第三方登录模型
	 *
	 * @param $userId
	 * @return int  //返回的为状态码    0:登录成功  1:帐号被拉黑   2:未能获取到账号信息
	 * @throws \think\Exception
	 * @throws \think\db\exception\DataNotFoundException
	 * @throws \think\db\exception\ModelNotFoundException
	 * @throws \think\exception\DbException
	 * @throws \think\exception\PDOException
	 */
	public function doLoginByOpenAccount($userId)
	{
		$userQuery = Db::name("user");

		$result = $userQuery->where('id', $userId)->find();

		//判断数据是否为空
		if (!empty($result)) {

			//拉黑判断。
			if ($result['user_status'] == 0) {
				return 1;
			}

			//将登陆信息保存到  session中
			session('user', $result);
			$data = [
				'last_login_time' => time(),
				'last_login_ip'   => get_client_ip(0, true),
			];
			$userQuery->where('id', $result["id"])->update($data);
			return 0;

		}
		return 2;


	}
}
