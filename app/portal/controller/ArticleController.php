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
use app\portal\model\PortalCategoryModel;
use app\portal\service\PostService;
use app\portal\model\PortalPostModel;
use app\user\controller\IndexController;
use think\Db;

class ArticleController extends HomeBaseController
{
	// public function keywordslink() {
	//     $page  = $this->request->param('page', 1, 'intval');
	//     $link = [];
	//     $tagName = [];
	//     $test_match_count = [];
	//     foreach($this->getPost($page,100) as $k => $v) {
	//         $pattern = '/<a\b[^>]+\bhref="([^"]*)"[^>]*><strong>([\s\S]*?)<\/strong><\/a>/';
	//         preg_match($pattern,$v["post_content"],$match);
	//         if(count($match)) {
	//             array_push($link,$match[1]);
	//             array_push($tagName,$match[2]);
	//             if(substr_count($match[1],"http://www.idckx.com")) {
	//                 array_push($test_match_count,preg_replace('/<a\b[^>]+\bhref="[^"]*"[^>]*><strong>[\s\S]*?<\/strong><\/a>/',"<a href='".cmf_url('portal/List/index',['id'=>Db::name("portal_category")->where("delete_time",0)->where("name",$match[2])->find()["id"]])."'><strong>".$match[2]."</strong></a>",$v["post_content"]));
	//             }
	//         }

	//     }
	//     return json(array("state"=>"1","msg"=>"获取成功","data"=>[]));
	// }
	public function linkUrl()
	{
		$cid = $this->request->param('cid', 0, 'intval');
		$aid = $this->request->param('aid', 0, 'intval');

		return json(array("state" => "1", "msg" => "生成成功", "data" => cmf_url('portal/Article/index', ['id' => $aid, 'cid' => $cid])));
	}
	// private function setPostContent($id,$content) {
	//     Db::name("portal_post")->where("id",$id)->update(["post_content" => $content]);
	// }
	// private function getPost($page,$count) {
	//     $result = Db::name("portal_post")->where("delete_time",0)->field("id,post_content")->select();
	//     return $result;
	// }
	public function publicGetUser()
	{
		$wgUser = cmf_get_current_user();
		if ($wgUser) {
			$ret = array(
				"is_login" => 1,
				"user"     => array(
					"user_id"     => $wgUser["id"],
					"nickname"    => $wgUser["user_nickname"],
					"img_url"     => "http://183.2.242.196:3000" . $wgUser["avatar"],
					"profile_url" => $wgUser["user_url"],
					"sign"        => "*"
				)
			);
		} else {
			$ret = array("is_login" => 0);//未登录
		}
		echo $_GET['callback'] . '(' . json_encode($ret) . ')';
	}

	public function returnLogin()
	{
		$wgUser = cmf_get_current_user();
		if (!$wgUser) {
			$return = array(
				'code'        => 1,
				'reload_page' => 0
			);
		} else {
			$index = new IndexController();
			$index->logout();
			$return = array(
				'code'        => 1,
				'reload_page' => 1
			);
		}
		echo $_GET['callback'] . '(' . json_encode($return) . ')';
	}

	public function index()
	{

		$portalCategoryModel = new PortalCategoryModel();
		$postService         = new PostService();

		$articleId  = $this->request->param('id', 0, 'intval');
		$categoryId = $this->request->param('cid', 0, 'intval');
		$article    = $postService->publishedArticle($articleId, $categoryId);
		if ($article && $article["post_status"] == 0) {
			if ($article["user_id"] != cmf_get_current_user_id()) {
				$article = null;
			}
		}
		if (empty($articleId)) {
			abort(404, '文章不存在!');
		}


		$prevArticle = $postService->publishedPrevArticle($articleId, $categoryId);
		$nextArticle = $postService->publishedNextArticle($articleId, $categoryId);

		$tplName = 'article';

		if (empty($categoryId)) {
			$categories = $article['categories'];

			if (count($categories) > 0) {
				$this->assign('category', $categories[0]);
				$category = $portalCategoryModel->where('id', $categories[0])->where('status', 1)->find();
			} else {
				abort(404, '文章未指定分类!');
			}

		} else {
			$category = $portalCategoryModel->where('id', $categoryId)->where('status', 1)->find();

			if (empty($category)) {
				abort(404, '文章不存在!');
			}

			$this->assign('category', $category);

			$tplName = empty($category["one_tpl"]) ? $tplName : $category["one_tpl"];
		}

		Db::name('portal_post')->where(['id' => $articleId])->setInc('post_hits');


		hook('portal_before_assign_article', $article);
		if (isset($category)) {
			$tagResult = $portalCategoryModel->getTag($portalCategoryModel->typeArticle($category->toArray()["id"]));
			$this->assign("tagall", $tagResult);
		} else {
			$tagResult = $portalCategoryModel->getTag($portalCategoryModel->typeArticle($article["category_id"]));
			$this->assign("tagall", $tagResult);
		}
		// $this->assign("tagall",[]);
		if (!$article) {
			$this->redirect("/", 302);
		}
		$this->assign('article', $article);
		$this->assign('prev_article', $prevArticle);
		$this->assign('next_article', $nextArticle);

		$tplName = empty($article['more']['template']) ? $tplName : $article['more']['template'];

		return $this->fetch("/$tplName");
	}

	// 文章点赞
	public function doLike()
	{
		$this->checkUserLogin();
		$articleId = $this->request->param('id', 0, 'intval');


		$canLike = cmf_check_user_action("posts$articleId", 1);

		if ($canLike) {
			Db::name('portal_post')->where(['id' => $articleId])->setInc('post_like');

			$this->success("赞好啦！");
		} else {
			$this->error("您已赞过啦！");
		}
	}

	public function myIndex()
	{
		//获取登录会员信息
		$user = cmf_get_current_user();
		$this->assign('user_id', $user['id']);
		return $this->fetch('user/index');
	}

	//用户添加
	public function add()
	{
		return $this->fetch('user/add');
	}

	public function addPost()
	{
		if ($this->request->isPost()) {
			$data   = $this->request->param();
			$post   = $data['post'];
			$result = $this->validate($post, 'AdminArticle');
			if ($result !== true) {
				$this->error($result);
			}

			$portalPostModel = new PortalPostModel();

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
			$portalPostModel->adminAddArticle($data['post'], $data['post']['categories']);

			$this->success('添加成功!', url('Article/myIndex', ['id' => $portalPostModel->id]));
		}
	}

	public function select()
	{
		$ids                 = $this->request->param('ids');
		$selectedIds         = explode(',', $ids);
		$portalCategoryModel = new PortalCategoryModel();

		$tpl = <<<tpl
<tr class='data-item-tr'>
    <td>
        <input type='checkbox' class='js-check' data-yid='js-check-y' data-xid='js-check-x' name='ids[]'
                               value='\$id' data-name='\$name' \$checked>
    </td>
    <td>\$id</td>
    <td>\$spacer <a href='\$url' target='_blank'>\$name</a></td>
    <td>\$description</td>
</tr>
tpl;

		$categoryTree = $portalCategoryModel->adminCategoryTableTree($selectedIds, $tpl);

		$where      = ['delete_time' => 0];
		$categories = $portalCategoryModel->where($where)->select();

		$this->assign('categories', $categories);
		$this->assign('selectedIds', $selectedIds);
		$this->assign('categories_tree', $categoryTree);
		return $this->fetch('user/select');
	}

	/**
	 * 测试
	 */
	public function test()
	{

//		dump('123');


		$t = get_client_ip($type = 0, $adv = false);



		dump($t);
	}
}
