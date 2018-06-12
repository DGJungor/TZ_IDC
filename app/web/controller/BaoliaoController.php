<?php
/**
 * Created by PhpStorm.
 * User: 胡志伟
 * Date: 2017/12/15
 * Time: 15:41
 */

namespace app\web\controller;

include_once(dirname(dirname(dirname(__FILE__))).'/tools/ajaxEcho.php');
include_once(dirname(dirname(dirname(__FILE__))).'/tools/cookie_session.php');
use cmf\controller\HomeBaseController;
use app\web\model\BaoliaoModel;
use think\Db;
class BaoliaoController  extends  HomeBaseController
{

    public  $class = false;

    public function __construct()
    {
        $this->class = new BaoliaoModel();
    }

    public function index()
    {
        if(!empty($_POST['id']))
        {
            $data =  $this->class->categorybaoliao($_POST['id']);
        }else{
            $data =  $this->class->categorybaoliao();
        }
        $categeory =  $this->class->webCategoryTableTree();
        return  ajaxEcho(['categeory'=>$categeory,'baoliao'=>$data],'爆料质料','1');
    }
    /*
    * AJAX以POST提交值
    * NAME: user ---> 匿名用户名
    *       user_qq  ---> 匿名用户QQ
    *       user_Mobile  ---> 匿名用户手机号
    *       text --->用户爆料
    *SESSION: baoliaoID  ==  爆料表的ID值
    * ---------爆料
    * @return \think\response\Json
    */
    public function baoliao()
    {
        $userID = cmf_get_current_user_id();
        if($userID !=0){
            if(count($_POST) == 3){
                $result = $this->class->setbao($_POST);
                $result = $this->class->setCategory($result,$_POST);
                if($result)
                {
                    return ajaxEcho([],'发布成功',1);
                }
                return ajaxEcho([],'发布失败');
            }
            return ajaxEcho([],'请编辑,在发布');
        }else if(!empty($_POST['anonymous']['user_log']) and count($_POST) >= 3)
        {
            $data = [
                'user'         => $_POST['anonymous']['user_login'],
                'user_qq'      => $_POST['anonymous']['user_QQ'],
                'user_Mobile' => $_POST['anonymous']['mobile'],
                'text'         => $_POST['post_content'],
                'title'        => $_POST['post_title'],
            ];
            $validate = \think\Loader::validate('Baoliao');
            if($validate->check($data))
            {
                $result = $this->class->getanonymous($_POST['anonymous']);
                if($result){
                    $data = [
                        'text'=> $_POST['text'],
                        'n_id' => $result['id'],
                    ];
                    $this->class->setbao($data);
                    return ajaxEcho([],'发布成功、待审核',1);
                }else{
                    $array = [
                        'user_login'         => $_POST['anonymous']['user_login'],
                        'user_QQ'      => $_POST['anonymous']['user_QQ'],
                        'mobile' => $_POST['anonymous']['mobile'],
                        'create_time'         => time(),
                    ];
                    $n_id = $this->class->setanonymous($array);
                    if($n_id)
                    {
                        $data = [
                            'post_content'=> $_POST['post_content'],
                            'user_id' => $n_id,
                        ];
                        $this->class->setbao($data);
                        return ajaxEcho([],'发布成功、待审核',1);
                    }
                }
                return ajaxEcho([],'发布失败');
            }
            return ajaxEcho([],'验证失败');
        }
        return ajaxEcho([],'请按要求填写');
    }

}