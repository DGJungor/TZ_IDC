<?php

namespace app\slideshow\model;

use think\Model;
use think\Db;

class AdminListModel extends Model
{
    public function getList($offset, $limit)
    {

        $data['list']  = Db::name('slideshow')
            ->limit($offset, $limit)
            ->select();
        $data['count'] = db('slideshow')->count();

        return $data;

    }


    /**
     * 修改轮播图状态
     * @param $slideshowId 需要修改的轮播图ID
     * @param $status 需要修改成的状态:  1:开启 2:关闭
     *
     */
    public function alterState($slideshowId,$status)
    {

        $info =  Db::name('slideshow')->where('id',$slideshowId)->update(['status' => $status]);
        return $info;
    }

}