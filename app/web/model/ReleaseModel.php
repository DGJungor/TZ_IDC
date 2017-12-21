<?php
/**
 * Created by PhpStorm.
 * User: 胡志伟
 * Date: 2017/12/13
 * Time: 10:37
 */

namespace app\web\model;

use think\Model;
use think\Db;
class ReleaseModel extends  Model
{

    /**
     * 判断文章是否允许评论
     * @param $id
     * @return $this
     */
    public function judge($id)
    {
        $result = Db::name('portal_post')->field('comment_status')->where('id',$id);
        return $result;
    }
    /*
     * 发布评论
     * @param $data
     * @return int|string
     */
    public function setcomment($data)
    {
        $result = Db::name('user_comment')->insertGetId($data);
        return $result;
    }

    /*
     * 评论的总条数
     * @return int|string
     */
    public function numbercomment()
    {
        $number = Db::name('user_comment')->count('c_id');
        return $number;
    }

    /*
     * 回复信息
     * @param $data
     * @return int|string
     */
    public function setreply($data)
    {
        $result = Db::name('user_reply')->insertGetId($data);
        return $result;
    }

    /*
     * 回复的总条数
     * @return int|string
     */
    public function numberreply()
    {
        $number = Db::name('user_reply')->count('r_id');
        return $number;
    }

    /**
     * 点击一篇文章查看
     * @param bool|false $id
     * @return bool|false|\PDOStatement|string|\think\Collection
     */
    public function getcomment($id = false)
    {
        if($id){
            $data = Db::table('idckx_portal_post')
                ->alias('p')
                ->join('idckx_user_comment c','c.issue_id = p.id','LEFT ')
                ->join('idckx_user_reply r','c.c_id = r.comment_id','LEFT ')
                ->field('p.id,user_id,published_time,post_title,post_content,post_content_filtered,create_time,C_user_id,c_id,comment_msg,C_create_date,r_id,to_user_id,reply_msg,R_create_date')
                ->where('p.id',$id)
                ->select();
            return $data;
        }
        return false;
    }
}