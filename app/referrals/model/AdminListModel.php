<?php

namespace app\referrals\model;

use think\Model;
use think\Db;
use think\File;

/**
 * Class ListModel
 *
 * 产品推介模型
 * @author 张俊
 * @package app\referrals\model
 *
 *
 */
class AdminListModel extends Model
{

    /**
     *
     *获取产品推介信息
     *
     * @author 张俊
     *
     */
    public function getList($offset, $limit)
    {

        //获取广告表 信息
        $data['list'] = Db::name('referrals')
            ->limit($offset, $limit)
            ->select();

        //获取所有广告总数
        $data['count'] = db('referrals')->count();

        return $data;

    }


    /**
     *
     * 修改产品推介  产品状态
     *
     * @author 张俊
     * @param $referralsId
     * @param $status
     * @return int|string
     * @throws \think\Exception
     * @throws \think\exception\PDOException
     */
    public function alterState($referralsId, $status)
    {

        //修改数据库中 广告表 中的广告字段  并获取  添加结果
        $info = Db::name('referrals')
            ->where('id', $referralsId)
            ->update(['state' => $status]);

        //返回添加结果
        return $info;
    }


    /**
     *
     * 删除产品推介
     *
     * 根据推介产品id 删除相关的推介产品信息
     * @author 张俊
     *
     */
    public function delReferrals($referralsId)
    {

        $delInfo = Db::name('referrals')
            ->where('id', $referralsId)
            ->delete();

        return $delInfo;

    }

    /**
     *
     * 获取产品推介图片路径
     *
     * @author 张俊
     * @param $referralsId '产品推介ID'
     */
    public function getPicPath($referralsId)
    {
        //根据产品产品推介ID 查询相关的图片路径
        $picPath = Db::name('referrals')
            ->field('id,pic_address')
            ->where('id', $referralsId)
            ->find();

        return $picPath;


    }

}