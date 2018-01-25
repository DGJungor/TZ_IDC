<?php
// +----------------------------------------------------------------------
// | ThinkCMF [ WE CAN DO IT MORE SIMPLE ]
// +----------------------------------------------------------------------
// | Copyright (c) 2013-2017 http://www.thinkcmf.com All rights reserved.
// +----------------------------------------------------------------------
// | Licensed ( http://www.apache.org/licenses/LICENSE-2.0 )
// +----------------------------------------------------------------------
// | Author: 老猫 <thinkcmf@126.com>
// +----------------------------------------------------------------------
namespace app\user\controller;

use app\tools\controller\AjaxController;
use app\user\model\PortalPostModel;
use app\user\model\UserFavoriteModel;
use cmf\controller\UserBaseController;
use think\Db;

class ArticleController extends UserBaseController
{
	/**
	 *用户添加收藏文章
	 *
	 * @author 张俊
	 * @return \think\response\Json
	 * @throws \think\db\exception\DataNotFoundException
	 * @throws \think\db\exception\ModelNotFoundException
	 * @throws \think\exception\DbException
	 *
	 * 接口地址：user/Article/collection
	 * 请求类型：post
	 * 参数：
	 *      id  文章ID
	 *      type 文章类型就是那个（post和user）user代表用户的文章，post代表系统发布的文章
	 * 返回参数：
	 *  空数组，状态必须为1
	 *
	 */
	public function collection()
	{

		//实例化工具
		$ajaxTools = new AjaxController();

		//实例化模型
		$portalPostModel   = new PortalPostModel();
		$userFavoriteModel = new UserFavoriteModel();

		//接收参数
		$data = $this->request->param();

		//获取用户ID
		$userId = cmf_get_current_user_id();

		//获取文章数据
		$postData = $portalPostModel->get($data['id']);

		//查询收藏表收藏中是否已存在
		$examineFavorite = $userFavoriteModel->queryFavoriteExist($userId, $postData['id']);

		//根据结果执行下一步
		if (!$examineFavorite) {

			//不存在,执行收藏
			//重新拼装数组
			$addData = [
				'user_id'     => $userId,
				'title'       => $postData['post_title'],
				'url'         => "?id=" . $postData['id'] . "&type=" . $data['type'],
				'description' => $postData['post_excerpt'],
				'table_name'  => 'portal_post',
				'object_id'   => $postData['id'],
				'type'        => $data['type'],
				'create_time' => time(),
			];

			//向添加收藏模型中发送数据
			$addResult = $userFavoriteModel->addUserFavorite($addData);

			//判断是否添加成功
			if ($addResult) {
				$info = $ajaxTools->ajaxEcho(null, '收藏成功', 1);
				return $info;
			} else {
				$info = $ajaxTools->ajaxEcho(null, '收藏失败', 0);
				return $info;
			}

		} else {
			//文章已经存在
			$info = $ajaxTools->ajaxEcho(null, '已经收藏过喇', 0);
			return $info;
		}

	}
}