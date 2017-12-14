<?php

namespace app\web\model;

use think\Model;
use think\Db;


/**
 * Class AdModel
 *
 * 前台文章页 文章模型
 *
 * @author 张俊
 * @package app\web\model
 *
 */
class PortalPostModel extends Model
{
    //配置主键字段位ID
    protected $pk = 'id';

    //配置more字段取数组类型
    protected $type = [

        'more' => 'array'
    ];

    /**
     *
     * 根据文章id获取文章数据
     *
     * @author 张俊
     * @param $postId
     *
     */
    public function getPost($postId)
    {
//        $postData = $this
//            ->where()

    }

    /**
     * 首页专题显示
     *
     * 根据专题名称查询专题id 再根据专题id查询相关的文章
     *
     * @author 张俊
     * @param $specialName  '专题名称'
     * @param int $limit    '获取的数量'
     * @return false|\PDOStatement|string|\think\Collection
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     *
     */
    public function getSpecial($specialName, $limit = 5)
    {

        //获取专题名的ID
        $specialId = Db::name('portal_category')
            ->field('id')
            ->where('name', $specialName)
            ->find();

        //根据专题ID查询相关的文章信息
        $getPost = $this->alias('pp')
            ->join('idckx_portal_category_post pcp', 'pp.id=pcp.post_id')
            ->field('pp.id,pp.post_title,pp.more')
            ->where('pcp.category_id', $specialId['id'])
            ->limit($limit)
            ->order('published_time desc')
            ->select();

        return $getPost;

    }
}