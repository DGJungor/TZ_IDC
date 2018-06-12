<?php
namespace app\portal\controller;

use cmf\controller\HomeBaseController;
use think\Image;
class MemberController extends HomeBaseController
{
    public function index()
    {
        return $this->fetch(':index');
    }
    public function saveimg() 
    {
        
        if(!input("?post.imgdata")) {
            return json([
                "msg" => "没有图片数据",
                "state"=> 0,
                "data"=> []
            ]);
        }
        $base64_image_content = input("post.imgdata");
        if (preg_match('/^(data:\s*image\/(\w+);base64,)/', $base64_image_content, $result)){
            $type = $result[2];
            $root_file_path = dirname(dirname(dirname(dirname(__FILE__))))."/public/upload/";
            $new_file_dir = $root_file_path."user/".date('Ymd',time())."/";
            
            if(!file_exists($new_file_dir)) {
                // return $new_file;
                if(!file_exists($root_file_path."user/")) {
                    mkdir($root_file_path."user/", 0700);
                }
                if(!file_exists($root_file_path."user/".date('Ymd',time())."/")) {
                    mkdir($new_file_dir, 0700);
                }
            }
            $new_file = $new_file_dir.time().".{$type}";
            if (file_put_contents($new_file, base64_decode(str_replace($result[1], '', $base64_image_content)))){
                $image = Image::open($new_file);
                 $image->thumb(198,140,Image::THUMB_CENTER);
                 $saveName = time()."_thumbnail";
                 $image->save($new_file_dir.$saveName.".{$type}");
                return json([
                    "msg"=>"文件创建成功",
                    "state"=>1,
                    "data"=>["imgurl"=>explode("public",$new_file_dir.$saveName.".{$type}")[1]]
                ]);
            } else {
                return json([
                    "msg"=>"文件创建失败",
                    "state"=>0,
                    "data"=>[]
                ]);
            }
        }
    }
}