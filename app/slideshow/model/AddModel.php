<?php

namespace app\slideshow\model;

use think\Model;
use think\Db;

class AddModel extends Model
{

    public function addPost()
    {

//        $title, $postID, $pic_address
        $title       = 'IDC快讯';
        $postID      = 123;
        $pic_address = '123asdf.jpg';
        $data        = ['slideshow_title' => $title, 'portal_id' => $postID, 'pic_address' => $pic_address];
        Db::name('slideshow')
            ->insert($data);
        $slideshowId = DB::name('slideshow')
            ->getLastInsID();
        return $slideshowId;

    }


}