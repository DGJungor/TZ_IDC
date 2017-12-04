<?php

namespace app\slideshow\model;

use think\Model;
use think\Db;

class ListModel extends Model
{
    public function getList()
    {

        $data['list']         = Db::name('slideshow')->select();
        $data['count'] = db('slideshow')->count();

        return $data;


    }

}