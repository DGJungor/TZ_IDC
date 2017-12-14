<?php

namespace app\article\model;

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

    // 开启自动写入时间戳字段
    protected $autoWriteTimestamp = true;


    public function getPost($postId)
    {


    }

    /**
     * 首页专题显示
     *
     * 根据专题名称查询专题id 再根据专题id查询相关的文章
     *
     * @author 张俊
     * @param     $specialName '专题名称'
     * @param int $limit '获取的数量'
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

    /**
     * post_content 自动转化
     *
     * 可以将原后台添加的html代码 与 图片地址链接转化成端显示的html代码与地址链接
     *
     * @author 张俊
     * @param $value
     * @return string
     */
    public function getPostContentAttr($value)
    {
        return cmf_replace_content_file_url(htmlspecialchars_decode($value));
    }

    /**
     * post_content 自动转化
     * @param $value
     * @return string
     */
    public function setPostContentAttr($value)
    {
        return htmlspecialchars(cmf_replace_content_file_url(htmlspecialchars_decode($value), true));
    }

    /**
     * published_time 自动完成
     * @param $value
     * @return false|int
     */
    public function setPublishedTimeAttr($value)
    {
        return strtotime($value);
    }
}