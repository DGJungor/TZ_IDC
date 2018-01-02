<?php
namespace app\portal\controller;

use cmf\controller\AdminBaseController;
use app\portal\model\InfoPostModel;
use app\portal\service\PostService;
use app\portal\model\InfoCategoryModel;
use think\Db;
use app\admin\model\ThemeModel;

class AdminStoreController extends AdminBaseController
{
    public function index()
    {
        $param = $this->request->param();

        $categoryId = $this->request->param('category', 0, 'intval');
        $portalCategoryModel = new InfoCategoryModel();
        $categoryTree        = $portalCategoryModel->adminCategoryTree($categoryId);
        $postService = new PostService();
        $data        = $postService->adminProductList($param)["data"];
        $typelist = $postService->adminProductList($param)["type"];
        $data->appends($param);
        
        $this->assign('start_time', isset($param['start_time']) ? $param['start_time'] : '');
        $this->assign('end_time', isset($param['end_time']) ? $param['end_time'] : '');
        $this->assign('keyword', isset($param['keyword']) ? $param['keyword'] : '');
        $this->assign('articles', $data->items());
        $this->assign('type_list', $typelist);
        $this->assign('category_tree', $categoryTree);
        $this->assign('category', $categoryId);
        $this->assign('page', $data->render());
        return $this->fetch();
    }
    public function add()
    {
        $themeModel        = new ThemeModel();
        $articleThemeFiles = $themeModel->getActionThemeFiles('portal/Article/index');
        $this->assign('article_theme_files', $articleThemeFiles);
        return $this->fetch();
    }
    /**
     * 编辑文章
     * @adminMenu(
     *     'name'   => '编辑文章',
     *     'parent' => 'index',
     *     'display'=> false,
     *     'hasView'=> true,
     *     'order'  => 10000,
     *     'icon'   => '',
     *     'remark' => '编辑文章',
     *     'param'  => ''
     * )
     */
    public function edit()
    {
        $id = $this->request->param('id', 0, 'intval');

        $portalPostModel = new InfoPostModel();
        $post            = $portalPostModel->where('id', $id)->find();
        $post["post_introduction"] = $portalPostModel->getPostContentAttr($post["post_introduction"]);
        $post["post_company_info"] = $portalPostModel->getPostContentAttr($post["post_company_info"]);
        $post["post_advantage"] = $portalPostModel->getPostContentAttr($post["post_advantage"]);
        $post["post_performance"] = $portalPostModel->getPostContentAttr($post["post_performance"]);
        $post["post_recommend"] = $portalPostModel->getPostContentAttr($post["post_recommend"]);
        $postCategories  = $portalPostModel->getTypeList($id);
        $postCategoryIds = implode(',', array_keys($postCategories));

        $themeModel        = new ThemeModel();
        $articleThemeFiles = $themeModel->getActionThemeFiles('portal/Article/index');
        $this->assign('article_theme_files', $articleThemeFiles);
        $this->assign('post', $post);
        $this->assign('post_categories', $postCategories);
        $this->assign('post_category_ids', $postCategoryIds);
        // return var_dump($postCategories);
        return $this->fetch();
    }
    public function addPost() {
        if ($this->request->isPost()) {
            $data   = $this->request->param();
            $post   = $data['post'];
            $result = $this->validate($post, 'AdminArticle');
            if ($result !== true) {
                $this->error($result);
            }

            $portalPostModel = new InfoPostModel();

            if (!empty($data['photo_names']) && !empty($data['photo_urls'])) {
                $data['post']['more']['photos'] = [];
                foreach ($data['photo_urls'] as $key => $url) {
                    $photoUrl = cmf_asset_relative_url($url);
                    array_push($data['post']['more']['photos'], ["url" => $photoUrl, "name" => $data['photo_names'][$key]]);
                }
            }

            if (!empty($data['file_names']) && !empty($data['file_urls'])) {
                $data['post']['more']['files'] = [];
                foreach ($data['file_urls'] as $key => $url) {
                    $fileUrl = cmf_asset_relative_url($url);
                    array_push($data['post']['more']['files'], ["url" => $fileUrl, "name" => $data['file_names'][$key]]);
                }
            }

            $portalPostModel->adminAddProduct($data['post'], $data['post']['categories']);

            $data['post']['id'] = $portalPostModel->id;
            $hookParam          = [
                'is_add'  => true,
                'article' => $data['post']
            ];
            hook('portal_admin_after_save_article', $hookParam);


            $this->success('添加成功!', url('AdminStore/edit', ['id' => $portalPostModel->id]));
        }
    }
     /**
     * 编辑文章提交
     * @adminMenu(
     *     'name'   => '编辑文章提交',
     *     'parent' => 'index',
     *     'display'=> false,
     *     'hasView'=> false,
     *     'order'  => 10000,
     *     'icon'   => '',
     *     'remark' => '编辑文章提交',
     *     'param'  => ''
     * )
     */
    public function editPost()
    {

        if ($this->request->isPost()) {
            $data   = $this->request->param();
            $post   = $data['post'];
            $result = $this->validate($post, 'AdminArticle');
            if ($result !== true) {
                $this->error($result);
            }

            $portalPostModel = new InfoPostModel();

            if (!empty($data['photo_names']) && !empty($data['photo_urls'])) {
                $data['post']['more']['photos'] = [];
                foreach ($data['photo_urls'] as $key => $url) {
                    $photoUrl = cmf_asset_relative_url($url);
                    array_push($data['post']['more']['photos'], ["url" => $photoUrl, "name" => $data['photo_names'][$key]]);
                }
            }

            if (!empty($data['file_names']) && !empty($data['file_urls'])) {
                $data['post']['more']['files'] = [];
                foreach ($data['file_urls'] as $key => $url) {
                    $fileUrl = cmf_asset_relative_url($url);
                    array_push($data['post']['more']['files'], ["url" => $fileUrl, "name" => $data['file_names'][$key]]);
                }
            }

            $portalPostModel->adminEditArticle($data['post'], $data['post']['categories']);

            $hookParam = [
                'is_add'  => false,
                'article' => $data['post']
            ];
            hook('portal_admin_after_save_article', $hookParam);

            $this->success('保存成功!');

        }
    }
    /**
     * 文章删除
     * @adminMenu(
     *     'name'   => '文章删除',
     *     'parent' => 'index',
     *     'display'=> false,
     *     'hasView'=> false,
     *     'order'  => 10000,
     *     'icon'   => '',
     *     'remark' => '文章删除',
     *     'param'  => ''
     * )
     */
    public function delete()
    {
        $param           = $this->request->param();
        $portalPostModel = new InfoPostModel();

        if (isset($param['id'])) {
            $id           = $this->request->param('id', 0, 'intval');
            $result       = $portalPostModel->where(['id' => $id])->find();
            $data         = [
                'object_id'   => $result['id'],
                'create_time' => time(),
                'table_name'  => 'portal_post',
                'name'        => $result['post_title']
            ];
            $resultPortal = $portalPostModel
                ->where(['id' => $id])
                ->update(['post_status' => 0]);
            if ($resultPortal) {
                // Db::name('portal_category_post')->where(['post_id'=>$id])->update(['status'=>0]);
                // Db::name('portal_tag_post')->where(['post_id'=>$id])->update(['status'=>0]);

                // Db::name('recycleBin')->insert($data);
            }
            $this->success("删除成功！", '');

        }

        if (isset($param['ids'])) {
            $ids     = $this->request->param('ids/a');
            $recycle = $portalPostModel->where(['id' => ['in', $ids]])->select();
            $result  = $portalPostModel->where(['id' => ['in', $ids]])->update(['post_status' => 0]);
            if ($result) {
                // foreach ($recycle as $value) {
                //     $data = [
                //         'object_id'   => $value['id'],
                //         'create_time' => time(),
                //         'table_name'  => 'portal_post',
                //         'name'        => $value['post_title']
                //     ];
                //     Db::name('recycleBin')->insert($data);
                // }
                $this->success("删除成功！", '');
            }
        }
    }
}