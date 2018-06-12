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
namespace app\portal\controller;

use cmf\controller\HomeBaseController;
use app\portal\model\InfoPostModel;
class IndexController extends HomeBaseController
{
    
    public function index()
    {
        header('Access-Control-Allow-Origin:*');
        header("Access-Control-Allow-Headers: Origin, X-Requested-With, Content-Type, Accept");
        header('Access-Control-Allow-Methods: GET, POST, PUT');
        if ($this->checkToken()){  
            return input("get.echostr");
        }else{  
            $infoPostModel = new InfoPostModel();
            $post = $infoPostModel->getProductData();
            $this->assign("post",$post); 
            return $this->fetch(':index');
        }  
       
    }
    // 微博回调认证
    public function wbOauth2() {
        if(input("?get.code")) {
            // $weibo = $this->https_request("https://api.weibo.com/oauth2/access_token?client_id=1484967174&client_secret=572c764f99369f9b906ab06a9484708f&grant_type=authorization_code&code=".input("get.code")."&redirect_uri=http://www.idckx.com/portal/Index/wbOauth2");
            $weibo = $this->https_request("https://api.weibo.com/oauth2/access_token","client_id=1484967174&client_secret=572c764f99369f9b906ab06a9484708f&grant_type=authorization_code&code=".input("get.code")."&redirect_uri=http://www.idckx.com/portal/Index/wbOauth2");
            $res=(json_decode($weibo, true));
            // $this->success("成功获取","/");
            idckx_token_add($res["uid"],$res["access_token"],$res["expires_in"],"weibo");
            
            // $this->redirect("/");
            echo "<script src='https://unpkg.com/axios/dist/axios.min.js' type='text/javascript' charset='utf-8'></script><script>axios.get('http://183.2.242.196:3000/oauth',{params: {oauthData: ".$weibo."}}).then(function (response) {console.log(response);window.close();});</script>";
            // return json($res);
        }
        
    }
    // 清除登录
    public function clearWbOauth2() {
        $weibo = $this->https_request("https://api.weibo.com/oauth2/revokeoauth2?access_token=".input("get.token"));
        $res=(json_decode($weibo, true));
        if($res["result"]) {
            idckx_token_del(1,input("get.token"));
        }
        return json([
            "state"=>$res["result"],
            "data"=> [],
            "mag"=>$res["result"]?"退出成功":"退出失败"
        ]);
    }
    private function getWxUserInfo($openid,$access_token) {
        $url="https://api.weixin.qq.com/sns/userinfo?access_token=".$access_token."&openid=".$openid."&lang=zh_CN";
        $res = $this->https_request($url);  
        $res=(json_decode($res, true)); 
        return $res;
    }
    public function wxOauth2() {
        $access_token = $this->class_weixin_adv("wxfe2fc7ac4501f033","2691608cf089941420e07fabe964441c");
        if(input("?get.code")) {
            $url="https://api.weixin.qq.com/sns/oauth2/access_token?appid=wxfe2fc7ac4501f033&secret=2691608cf089941420e07fabe964441c&code=".input("get.code")."&grant_type=authorization_code";  
            if(input("?get.state")&&input("get.state")!="STATE") {
                $this->redirect("http://www.idckx.com".input("get.state")."&access_token=".$access_token);
            }else {
                $res = $this->https_request($url);  
                $res=(json_decode($res, true));  
                $row=$this->get_user_info($res['openid'],$access_token);  
                if ($row['openid']) { 
                    // $this->success("成功获取","/");
                    idckx_token_add($row['openid'],$access_token,60*60*2,"wx");
                    // return json([
                    //     "openid"=> $row['openid'],
                    //     "access_token"=> $access_token
                    // ]);
                    $row["access_token"] = $access_token;
                    
                        echo "<script src='https://unpkg.com/axios/dist/axios.min.js' type='text/javascript' charset='utf-8'></script><script>axios.get('http://183.2.242.196:3000/oauth',{params: {oauthData: ".json_encode($row)."}}).then(function (response) {alert('登录成功，请在网页上操作');window.close();});</script>";
                    
                
                    // return json($row);
                }else {
                    $this->error('授权出错,请重新授权!'); 
                }
            }
        }else {
            $this->error('不是微信无法访问'); 
        }
    }
    public function getwxjsconfig() {
        if(input("?post.access_token")) {
            $url = "https://api.weixin.qq.com/cgi-bin/ticket/getticket?access_token=".input("post.access_token")."&type=jsapi";
            $res = $this->https_request($url);  
            $res=(json_decode($res, true));
            $timestamp = time();
            if(input("?post.url")) {
                $signature = "jsapi_ticket=".$res["ticket"]."&noncestr=idckxleeziliang&timestamp=".$timestamp."&url=".input("post.url");  
            }else {
                $signature = "jsapi_ticket=".$res["ticket"]."&noncestr=idckxleeziliang&timestamp=".$timestamp."&url=".request()->domain();  
            }
            
            return json([
                "timestamp"=>$timestamp,
                "nonceStr"=>"idckxleeziliang",
                "signature"=>sha1($signature),
                "jsapi_ticket"=>$res["ticket"],
                "url"=> input("post.url")
            ]);
        }
    }
    // 7257a3b824196d3ce6d4f0329db2543b0ab185cd
    // 7257a3b824196d3ce6d4f0329db2543b0ab185cd
    private function checkToken() {
        if(input("?get.signature")&&input("?get.timestamp")&&input("?get.nonce")) {
            $signature = input("get.signature");
            $timestamp = input("get.timestamp");
            $nonce = input("get.nonce");
            $tmpArr = array("wxidckxlogin",$timestamp,$nonce);
            sort($tmpArr,SORT_STRING);
            $tmpStr =  implode($tmpArr);  
            $tmpStr =  sha1($tmpStr);  
            if( $tmpStr == $signature ){  
                return true;  
            }else{  
                return false;  
            } 
        }else {
            return false; 
        }
         
    }
    private function class_weixin_adv($appid = NULL, $appsecret = NULL) {
        if($appid) {
            $this->appid = $appid;
        }
        if($appsecret) {
            $this->appsecret = $appsecret;  
        }
        $this->access_token = "";
        $url = "https://api.weixin.qq.com/cgi-bin/token?grant_type=client_credential&appid=".$this->appid."&secret=".$this->appsecret;  
        $res = $this->https_request($url);  
        $result = json_decode($res, true); 
        // $this->access_token = $result["access_token"];  
        $this->lasttime = time();  
        return $result["access_token"];
         
    }
    private function get_user_info($openid,$access_token)  
    {  
        $url = "https://api.weixin.qq.com/cgi-bin/user/info?access_token=".$access_token."&openid=".$openid."&lang=zh_CN";  
        $res = $this->https_request($url);  
        return json_decode($res, true);  
    }  
    private function https_request($url, $data = null) {
        $curl = curl_init();  
        curl_setopt($curl, CURLOPT_URL, $url);  
        curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, FALSE);  
        curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, FALSE);  
        if (!empty($data)){  
            curl_setopt($curl, CURLOPT_POST, 1);  
            curl_setopt($curl, CURLOPT_POSTFIELDS, $data);  
        }  
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);  
        $output = curl_exec($curl);  
        curl_close($curl);  
        return $output;
    }
}
