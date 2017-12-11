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


    /**
     *
     * 删除轮播图
     *
     * 根据推介产品id 删除相关的轮播图信息
     * @author 张俊
     *
     */
    public function delSlideshow($slideshowId)
    {

        $delInfo = Db::name('slideshow')
            ->where('id', $slideshowId)
            ->delete();

        return $delInfo;

    }

    /**
     *
     * 获取轮播图图片路径
     *
     * @author 张俊
     * @param $slideshowId '轮播图ID'
     */
    public function getPicPath($slideshowId)
    {
        //根据产品产品推介ID 查询相关的图片路径
        $picPath = Db::name('slideshow')
            ->field('id,pic_address')
            ->where('id', $slideshowId)
            ->find();

        return $picPath;


    }


}