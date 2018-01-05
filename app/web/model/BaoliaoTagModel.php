<?php
/**
 * Created by PhpStorm.
 * User: 胡志伟
 * Date: 2017/12/15
 * Time: 13:25
 */
namespace app\web\model;

use think\Model;


class BaoliaoTagModel extends Model
{
    public static   $STATUS = array(
        0=>"未启用",
        1=>"已启用",
    );
}