<?php
    namespace app\outh\controller;
    use cmf\controller\HomeBaseController;
    class QqController extends HomeBaseController {
        public function registration() {
            idckx_token_add(input("get.openid"),input("get.access_token"),7200,"qq");
            return json([
                "state"=>1,
                "msg"=>"登录成功",
                "data"=>[]
            ]);
        }
    }