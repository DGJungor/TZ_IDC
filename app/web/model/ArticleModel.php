<?php
namespace app\web\model;
include_once(dirname(dirname(dirname(__FILE__))).'\\tools\\filter.php');
use think\Model;
use think\Db;
class ArticleModel extends Model {
    /**
     * 发布评论
     * */ 
    public function postComment($article_id,$user_id,$data) {
        $result = Db::name('user_comment')->insert($data);
        if($result) {
            if($data["article_type"]=="post") {
                $comment_count = Db::name("portal_post")->where("id",$data["article_id"]).find()["comment_count"];
                Db::name("portal_post")->where("id",$data["article_id"])->update([
                    "comment_count"=>(int)($comment_count+1)
                ]);
            }
            if(Db::name('user_index')->where("article_id",$article_id)->where("user_id",$user_id)->find()["comment_id"]) {
                Db::name('user_index')->insert([
                    "article_id"=>$article_id,
                    "user_id"=>$user_id,
                    "comment_id"=>Db::name('user_comment')->getLastInsID()
                ]);
            }else {
                Db::name('user_index')->where("article_id",$article_id)->where("user_id",$user_id)->update([
                    "comment_id"=>Db::name('user_comment')->getLastInsID()
                ]);
            }
            
            return $result;
        }
    }
    /**
     * 收藏文章
     * */ 
    public function postCollection($data) {
        if(Db::name('user_collection')->where('article_id',$data["article_id"])->where("user_id",$data["user_id"])->where("type",$data["type"])->find()) {
            return 0;
        }
        $result = Db::name('user_collection')->insert($data);
        return $result;
    }
    /**
     * 获取评论
     * */
    public function getComment($article_id) {
        $result = [];
        $data = Db::name('user_comment')->where('article_id',$article_id)->select();
        foreach($data as $k=>$v) {
            array_push($result,filter($v,["content","time","comment_id"]));
            $result[$k]["user"] = filter(Db::name("user_vip")->where("id",$v["user_id"])->find(),["avatar","user_nickname","user_email","id"]);
            $result[$k]["replys"] = [];
            foreach(Db::name("user_reply")->where("comment_id",$v["comment_id"])->select() as $r_k=>$r_v) {
                array_push($result[$k]["replys"],filter($r_v,["content","time","id"]));
                $result[$k]["replys"][$r_k]["user"] = filter(Db::name("user_vip")->where("id",$r_v["user_id"])->find(),["avatar","user_nickname","user_email","id"]);
            }
        }
        return $result;
    }
    /**
     * 获取评论回复
     * */
    public function postReply($data) {
        $result = Db::name('user_reply')->insert($data);
        return $result;
    }
}