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
        $data    = ['ad_name' => $adName, 'ad_link' => $adLinke, 'ad_site_id' => $siteId];
        Db::name('ad')
            ->insert($data);
        $slideshowId = DB::name('ad')
            ->getLastInsID();
        return $slideshowId;

    }


}