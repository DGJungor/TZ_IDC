<?php
// +----------------------------------------------------------------------
// | ThinkCMF [ WE CAN DO IT MORE SIMPLE ]
// +----------------------------------------------------------------------
// | Copyright (c) 2013-2017 http://www.thinkcmf.com All rights reserved.
// +----------------------------------------------------------------------
// | Licensed ( http://www.apache.org/licenses/LICENSE-2.0 )
// +----------------------------------------------------------------------
// | Author: 老猫 <thinkcmf@126.com>
// +----------------------------------------------------------------------
namespace app\portal\controller;

use cmf\controller\HomeBaseController;
use app\portal\model\SearchModel;

class SearchController extends HomeBaseController
{
    public function index()
    {
        $keyword = $this->request->param('keyword');

        if (empty($keyword)) {
            $this -> error("关键词不能为空！请重新输入！");
        }
        $result = $this -> estimated($keyword);
        $this -> assign("test",$result);
        $this -> assign("keyword", $keyword);
        return $this->fetch('/search');
    }
    private function estimated($keyword) {
        $result = [];
        $searchModel = new SearchModel();
       $articles = $searchModel->getArticle($keyword);
       $result["data"] = $articles;
       if(count($articles)) {
        $result = $searchModel->setSearchKeyword([
            "search_keyword"=>$keyword,
            "search_count"=> $searchModel->getSearchKeyword($keyword)["search_count"]+1,
            "similarity"=>$this->countSimilarity($articles,$keyword),
            "article_count"=>count($articles)
        ]);
       }
       return $result;
    }
    private function countSimilarity($articles,$keyword) {
        $resultSimilarity = 0;
        foreach($articles as $k=>$v) {
            $resultSimilarity += substr_count($v["post_content"].$v["post_title"].$v["post_keywords"],$keyword);
        }
        return $resultSimilarity/count($articles);
    }
}
