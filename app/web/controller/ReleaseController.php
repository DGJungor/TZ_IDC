<?php
/**
 * Created by PhpStorm.
 * User: 胡志伟
 * Date: 2017/12/7
 * Time: 15:27
 */

namespace app\web\controller;

use FontLib\Table\Type\glyf;
use think\Session;
use think\Validate;
use think\Db;
use think\Request;

class ReleaseController extends Validate
{

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
//        if(Request::instance()->isAjax())
//        {
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
                    $result = Db::name('user_baoliao')->insertGetId($_POST);
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
                $rule = [
                    'user'         =>  'require|chsAlpha|max:25',
                    'text'         =>  'require',
                    'user_qq'      =>  'require|number|min:9|max:11',
                    'user_Mobile' =>   'require|number|min:11',
                ];
                $msg = [
                    'user.chsAlpha'          => '用户名只能是汉字或者字母',
                    'user.max'                => '用户名最多不能超过25个字符',
                    'user.require'            => '用户名不能为空',
                    'user_Mobile.number'     => '手机号只能是数字',
                    'user_Mobile.min'        => '手机号至少11位',
                    'user_Mobile.require'    => '手机号不能为空',
                    'text.require'            => '内容不能为空',
                    'user_qq.require'         => 'QQ号不能为空',
                    'user_qq.max'             => 'QQ号最多不能超过11个字符',
                    'user_qq.min'             => 'QQ号至少9位',
                    'user_qq.number'          => 'QQ号只能是数字',
                ];
                $data = [
                    'user'         => $_POST['user'],
                    'user_qq'      => $_POST['user_qq'],
                    'user_Mobile' => $_POST['mobile'],
                    'text'         => $_POST['text'],
                ];
                $validate = new Validate($rule, $msg);
                $result = $validate->check($data);
                if($result)
                {
                    $array = [
                        'user'         => $_POST['user'],
                        'user_qq'      => $_POST['user_qq'],
                        'user_Mobile' => $_POST['mobile'],
                        'time'         => time(),
                    ];
                    $result = Db::name('anonymous')->where(['user_qq'=>$_POST['user_qq'],'user_Mobile' => $_POST['mobile']])->find();
                    if($result){
                        $data = [
                            'text'=> $_POST['text'],
                            'n_id' => $result['id'],
                        ];
                        $result = Db::name('baoliao')->insertGetId($data);
                        return json(['name'=>'发布成功、待审核','id'=>0]);
                    }else{
                        $n_id = Db::name('anonymous')->insertGetId($array);
                        if($n_id)
                        {
                            $data = [
                                'text'=> $_POST['text'],
                                'n_id' => $n_id,
                            ];
                            $result = Db::name('baoliao')->insertGetId($data);
                            return json(['name'=>'发布成功、待审核','id'=>0]);
                        }
                    }
                }
                return json(['name'=>'验证失败','id'=>1]);
            }


//        }
    }


    /*
    * AJAX以POST提交值
    * NAME: u_id ---> 会员id
    *       w_id  ---> 文章ID
    *       text --->用户评论
    * ---------文章评论
    * */
    public function  comment()
    {
//        if(Request::instance()->isAjax())
//        {
            $number = 0;
            if(session('?commentID')) {
                $number = session('commentID') + 1;
            }else{
                $number = Db::name('user_comment')->count('id');
                Session::set('commentID',$number);
            }
            if(cmf_is_user_login())
            {
                if(!empty($_POST['text'])){
                    $data = [
                        'id'           => $number,
                        'issue_id'    => $_POST['b_id'],
                        'user_id'     => cmf_get_current_user_id(),
                        'reply_msg'   => $_POST['text'],
                        'create_date' => time(),
                    ];
                    $result = Db::name('user_comment')->insertGetId($data);
                    if($result){
                        return json(['name'=>'发布成功、待审核','id'=>1]);
                    }
                    return json(['name'=>'发布失败','id'=>0]);
                }
            }
            return json(['name'=>'请登录','id'=>2]);
//        }

    }
    public function  Reply_comment()
    {
        $number = 0;
        if(session('?replyID')) {
            $number = session('replyID') + 1;
        }else{
            $number = Db::name('user_reply')->count('id');
            Session::set('replyID',$number);
        }
        if(cmf_is_user_login())
        {
            if(!empty($_POST['text'])){
                $data = [
                    'id'              => $number,
                    'comment_id'     => $_POST['c_id'],
                    'from_user_id'   => cmf_get_current_user_id(),
                    'reply_msg'      => $_POST['text'],
                    'to_user_id'     => $_POST['h_id'],
                    'create_date'    => time(),
                ];
                $result = Db::name('user_reply')->insertGetId($data);
                if($result){
                    return json(['name'=>'回复成功、待审核','id'=>1]);
                }
                return json(['name'=>'回复失败','id'=>0]);
            }
        }
        return json(['name'=>'请登录','id'=>2]);
    }
}