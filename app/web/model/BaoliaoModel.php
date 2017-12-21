<?php
/**
 * Created by PhpStorm.
 * User: 胡志伟
 * Date: 2017/12/15
 * Time: 15:47
 */

namespace app\web\model;

use think\Db;
use think\Model;
use tree\Tree;
class BaoliaoModel extends  Model
{
    /*
    * 发布爆料
    * @param $data
    * @return int|string
    */
    public function setbao($data)
    {
        $array = [
            'post_content' => $data['post_content'],
            'post_title' => $data['post_title'],
            'user_id' => cmf_get_current_user_id(),
            'create_time' => time(),
        ];
        $result = Db::name('baoliao_post')->insertGetId($data);
        return $result;
    }


    /*
    * 查询爆料
    * @return false|\PDOStatement|string|\think\Collection
    */
    public function getbao()
    {
        $result =  Db::name('baoliao_post')->where('status',1)->select();
        return $result;
    }

    /*
    * 查询一个匿名用户是否存在
    *返回匿名用户信息
    * @param $data
    * @return array|false|\PDOStatement|string|Model
    */
    public function getanonymous($data)
    {
        $result = Db::name('user_vip')->where($data)->where('user_type',0)->find();
        return $result;
    }

    /*
     * 写入匿名用户信息
     * 返回匿名用户ID
     * @param $data
     * @return int|string
     */
    public function setanonymous($data)
    {
        $n_id = Db::name('user_vip')->insertGetId($data);
        return $n_id;
    }

    /*
     * 写入分类与爆料中间联系表
     * @param $id
     * @param $data
     * @return bool
     */
    public function setCategory($id,$data)
    {
        $array = [
            'post_id'=> $id,
            'category_id'=> $data['category_id'],
        ];
        $category_ID = Db::name('baoliao_category_post')->insertGetId($array);
        $tag_ID = Db::name('baoliao_tag')->insertGetId(['name'=>$data['post_title']]);
        $tag_post_ID = Db::name('baoliao_tag_post')->insertGetId(['tag_id'=>$tag_ID,'post_id'=>$id]);
        if($category_ID and $tag_ID and $tag_post_ID)
        {
            return true;
        }
        return false;
    }

    /**
     * 前台分类
     * @param int|array $currentIds
     * @param string $tpl
     * @return string
     */
    public function webCategoryTableTree()
    {
        $where = ['delete_time' => 0];
        $categories = Db::name('baoliao_category')->order("list_order ASC")->where($where)->where('status',1)->select()->toArray();

        $tree       = new Tree();
        $tree->nbsp = '&nbsp;';
        $newCategories = [];

        foreach ($categories as $item) {
            array_push($newCategories, $item);
        }

        $tree->init($newCategories);

        if (empty($tpl)) {
            $tpl = "<option value='\$id'>\$spacer\$name</option>";
        }
        $treeStr = $tree->getTree(0, $tpl);

        return $treeStr;
    }
}