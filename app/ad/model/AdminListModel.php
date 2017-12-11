<?php

namespace app\ad\model;

use think\Model;
use think\Db;


/**
 * Class ListModel
 *
 * @author 张俊
 * @package app\ad\model
 *
 *
 */
class AdminListModel extends Model
{

    /**
     * 获取图片广告信息 模型
     *
     * @author 张俊
     *
     */
    public function getList($offset, $limit)
    {

        //获取广告表 信息
        $data['list'] = Db::name('ad')
            ->limit($offset, $limit)
            ->select();

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
    public function alterState($adId, $status)
    {

        //修改数据库中 广告表 中的广告字段  并获取  添加结果
        $info = Db::name('ad')->where('id', $adId)->update(['ad_state' => $status]);

        //返回添加结果
        return $info;
    }


    /**
     *
     * 删除广告推介
     *
     * 根据推介产品id 删除相关的推介产品信息
     * @author 张俊
     *
     */
    public function delAd($adId)
    {

        $delInfo = Db::name('ad')
            ->where('id', $adId)
            ->delete();

        return $delInfo;

    }

    /**
     *
     * 获取广告图片路径
     *
     * @author 张俊
     * @param $referralsId '广告ID'
     */
    public function getPicPath($adId)
    {
        //根据产品产品推介ID 查询相关的图片路径
        $picPath = Db::name('ad')
            ->field('id,ad_pic_address')
            ->where('id', $adId)
            ->find();

        return $picPath;


    }




}