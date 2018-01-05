<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2017/12/14
 * Time: 16:29
 */

namespace app\web\controller;


use think\Controller;
class ListController extends Controller
{
    public function index()
    {
        $categoryId = $this->request->param('id', 0, 'intval');
        $this->redirect('AdminCategory/index', ['cate_id' => $categoryId]);
    }
}