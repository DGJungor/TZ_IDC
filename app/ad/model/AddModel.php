<?php

namespace app\add\model;

use think\Model;
use think\Db;

class AddModel extends Model
{

    public function addAd($data)
    {

        $adName  = $data['data']['adName'];
        $adLinke = $data['data']['adLink'];
        $siteId  = $data['data']['siteId'];
        $picAddress = $data['data']['pic_address'];

        $data    = ['ad_name' => $adName, 'ad_link' => $adLinke, 'ad_site_id' => $siteId,'ad_pic_address'=>$picAddress];
        Db::name('ad')
            ->insert($data);
        $adId = DB::name('ad')
            ->getLastInsID();
        return $adId;

    }


}