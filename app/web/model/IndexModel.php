<?php

namespace app\web\model;

use think\Model;
use think\Db;

class IndexModel extends Model
{

    public function hotInfo()
    {


        $hotInfo = Db::name('portal_post')
            ->field('id')
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
        $tagCode = Db::name('portal_tag')
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
            ->join(['idckx_portal_tag_post'=>'tp'],'tp.post_id=p.id')
            ->join(['idckx_portal_tag'=>'t'],'tp.tag_id=t.id')
            ->field('p.post_title,p.more,p.post_like,p.comment_count,p.post_excerpt,p.published_time,t.status,t.name')
            ->select();

        return $str;
    }

}