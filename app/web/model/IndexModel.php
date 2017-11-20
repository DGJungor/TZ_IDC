<?php

namespace app\web\model;

use think\Model;
use think\Db;

class IndexModel extends Model
{

    public function hotInfo()
    {


        $hotInfo = Db::name('portal_post')
            ->limit(7)
            ->select();


        return $hotInfo;
    }

}