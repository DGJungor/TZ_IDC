<?php

namespace app\referrals\model;

use think\Model;
use think\Db;

/**
 * Class AdminAddModel
 *
 * @author  张俊
 * @package app\referrals\model
 *
 */
class AdminAddModel extends Model
{

    /**
     *
     * 产品推介 添加模型
     *
     * @param $data
     * @return string
     *
     */
    public function addReferrals($data)
    {

        //获取控制器中的数据
        $reName     = $data['data']['reName'];
        $rePrice    = $data['data']['rePrice'];
        $reRate     = $data['data']['reRate'];
        $reIntro    = $data['data']['reIntro'];
        $picAddress = $data['data']['pic_address'];


        //生成添加数据的库的数组
        $data = [
            'name'        => $reName,
            'price'       => $rePrice,
            'rate'        => $reRate,
            'intro'       => $reIntro,
            'pic_address' => $picAddress
        ];

        //添加数据库
        Db::name('referrals')
            ->insert($data);

        //返回加添加结果
        $referralsId = DB::name('referrals')
            ->getLastInsID();
        return $referralsId;

    }


}