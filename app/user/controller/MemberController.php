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

use app\user\model\PortalCategoryModel;
use app\user\model\PortalCategoryPostModel;
use app\user\model\UserExtensionModel;
use app\user\model\PortalPostModel;
use app\user\model\UserFavoriteModel;
use cmf\controller\UserBaseController;
use think\Cache;
use think\Db;
use think\Loader;
use think\Session;
use think\Validate;
use app\user\model\UserModel;
use app\tools\controller\AjaxController;
use app\user\model\UserTokenModel;

class MemberController extends UserBaseController
{


	/**
	 *获取用户个人信息
	 *
	 * @author 张俊
	 * @return \think\response\Json
	 * @throws \think\db\exception\DataNotFoundException
	 * @throws \think\db\exception\ModelNotFoundException
	 * @throws \think\exception\DbException
	 *
	 * 接口地址：user/Member/getMemberData
	 * 参数：
	 *     无
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
//		$ajaxTools = new AjaxController();

		//实例化模型
		$userExtensionModel = new UserExtensionModel();
		$userModel          = new UserModel();

		//从session 中获取用户ID
		$userId = Session('user.id');

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
//		$info = $ajaxTools->ajaxEcho($data, '成功获取用户信息', 1);
//		return $info;
		return idckx_ajax_echo($data, '成功获取用户信息', 1);
	}

	/**
	 * 编辑用户信息
	 *
	 * @author 张俊
	 * @return \think\response\Json
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
		$userId = cmf_get_current_user_id();

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
			'qq'            => $this->request->post('qq'),
		];


		//修改数据库中的数据
		$userExtensionRes = $userExtensionModel->setUserExtension($userId, $userExtensionData);
		$userUserRes      = $userModel->setUser($userId, $userData);

		$info = $ajaxTools->ajaxEcho(null, '已修改', 1);
		return $info;

	}

	/**
	 * 获取用户发布的文章
	 *
	 * @author 张俊
	 * @return \think\response\Json
	 * @throws \think\db\exception\DataNotFoundException
	 * @throws \think\db\exception\ModelNotFoundException
	 * @throws \think\exception\DbException
	 *
	 * 接口地址：user/Member/getArticle
	 * 参数：无
	 * 请求类型：GET
	 * 返回参数：
	 *         Array类型 [
	 *              {aid,title,status,comment_count,link}
	 *              {aid,title,status,comment_count,link}
	 *          ]
	 *          aid是文章ID
	 *          title：是文章标题
	 *          status：是文章状态
	 *          comment_count：文章评论数量
	 *          link：文章链接  (cmf_url('portal/Article/index',['id'=>1]))
	 *
	 */
	public function getArticle()
	{
		//实例化ajax工具
		$ajaxTools = new AjaxController();

		//获取用户ID
		$userId = cmf_get_current_user_id();

		//实例化模型
		$potalPostModel         = new PortalPostModel();
		$potalCategoryPostModel = new PortalCategoryPostModel();

		//根据用户id  获取用户的文章信息
		$result = $potalPostModel->getUserArticle($userId);

		//判断用户有文章数据
		if (!empty($result[0])) {

			//重新拼装成新数组
			foreach ($result as $value => $item) {
				$data[$value]['aid']           = $item['id'];
				$data[$value]['title']         = $item['post_title'];
				$data[$value]['status']        = $item['post_status'];
				$data[$value]['comment_count'] = $item['comment_count'];
				$data[$value]['link']          = cmf_url('portal/Article/index', [
					'id'  => $item['id'],
					'cid' => $potalCategoryPostModel->getCategoryId($item['id']),
				]);
			}

			$info = $ajaxTools->ajaxEcho($data, '获取用户文章信息', 1);
			return $info;
		} else {

			$info = $ajaxTools->ajaxEcho(null, '无文章信息', 0);
			return $info;
		}

	}

	/**
	 *获取用户收藏的文章
	 *
	 * @author 张俊
	 * @return \think\response\Json
	 * @throws \think\db\exception\DataNotFoundException
	 * @throws \think\db\exception\ModelNotFoundException
	 * @throws \think\exception\DbException
	 *
	 * 接口地址：user/Member/getCollection
	 * 参数：无
	 * 请求类型：GET
	 * 返回参数：
	 *          Array类型 [
	 *              {title,date,link}
	 *          ]
	 *          title：是文章标题
	 *          date:文章收藏时间
	 *          link：文章链接
	 */
	public function getCollection()
	{

		//实例化ajax工具
		$ajaxTools = new AjaxController();

		//实例化模型
		$userFavoriteModel      = new UserFavoriteModel();
		$potalCategoryPostModel = new PortalCategoryPostModel();

		//获取前台登录的id
		$userId = cmf_get_current_user_id();

		//获取收藏文章信息
		$postData = $userFavoriteModel->getUserFavorite($userId);

		//判断用户有无收藏列表
		if (!empty($postData[0])) {

			//重新拼装成新数组
			foreach ($postData as $value => $item) {
				$data[$value]['id']    = $item['object_id'];
				$data[$value]['title'] = $item['title'];
				$data[$value]['date']  = $item['create_time'];
				$data[$value]['link']  = cmf_url('portal/Article/index', [
					'id'  => $item['object_id'],
					'cid' => $potalCategoryPostModel->getCategoryId($item['object_id']),
				]);
			}

			$info = $ajaxTools->ajaxEcho($data, '获取用户文章信息', 1);
			return $info;
		} else {

			$info = $ajaxTools->ajaxEcho(null, '无文章信息', 0);
			return $info;
		}

	}


	/**
	 * 删除用户收藏的文章
	 *
	 * @author 张俊
	 * @return \think\response\Json
	 *
	 * 接口地址：user/Member/delCollection
	 * 参数：
	 *     id
	 * 返回参数
	 *       无（状态设为1成功即可）
	 */
	public function delCollection()
	{
		//实例化ajax工具
		$ajaxTools = new AjaxController();

		//实例化模型
		$userFavoriteModel = new UserFavoriteModel();

		//获取需要删除的文章ID
		$delPostId = $this->request->param('id');

		//获取用户ID
		$userId = cmf_get_current_user_id();

		//执行删操作
		$delResult = $userFavoriteModel->delUserFavorite($delPostId, $userId);

		//判断是否删除成功
		if ($delResult) {
			$info = $ajaxTools->ajaxEcho(null, '删除成功', 1);
		} else {
			$info = $ajaxTools->ajaxEcho(null, '删除失败', 0);
		}

		return $info;

	}

	/**
	 * 前台用户 修改密码
	 *
	 * @author 张俊
	 * @return \think\response\Json
	 * @throws \think\db\exception\DataNotFoundException
	 * @throws \think\db\exception\ModelNotFoundException
	 * @throws \think\exception\DbException
	 *
	 * 接口地址：user/Member/changePassword
	 * 参数：
	 *      id用户ID（这个可以不用）
	 *      oldPassword用户旧密码
	 *      password用户新设置的密码
	 * 返回参数：
	 *          更改成成功状态即可
	 */
	public function changePassword()
	{
		//实例化ajax工具
		$ajaxTools = new AjaxController();

		//实例化模型
		$userModel = new UserModel();

		//获取用户信息
		$userData = $userModel->where('id', cmf_get_current_user_id())->find();

		//获取接收到的数据信息
		$data = $this->request->param();

		//生成验证器
		$changePasswordValidate = Loader::validate('ChangePassword');

		//验证新密码规则是否正确
		if ($changePasswordValidate->check($data)) {

			//判断原密码是否正确
			if (cmf_compare_password($data['oldPassword'], $userData['user_pass'])) {
				//修改密码并判断是否成功
				if ($userModel->changePassword($data['password'])) {
					$info = $ajaxTools->ajaxEcho(null, '修改成功', 1);
					return $info;
				} else {
					$info = $ajaxTools->ajaxEcho(null, '修改失败', 0);
					return $info;
				}
			} else {
				$info = $ajaxTools->ajaxEcho(null, '原密码错误', 0);
				return $info;
			}

		} else {
			$validateResultInfo = $changePasswordValidate->getError();
			$info               = $ajaxTools->ajaxEcho(null, $validateResultInfo, 0);
			return $info;
		}

	}


	/**
	 * 获取栏目信息
	 *
	 * @author 张俊
	 * @return \think\response\Json
	 * @throws \think\db\exception\DataNotFoundException
	 * @throws \think\db\exception\ModelNotFoundException
	 * @throws \think\exception\DbException
	 *
	 * 接口地址：user/Member/getCategory
	 * 参数：
	 *      name栏目名称（暂时只会传IDC新闻，后期需要弄成后台能设置可用户发布的栏目）
	 *      sub是否有下级栏目  1是   0不是
	 * 返回参数
	 *      Array类型 [
	 *          {id,name}
	 *      ]
	 *      id栏目ID
	 *      name栏目名称
	 */
	public function getCategory()
	{

		//实例化Ajax工具
		$ajaxTools = new AjaxController();

		//实例化模型
		$portalCategoryModel = new PortalCategoryModel();

		//获取参数
		$data = $this->request->param();

		//获取主栏目数据 并重新拼装成新数组
		$categoryData = $portalCategoryModel->getCategoryByName($data['name']);

		//根据是否为下级栏目作出不同的的操作
		switch ($data['sub']) {

			//1有下级栏目  返回下级栏目
			case 1:

				//根据父栏目获取 下级栏目信息
				$categoryData = $portalCategoryModel->getCategoryByParentId($categoryData['id']);

				//根据需要的数据拼装数组
				foreach ($categoryData as $value => $item) {
					$result[$value]['id']   = $item['id'];
					$result[$value]['name'] = $item['name'];
				}

				$info = $ajaxTools->ajaxEcho($result, '成功', 1);
				return $info;

				break;

			//0无下级栏目  返回当前栏目信息.
			case 0:

				$result[] = [
					'id'   => $categoryData['id'],
					'name' => $categoryData['name'],
				];

				$info = $ajaxTools->ajaxEcho($result, '成功', 1);
				return $info;

				break;

			//错误请求请求
			default:
				$info = $ajaxTools->ajaxEcho(null, '错误请求', 0);
				return $info;
		}

	}


	/**
	 * 用户发表文章
	 *
	 * @author 张俊
	 * @return \think\response\Json
	 *
	 * 接口地址：user/Member/postArticle
	 * 参数：
	 *      typeid栏目ID
	 *      title文章标题
	 *      content文章内容
	 *      descriptions文章描述
	 * 返回参数：
	 *          返回成功状态即可
	 *
	 */
	public function postArticle()
	{

		//实例化Ajax工具
		$ajaxTools = new AjaxController();

		//实例化模型
		$portalPostModel = new PortalPostModel();
//		$portalCategoryPostModel = new PortalCategoryPostModel();

		//获取用户id
		$userId = cmf_get_current_user_id();

		//获取参数数据
		$postData = $this->request->param();

		//启动事务处理
		Db::startTrans();

		//拼装成添加文章表中的数组
		$createPostData = [
			'user_id'        => $userId,
			'post_title'     => $postData['title'],
			'post_excerpt'   => $postData['descriptions'],
			'post_content'   => $portalPostModel->setPostContentAttr($postData['content']),
			'post_status'    => 0,
			'published_time' => time(),
		];

		//添加文章信息 成功则返回一个文章ID
		$portalId = $portalPostModel->postArticle($createPostData);

		//添加文章分类信息
		$portalCategoryPost = portalCategoryPostModel::create([
			'post_id'     => $portalId,
			'category_id' => $postData['typeid'],
		]);
		//获取文章分类ID
		$portalCategoryId = $portalCategoryPost->id;

		if ($portalId && $portalCategoryId) {
			//提交事务
			Db::commit();

			$info = $ajaxTools->ajaxEcho(null, '添加成功', 1);
			return $info;

		} else {
			//事务回滚
			Db::rollback();

			$info = $ajaxTools->ajaxEcho(null, '添加失败', 0);
			return $info;
		}

	}


	/**
	 * 前台用户删除文章
	 *
	 * @throws \think\Exception
	 * @throws \think\db\exception\DataNotFoundException
	 * @throws \think\db\exception\ModelNotFoundException
	 * @throws \think\exception\DbException
	 * @throws \think\exception\PDOException
	 *
	 * 文章删除
	 * @adminMenu(
	 *     'name'   => '文章删除',
	 *     'parent' => 'index',
	 *     'display'=> false,
	 *     'hasView'=> false,
	 *     'order'  => 10000,
	 *     'icon'   => '',
	 *     'remark' => '文章删除',
	 *     'param'  => ''
	 * )
	 * 接口改为：
	 *        用户删除： user/Member/delete
	 *        管理员删除：user/Admin_Member/delete
	 *        统一返回：
	 *                状态（state）为，1 成功，0 失败
	 */
	public function delete()
	{
		$param           = $this->request->param();
		$portalPostModel = new PortalPostModel();

		if (isset($param['id'])) {
			$id           = $this->request->param('id', 0, 'intval');
			$result       = $portalPostModel->where(['id' => $id])->find();
			$data         = [
				'object_id'   => $result['id'],
				'create_time' => time(),
				'table_name'  => 'portal_post',
				'name'        => $result['post_title']
			];
			$resultPortal = $portalPostModel
				->where(['id' => $id])
				->update(['delete_time' => time()]);
			if ($resultPortal) {
				Db::name('portal_category_post')->where(['post_id' => $id])->update(['status' => 0]);
				Db::name('portal_tag_post')->where(['post_id' => $id])->update(['status' => 0]);

				Db::name('recycleBin')->insert($data);
			}
			$portalPostModel->delSlide($id);
//			$this->success("删除成功！", '');
			return idckx_ajax_echo(null, '删除成功', 1);
		}

		if (isset($param['ids'])) {
			$ids     = $this->request->param('ids/a');
			$recycle = $portalPostModel->where(['id' => ['in', $ids]])->select();
			$result  = $portalPostModel->where(['id' => ['in', $ids]])->update(['delete_time' => time()]);
			if ($result) {
				foreach ($recycle as $value) {
					$data = [
						'object_id'   => $value['id'],
						'create_time' => time(),
						'table_name'  => 'portal_post',
						'name'        => $value['post_title']
					];
					Db::name('recycleBin')->insert($data);
				}
				$portalPostModel->delSlide($id);
//				$this->success("删除成功！", '');
				return idckx_ajax_echo(null, '删除成功', 1);
			}
		}
		return idckx_ajax_echo(null, '删除失败', 0);
	}


	/**
	 * 获取系统消息
	 *
	 * 接口地址：user/Member/getMessages
	 * 参数：无
	 * 请求类型：GET
	 * 返回参数：
	 *          Array类型 [
	 *              {messages_title,date,link}
	 *          ]
	 *          messages_title：是信息标题
	 *          date:  信息生成时间
	 *          link：信息链接（可以没有链接）
	 */
	public function getMessages()
	{


		//测试发送系统消息
//		$test = idckx_message_send();
//		dump($test);

		Cache::set('name', 123, 3600);

		dump(Cache::get('name'));

//		Cache::store('redis')->set('name',123123);


	}


}
