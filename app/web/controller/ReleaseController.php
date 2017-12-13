<?php
/**
 * Created by PhpStorm.
 * User: 胡志伟
 * Date: 2017/12/7
 * Time: 15:27
 */

namespace app\web\controller;

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
    * NAME: user ---> 匿名用户名
    *       user_qq  ---> 匿名用户QQ
    *       user_Mobile  ---> 匿名用户手机号
    *       text --->用户爆料
    *SESSION: baoliaoID  ==  爆料表的ID值
    * ---------爆料
    * */
    public function baoliao()
    {
        $userID = cmf_get_current_user_id();
        $number = 1;
        if(session('?baoliaoID')){
            $number = session('baoliaoID') + $number;
        }
        if($userID !=0){
            if(!empty($_POST)){
                $_POST['u_id'] = $userID;
                $_POST['sort'] = $number;
                $_POST['time'] = time();
                $result = $this->class->setbao($_POST);
                if($result)
                {
                    Session::set('baoliaoID',$result);
                    return json(['name'=>'发布成功','id'=>0]);
                }
                return json(['name'=>'发布失败','id'=>1]);
            }
            return json(['name'=>'请编辑,在发布','id'=>2]);
        }else if(!empty($_POST) and count($_POST) >=3)
        {
            $data = [
                'user'         => $_POST['user'],
                'user_qq'      => $_POST['user_qq'],
                'user_Mobile' => $_POST['mobile'],
                'text'         => $_POST['text'],
            ];
            $validate = \think\Loader::validate('Release');
            if($validate->check($data))
            {
                $array = [
                    'user'         => $_POST['user'],
                    'user_qq'      => $_POST['user_qq'],
                    'user_Mobile' => $_POST['mobile'],
                    'time'         => time(),
                ];
                $result = $this->class->getanonymous($_POST);
                if($result){
                    $data = [
                        'text'=> $_POST['text'],
                        'n_id' => $result['id'],
                    ];
                    $this->class->setbao($data);
                    return json(['name'=>'发布成功、待审核','id'=>0]);
                }else{
                    $n_id = $this->class->setanonymous($array);
                    if($n_id)
                    {
                        $data = [
                            'text'=> $_POST['text'],
                            'n_id' => $n_id,
                        ];
                        $this->class->setbao($data);
                        return json(['name'=>'发布成功、待审核','id'=>0]);
                    }
                }
            }
            return json(['name'=>'验证失败','id'=>1]);
        }

    }


    /*
    * AJAX以POST提交值
    * ---------文章评论
    * */
    public function  comment()
    {
        $number = 0;
        if(session('?commentID')) {
            $number = session('commentID') + 1;
        }else{
            $number = $this->class->numbercomment();
            Session::set('commentID',$number);
        }
        if(cmf_is_user_login())
        {
            if(!empty($_POST['text'])){
                $data = [
                    'id'           => $number,                       //$number 最后的id
                    'issue_id'    => $_POST['b_id'],             //文章ID
                    'user_id'     => cmf_get_current_user_id(),  //会员ID
                    'reply_msg'   => $_POST['text'],          //评论内容
                    'create_date' => time(),                 //评论时间
                ];
                $result = $this->class->setcomment($data);
                if($result){
                    return json(['name'=>'发布成功、待审核','id'=>1]);
                }
                return json(['name'=>'发布失败','id'=>0]);
            }
        }
        return json(['name'=>'请登录','id'=>2]);
    }

    /*
    * AJAX以POST提交值
    * ---------文章回复
    * */
    public function  Reply_comment()
    {
        $number = 0;
        if(session('?replyID')) {
            $number = session('replyID') + 1;
        }else{
            $number = $this->class->numberreply();;
            Session::set('replyID',$number);
        }
        if(cmf_is_user_login())
        {
            if(!empty($_POST['text'])){
                $data = [
                    'id'              => $number,                            //$number 最后的id
                    'comment_id'     => $_POST['c_id'],                  //评论ID
                    'from_user_id'   => cmf_get_current_user_id(),      //用户ID
                    'reply_msg'      => $_POST['text'],               //回复内容
                    'to_user_id'     => $_POST['h_id'],             //回复对象ID
                    'create_date'    => time(),                    //回复时间
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
}