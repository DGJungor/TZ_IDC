<?php
    namespace app\outh\controller;
    use cmf\controller\HomeBaseController;
    
    class WeiboController extends HomeBaseController {
        public function __construct(){
            parent::__construct();
            $this->config = array("appkey"=>"990609482","appsecret"=>"596841b2eb675c7c1279d5d4544a8ef5");
        }
        public function index() {
            // 暂时为空
        }
        // 获取token
        public function wbOauth2() {
            header('Access-Control-Allow-Origin: *');
            header("Access-Control-Allow-Headers: Origin, X-Requested-With, Content-Type, Accept");
            header('Access-Control-Allow-Methods: GET, POST, PUT');
            // if(session("?__wb_access_token__")) {
            //     if(idckx_token_valid(json_decode(session("__wb_access_token__"))["access_token"])) {
            //         echo "<script src='https://unpkg.com/axios/dist/axios.min.js' type='text/javascript' charset='utf-8'></script><script>axios.get('http://183.2.242.196:3000/oauth',{params: {oauthData: ".session("__wb_access_token__")."}}).then(function (response) {console.log(response);window.close();});</script>";
            //         return ;
            //     }
            // }
            if(input("?get.code")) {
                // $weibo = $this->https_request("https://api.weibo.com/oauth2/access_token?client_id=1484967174&client_secret=572c764f99369f9b906ab06a9484708f&grant_type=authorization_code&code=".input("get.code")."&redirect_uri=http://www.idckx.com/portal/Index/wbOauth2");
                $weibo = $this->https_request("https://api.weibo.com/oauth2/access_token","client_id=".$this->config["appkey"]."&client_secret=".$this->config["appsecret"]."&grant_type=authorization_code&code=".input("get.code")."&redirect_uri=http://www.idckx.com/outh/Weibo/wbOauth2");
                $res=(json_decode($weibo, true));
                // $this->success("成功获取","/");
                idckx_token_add($res["uid"],$res["access_token"],$res["expires_in"],"weibo");
                session("__wb_access_token__",$weibo);
                // $this->redirect("/");
                echo "<script src='https://cdn.bootcss.com/axios/0.18.0/axios.js' type='text/javascript' charset='utf-8'></script><script>axios.get('http://183.2.242.196:3000/oauth',{params: {oauthData: ".$weibo."}}).then(function (response) {console.log(response);window.close();});</script>";
                // return json($res);
            }
        }
        // 检测token
        public function checkToken() {
            if(input("get.access_token")) {
                if(idckx_token_valid(input("get.access_token"))) {
                    return json([
                        "state"=>1,
                        "msg"=>"token正常",
                        "data"=>[]
                    ]);
                } else {
                    return json([
                        "state"=>0,
                        "msg"=>"token不存在",
                        "data"=>[]
                    ]);
                }
                
            } else {
                return json([
                    "state"=>0,
                    "msg"=>"token必须传入",
                    "data"=>[]
                ]);
            }
            
        }
        // 清除登录
        public function clearWbOauth2() {
            header('Access-Control-Allow-Origin: *');
            header("Access-Control-Allow-Headers: Origin, X-Requested-With, Content-Type, Accept");
            header('Access-Control-Allow-Methods: GET, POST, PUT');
            $weibo = $this->https_request("https://api.weibo.com/oauth2/revokeoauth2?access_token=".input("get.token"));
            $res=(json_decode($weibo, true));
            if($res["result"]) {
                idckx_token_del(1,input("get.token"));
                session("__wb_access_token__",null);
            }
            return json([
                "state"=>$res["result"],
                "data"=> [],
                "mag"=>$res["result"]?"退出成功":"退出失败"
            ]);
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