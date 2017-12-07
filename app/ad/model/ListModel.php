<?php

namespace app\ad\model;

use think\Model;
use think\Db;


class ListModel extends Model
{

    /**
     * 获取图片广告信息 模型
     *
     *
     */
    public function getList()
    {
        //获取广告表 信息
        $data['list']  = Db::name('ad')->select();

        //获取所有广告总数
        $data['count'] = db('ad')->count();

        return $data;

    }


    /**
     * 修改广告状态
     * @param $asId 需要修改的广告ID
     * @param $status 需要修改成的状态:  1:开启 2:关闭
     *
     */
    public function alterState($adId,$status)
    {

        //修改数据库中 广告表 中的广告字段  并获取  添加结果
        $info =  Db::name('ad')->where('id',$adId)->update(['ad_state' => $status]);

        //返回添加结果
        return $info;
    }




}