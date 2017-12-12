<?php

namespace app\ad\model;

use think\Model;
use think\Db;


/**
 * Class AdminAddModel
 *
 * 后天广告添加模型
 *
 * @author 张俊
 * @package app\ad\model
 *
 *
 *
 */
class AdminAddModel extends Model
{


    /**
     *
     * 添加广告信息到数据库
     *
     * @param $data
     * @return string
     *
     *
     */
    public function addAd($data)
    {
        $create_time = time();
        $adName      = $data['data']['adName'];
        $adLinke     = $data['data']['adLink'];
        $siteId      = $data['data']['siteId'];
        $picAddress  = $data['data']['pic_address'];

        $data = [
            'ad_name'        => $adName,
            'ad_link'        => $adLinke,
            'ad_site_id'     => $siteId,
            'ad_pic_address' => $picAddress,
            'create_time'    => $create_time
        ];

        Db::name('ad')
            ->insert($data);

        $adId = DB::name('ad')
            ->getLastInsID();

        return $adId;

    }


}