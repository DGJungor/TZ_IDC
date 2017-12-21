<?php
/**
 * Created by PhpStorm.
 * User: 胡志伟
 * Date: 2017/12/7
 * Time: 15:27
 */

namespace app\web\controller;

use app\portal\taglib\Portal;
use cmf\controller\HomeBaseController;
use app\web\model\ReleaseModel;
use think\Session;
use think\Db;
use think\Request;

class ReleaseController extends HomeBaseController
{
    public $class = '';
    public function __construct()
    {
        $this->class = new ReleaseModel();
    }

    /*
    * AJAX以POST提交值
    * ---------文章评论
    * */
    public function  comment()
    {
        if(cmf_is_user_login())
        {
            if(!empty($_POST['text'])){
                $judge = $this->class->judge($_POST['b_id']);
                if($judge) {
                    $data = [
                        'issue_id' => $_POST['b_id'],                //文章ID
                        'C_user_id' => cmf_get_current_user_id(),  //用户ID
                        'comment_msg' => $_POST['text'],          //评论内容
                        'C_create_date' => time(),                 //评论时间
                    ];
                    $result = $this->class->setcomment($data);
                    if ($result) {
                        return json(['name' => '发布成功、待审核', 'id' => 1]);
                    }
                    return json(['name' => '发布失败', 'id' => 0]);
                }
                return json(['name'=>'不允许评论','id'=>3]);
            }
        }
        return json(['name'=>'请登录','id'=>2]);
    }

    /*
    * AJAX以POST提交值
    * ---------文章回复
    * @return \think\response\Json
    */
    public function  Reply_comment()
    {
        if(cmf_is_user_login())
        {
            if(!empty($_POST['text'])){
                $data = [
                    'comment_id'     => $_POST['c_id'],                  //评论ID
                    'from_user_id'   => cmf_get_current_user_id(),      //用户ID
                    'reply_msg'      => $_POST['text'],               //回复内容
                    'to_user_id'     => $_POST['h_id'],             //回复对象ID
                    'R_create_date'    => time(),                  //回复时间
                ];
                $result = $this->class->setreply($data);
                if($result){
                    return json(['name'=>'回复成功、待审核','id'=>1]);
                }
                return json(['name'=>'回复失败','id'=>0]);
            }
        }
        return json(['name'=>'请登录','id'=>2]);
    }
    public function viewcomment()
    {
        $data = $this->class->getcomment($_POST['id']);
        var_dump($data);
    }
}