<?php

namespace app\ad\controller;

use cmf\controller\AdminBaseController;

/**
 * Class AdminIndexController
 * @package app\Ad\controller
 * @adminMenuRoot(
 *     'name'   =>'广告管理',
 *     'action' =>'default',
 * )
 */
class AdIndexController extends AdminBaseController
{
    public function  index()
    {
        return $this->fetch();
    }
}
