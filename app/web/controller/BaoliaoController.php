<?php
/**
 * Created by PhpStorm.
 * User: 胡志伟
 * Date: 2017/12/15
 * Time: 15:41
 */

namespace app\web\controller;

use cmf\controller\AdminBaseController;
use app\web\model\BaoliaoModel;
use think\Db;
class BaoliaoController  extends  AdminBaseController
{

    public  $class = false;

    public function __construct()
    {
        $this->class = new BaoliaoModel();
    }

    public function index()
    {
        $categeory =  $this->class->webCategoryTableTree();
        return $categeory;
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
                    return json(['name'=>'发布成功','id'=>0]);
                }
                return json(['name'=>'发布失败','id'=>1]);
            }
            return json(['name'=>'请编辑,在发布','id'=>2]);
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
                    return json(['name'=>'发布成功、待审核','id'=>1]);
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
                        return json(['name'=>'发布成功、待审核','id'=>1]);
                    }
                }
                return json(['name'=>'发布失败','id'=>0]);
            }
            return json(['name'=>'验证失败','id'=>2]);
        }
        return json(['name'=>'请按要求填写','id'=>3]);
    }

}