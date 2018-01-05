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
     * 一篇文章评论条数添加
     * @param $id  文章ID
     * @param $number 文章总评论数
     * @throws \think\Exception
     */
    public function numbercomment($id,$number)
    {
        Db::name('portal_post')->where('id',$id)->update(['comment_count'=>$number+1]);
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
     * 篇文章查看
     * @param bool|false $id    文章ID
     * @return bool|false|\PDOStatement|string|\think\Collection
     */
    public function getcomment($id = false)
    {
        $where = ['p.post_status'=>1];      //全部文章查看
        if($id){
            $where = ['p,id'=>$id,'p.post_status'=>1,'c.C_status'=>1];    //点击一篇文章查看
        }
        $data = Db::table('idckx_portal_post')
            ->alias('p')
            ->join('idckx_user_comment c','c.issue_id = p.id','LEFT ')
            ->field('p.id,user_id,published_time,post_title,post_content,post_content_filtered,create_time,C_user_id,c_id,comment_msg,C_create_date')
            ->where($where)
            ->order('p.published_time','DESC')
            ->paginate(10);
        return $data;
    }

    /**
     * 点击评论查看回复信息
     * @param bool|false $id   评论ID
     * @return false|\PDOStatement|string|\think\Collection
     */
    public function getreply($id =false)
    {
        $where = ['c.id'=>$id,'r.R_state'=>1];
        $data = Db::table('idckx_user_comment')
            ->alias('c')
            ->join('idckx_user_reply r','c.id = r.comment_id')
            ->where($where)
            ->select();
        return $data;
    }
}