<?php
    namespace app\outh\controller;
    use cmf\controller\HomeBaseController;
    class WxController extends HomeBaseController {
        private $appid = "wxfe2fc7ac4501f033";
        private $appsecret = "2691608cf089941420e07fabe964441c";
        public function __construct()
        {
            parent::__construct();
            session([
                'expire' => 7200
            ]);
        }
        public function oauth() {
            $res = $this->https_request("https://api.weixin.qq.com/sns/oauth2/access_token?appid=".$this->appid."&secret=".$this->appsecret."&code=".input("get.code")."&grant_type=authorization_code");
            $resData = (json_decode($res, true));
            idckx_token_add($resData["openid"],$resData["access_token"],$resData["expires_in"],"wx");
            session("__wx_access_token__",$resData);
            echo "<script src='https://cdn.bootcss.com/axios/0.18.0/axios.js' type='text/javascript' charset='utf-8'></script><script>axios.get('http://183.2.242.196:3000/oauth',{params: {oauthData: ".$res."}}).then(function (response) {console.log(response);window.close();alert('请在网页上操作');});</script>";
        }
        public function cleartoken() {
            session("__wx_access_token__",NULL);
            return json([
                "state"=>1,
                "msg"=>"session清除成功",
                "data"=>[]
            ]);
        }
        public function getwxtokenavailable() {
            $res = $this->https_request("https://api.weixin.qq.com/sns/auth?access_token=".input("get.token")."&openid=".input("get.openid"));
            $resData = (json_decode($res, true));
            if($resData["errcode"]==0) {
                return json([
                    "state"=>1,
                    "msg"=>"获取成功",
                    "data"=>session("__wx_access_token__")
                ]);
            } else {
                return json([
                    "state"=>5000,
                    "msg"=>"获取失败",
                    "data"=>$resData
                ]);
            }
        }
        // 代理接口
        public function getWeiboApiProxy() {
            if(input("get.type")=="get") {
                return json(json_decode($this->https_request(input("get.url")."?".htmlspecialchars_decode(input("get.data"))),true));
            }else {
                return json(json_decode($this->https_request(input("get.url"),input("post.data")),true));
            }
           
        }
        // 发送请求
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