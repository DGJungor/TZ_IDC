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

	function byTokenGetUser($token=null)
	{
		$userTokenModel = new UserTokenModel();

		$info =  $userTokenModel->getUserId($token);
		return $info;
	}
}
