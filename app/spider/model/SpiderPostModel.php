<?php

namespace app\spider\model;

use think\Model;
use think\Db;

class SpiderPostModel extends Model
{

    /**
     *  查询爬虫列表数据
     *
     * @author ZhangJun
     */
    public function showList($page = null, $length = null)
    {
        $postData = $this
            ->page($page, $length)
            ->order('date', 'desc')
            ->select();

        return $postData;

//        return $postData;

    }

    /**
     * 获取爬虫表文章 数据
     */
    public function selectPost($id = '')
    {


    }

}