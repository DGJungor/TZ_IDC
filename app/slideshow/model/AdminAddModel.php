<?php

namespace app\slideshow\model;

use think\Model;
use think\Db;

/**
 * Class AdminAddModel
 *
 * 后台  轮播图添加
 *
 * @author 张俊
 * @package app\slideshow\model
 *
 */
class AdminAddModel extends Model
{

    // 开启自动写入时间戳字段
    protected $autoWriteTimestamp = true;

    public function addPost($data)
    {
        $create_time = time();
        $title       = $data['data']['title'];
        $postID      = $data['data']['postID'];
        $pic_address = $data['data']['pic_address'];
        $data        = [
            'slideshow_title' => $title,
            'portal_id'       => $postID,
            'pic_address'     => $pic_address,
            'create_time'     => $create_time
        ];
        Db::name('slideshow')
            ->insert($data);
        $slideshowId = DB::name('slideshow')
            ->getLastInsID();
        return $slideshowId;

    }

}