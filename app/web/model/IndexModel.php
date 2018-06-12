<?php

namespace app\web\model;

use think\Model;
use think\Db;
include_once(dirname(dirname(dirname(__FILE__))).'/tools/filter.php');
class IndexModel extends Model
{

    public function hotInfo()
    {
        //获取15天前的时间戳
        $timeAgo = strtotime("-15 day");


        $hotInfo = Db::name('portal_post')
            ->field('id,post_title,create_time,post_hits')
            ->where('create_time','>',$timeAgo)
            ->order('post_hits desc')
            ->limit(7)
            ->select();


        return $hotInfo;
    }
    /**
     * 获取分类
     * */ 
    public function getCategory($name,$sub) {
        $result = [];
        if($sub) {
            $parentData = Db::name('portal_category')->where('name',$name)->where("delete_time",0)->find();
            $data = Db::name('portal_category')->where('parent_id',$parentData["id"])->where("delete_time",0)->select();
            foreach($data as $k=>$v) {
                array_push($result,filter($v,["id","name","status"])); 
             }
        }else {
            $data = Db::name('portal_category')->where('name',$name)->where("delete_time",0)->find();
            array_push($result,filter($data,["id","name","status"]));
        }
        return $result;
    }
     /**
     * 获取分类内容
     * */ 
    public function getTypeContent($id="all") {
        $result = [];
        $where = [
            'w.post_type'      => 1,
            'w.published_time' => [['< time', time()], ['> time', 0]],
            'w.post_status'    => 1,
            'w.delete_time'    => 0
        ];
        if($id=="all") {
            $data = Db::name('portal_category_post')->alias('a')->join('idckx_portal_category t','a.category_id = t.id')->join('idckx_portal_post w','a.post_id = w.id')->where($where)->order('create_time desc')->limit(7)->column("w.id,t.name,w.create_time,w.update_time,w.published_time,w.post_title,w.post_excerpt,w.post_hits,w.comment_count,w.more");
            foreach($data as $k=>$v) {
                $v["typeid"] = Db::name('portal_category_post')->alias('a')->where('post_id',$v["id"])->join('idckx_portal_category t','a.category_id = t.id')->find()["id"];
                if(Db::name("portal_category")->where("name","最新文章")->find()) {
                    $v["typeurl"] = cmf_url('portal/List/index',['id'=>Db::name("portal_category")->where("name","最新文章")->find()["id"]]);
                }else {
                    $v["typeurl"] = "#";
                }
                
                $v["articleurl"] = cmf_url('portal/Article/index',['id'=>$v["id"],'cid'=>$v["typeid"]]);
                $more = json_decode($v["more"],true);
                $more["thumbnail"] = cmf_get_image_url($more["thumbnail"]);
                $v["more"] = json_encode($more);
                array_push($result,filter($v,["id","create_time","update_time","published_time","post_title","post_excerpt","post_hits","comment_count","name","more","typeid","typeurl","articleurl"]));
            }
            return $result;
        }else {
            $data = Db::name('portal_category_post')->alias('a')->where('category_id',$id)->join('idckx_portal_category t','a.category_id = t.id')->join('idckx_portal_post w','a.post_id = w.id')->where($where)->order('create_time desc')->limit(7)->column("w.id,t.name,w.create_time,w.update_time,w.published_time,w.post_title,w.post_excerpt,w.post_hits,w.comment_count,w.more");

            foreach($data as $k=>$v) {
                $v["typeid"] = Db::name('portal_category_post')->alias('a')->where('post_id',$v["id"])->join('idckx_portal_category t','a.category_id = t.id')->find()["id"];
                $v["typeurl"] = cmf_url('portal/List/index',['id'=>$v["typeid"]]);
                $v["articleurl"] = cmf_url('portal/Article/index',['id'=>$v["id"],'cid'=>$v["typeid"]]);
                $more = json_decode($v["more"],true);
                $more["thumbnail"] = cmf_get_image_url($more["thumbnail"]);
                $v["more"] = json_encode($more);
                array_push($result,filter($v,["id","create_time","update_time","published_time","post_title","post_excerpt","post_hits","comment_count","name","more","typeid","typeurl","articleurl"]));
            }
            return $result;
        }
    }
    /**
     *
     *
     *
     */
    public function matchTag($tagName)
    {
        $tagCode = Db::name('portal_category')
            ->field('id')
            ->where('name',$tagName)
            ->find();

        return $tagCode;

    }

    /**
     * 前台首页Ajax  分类显示
     *
     *
     *
     */
    public  function ajaxTag($tagCode)
    {

        $str = Db::table('idckx_portal_post')
            ->alias('p')
            ->join(['idckx_portal_category_post'=>'cp'],'cp.post_id=p.id')
            ->join(['idckx_portal_category'=>'c'],'cp.category_id=c.id')
            ->field('p.post_title,p.more,p.post_like,p.comment_count,p.post_excerpt,p.published_time,c.status,c.name,c.id')
            ->select();

        return $str;
    }


    /**
     * 获取友情链接
     *
     *
     * @author 张俊
     * @return false|\PDOStatement|string|\think\Collection
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     *
     */
    public function  friendLink()
    {
        //查询状态为开启的友情链接
        $str = Db::name('link')
            ->where('status','=','1')
            ->select();

        return $str;
    }


    /**
     *获取首页轮播图
     *
     * @author 张俊
     *
     *
     */
    public function getSlideshow()
    {
        //取出按时间排序 的前3条数据
        $getSlideshow = Db::name('slideshow')
            ->where('status','=','1')
            ->limit(3)
            ->order('create_time desc')
            ->select();

        return $getSlideshow;

    }

}