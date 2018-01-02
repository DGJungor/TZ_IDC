<?php
    namespace app\portal\model;
    
    use app\admin\model\RouteModel;
    use app\portal\model\PortalPostModel;
    use think\Model;
    use think\Db;
    class SearchModel extends Model
    {
        function __construct() {
            $this->portalPostModel = new PortalPostModel();
        }
        public static function getAllSearchKeyword($param) {
            $order = empty($param['order']) ? '' : $param['order'];
            $where =  empty($param['where']) ? '' : $param['where'];
            $articles = Db::name("search_keyword")->alias('post')->where($where)->order($order)->select();
            $result = $articles;
            return $result;
        }
        public function getArticle($keyword) {
            $result = [];
            $where=[
            'post.create_time'=>['egt',0],
            'post.post_content|post.post_title|post.post_keywords'=>['like',"%$keyword%"]
            ];
            $result = $this->portalPostModel->alias('post')->where($where)->select();
           return $result->toArray();
        }
        public function getSearchKeyword($keyword) {
           $result = Db::name("search_keyword")->where("search_keyword",$keyword)->find();
           if($result) {
            return $result;
           }else {
            return ["search_count"=>0];
           }
           
        }
        public function setSearchKeyword($data) {
            $isAdd = Db::name("search_keyword")->where("search_keyword",$data["search_keyword"])->find();
            if($isAdd) {
                $result = Db::name("search_keyword")->where("search_keyword",$data["search_keyword"])->update($data);
                if($result) {
                    return $result;
                }else {
                    return "更新失败";
                }
            }else {
                $result = Db::name("search_keyword")->insert($data);
                if($result) {
                    return $result;
                }else {
                    return "写入失败";
                }
            }
           
        }
    }