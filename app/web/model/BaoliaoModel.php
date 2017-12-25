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
        Db::startTrans();
        try{
            $array = [
                'post_id'=> $id,
                'category_id'=> $data['category_id'],
            ];
            Db::name('baoliao_category_post')->insertGetId($array);
            $tag_ID = Db::name('baoliao_tag')->insertGetId(['name'=>$data['post_title']]);
            Db::name('baoliao_tag_post')->insertGetId(['tag_id'=>$tag_ID,'post_id'=>$id]);
            // 提交事务
            Db::commit();
            return true;
        } catch (\Exception $e) {
            // 回滚事务
            Db::rollback();
            return false;
        }
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

    /**
     * 点击查询一篇爆料
     * @param $id  爆料ID
     * @return array|false|\PDOStatement|string|Model
     */
    public function webBaoliao($id)
    {
        $data = Db::name('baoliao')->where('id',1)->where('post_status')->find();
        return $data;
    }

    /**
     * WEB爆料
     * @param bool|false $id 分类ID
     * @return \think\Paginator
     */
    public function categorybaoliao($id=false)
    {
        $where = ['b.post_status'=>1,'b.post_type'=>1]; //查询所有爆料跟爆料分类

        if($id) {

            $where = ['c.id'=> $id,'b.post_status'=>1,'b.post_type'=>1];  //查询分类是 $id 的爆料

        }

        $data = Db::table('idckx_baoliao_category')
            ->alias('c')
            ->join('idckx_baoliao_category_post p', 'c.id = p.category_id', 'LEFT ')
            ->join('idckx_baoliao_post b', 'p.post_id = b.id', 'LEFT ')
            ->field('c.id,c.name,b.id,b.post_title,b.post_content,b.published_time')
            ->where($where)
            ->order('b.published_time', 'DESC')
            ->paginate(10);
        if(count($data))
        {
            return $data;
        }

        return false;
    }

    /**
     * 我的爆料
     * @param bool|false $id  会员ID
     * @return \think\Paginator
     */
    public function Mybaoliao($id =false)
    {
        $data = Db::table('idckx_baoliao_post')
            ->alias('b')
            ->join('idckx_baoliao_category_post p', 'b.id = p.post_id', 'LEFT ')
            ->join('idckx_baoliao_category c', 'c.id = p.category_id', 'LEFT ')
            ->field('c.id,c.name,b.id,b.post_title,b.post_content,b.published_time')
            ->where(['b.user_id'=> $id,'b.post_status'=>1,'b.post_type'=>1])
            ->order('b.published_time', 'DESC')
            ->paginate(10);
        if(count($data))
        {
            return $data;
        }
        return false;
    }
}