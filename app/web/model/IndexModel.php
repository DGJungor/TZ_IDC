<?php

namespace app\web\model;

use think\Model;
use think\Db;

class IndexModel extends Model
{

    public function hotInfo()
    {



        $hotInfo = Db::name('portal_post')
            ->field('id,post_title')
            ->limit(7)
            ->select();


        return $hotInfo;
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

    public function  friendLink()
    {
        $str = Db::name('link')
            ->where('status','=','1')
            ->select();

        return $str;
    }

}