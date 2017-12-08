<?php

namespace app\referrals\model;

use think\Model;
use think\Db;


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


}