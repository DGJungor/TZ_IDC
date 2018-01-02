<?php
    namespace app\portal\controller;
    
    use cmf\controller\HomeBaseController;
    use app\portal\model\InfoPostModel;
    class ProductController extends HomeBaseController
    {
        public function index()
        {
            $id = $this->request->param('id', 0, 'intval');
            $infoPostModel = new InfoPostModel();
            $post = $infoPostModel->getProductData($id);
            $post["post_introduction"] = $infoPostModel->getPostContentAttr($post["post_introduction"]);
            $post["post_company_info"] = $infoPostModel->getPostContentAttr($post["post_company_info"]);
            $post["post_advantage"] = $infoPostModel->getPostContentAttr($post["post_advantage"]);
            $post["post_performance"] = $infoPostModel->getPostContentAttr($post["post_performance"]);
            $post["post_recommend"] = $infoPostModel->getPostContentAttr($post["post_recommend"]);
            $this->assign("post",$post);
            return $this->fetch(':product');
        }
    }