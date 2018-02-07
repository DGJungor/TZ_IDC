<?php
// +----------------------------------------------------------------------
// | IDCKX
// +----------------------------------------------------------------------
// | Author: 张俊
// +----------------------------------------------------------------------
namespace app\tools\controller;

use cmf\controller\BaseController;
use think\Controller;
use think\Loader;

/**
 * 百度资源推送控制器    https://ziyuan.baidu.com/
 *
 * Class BaiduPushController
 * @author 张俊
 * @package app\tools\controller
 */
class BaiduPushController extends BaseController
{

	/**
	 * 单挑链接推送
	 *
	 * 接口 : /tools/Baidu_public/pushOneUrl
	 * 参数 :
	 *        $pid  文章ID
	 * 返回参数 :
	 *        百度接口默认返回的参数
	 *
	 * @return mixed
	 * @throws \think\db\exception\DataNotFoundException
	 * @throws \think\db\exception\ModelNotFoundException
	 * @throws \think\exception\DbException
	 *
	 */
	public function pushOneUrl()
	{

		//获取文章id
		$pid = $this->request->param('pid');

		//根据文章id   获取分类id
		$cid = idckx_get_category_id($pid);

		//生成文章链接
		$urls[] = $this->request->domain() . cmf_url('portal/Article/index', ['id' => $pid, 'cid' => $cid]);

		//通过公共函数推送url到百度api
		$res = idckx_api_baidupush($urls);

		return $res;

	}

	/**
	 * 批量提交
	 */
	public function pushNewPortal()
	{


	}

}