<?php

namespace app\slideshow\model;

use think\Model;
use think\Db;

class AddModel extends Model
{

    public function addPost($data)
    {

        $title       = $data['data']['title'];
        $postID      = $data['data']['postID'];
        $pic_address = $data['data']['pic_address'];
        $data        = ['slideshow_title' => $title, 'portal_id' => $postID, 'pic_address' => $pic_address];
        Db::name('slideshow')
            ->insert($data);
        $slideshowId = DB::name('slideshow')
            ->getLastInsID();
        return $slideshowId;

    }


}