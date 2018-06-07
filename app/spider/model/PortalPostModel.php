<?php

namespace app\spider\model;

use think\Model;
use think\Db;

class PortalPostModel extends Model
{

    //开始时间戳
    protected $autoWriteTimestamp = true;


    /**
     * post_content 自动转化
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


    /**
     *   添加文章数据
     */
    public function postArticle($postData)
    {

        $this->data($postData)->save();

        return $this->id;
    }


}