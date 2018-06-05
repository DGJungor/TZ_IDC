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
    public function showList($offset = null, $length = null)
    {
        $postData = $this
            ->limit(10, 10)
            ->order('date', 'desc')
            ->select();

        return $postData;

//        return $postData;

    }

}