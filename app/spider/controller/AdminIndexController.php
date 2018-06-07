<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018/5/24
 * Time: 15:15
 */


namespace app\spider\controller;


use app\spider\model\PortalCategoryPostModel;
use app\spider\model\PortalPostModel;
use app\spider\model\SpiderPostModel;
use cmf\controller\AdminBaseController;
use think\Db;
use think\Loader;
use think\Request;
use think\Validate;


class AdminIndexController extends AdminBaseController
{

    /**
     * 爬虫页首页
     */
    public function index()
    {

        return $this->fetch('index');

//        return '123';
    }


    /**
     *
     */
    public function test2()
    {
        $test = $this->request->param('test');

        return $test;

    }

    /**
     * 爬虫列表 接口
     */
    public function showList()
    {

        //获取分页参数
        $page   = $this->request->param('page');
        $length = $this->request->param('length');


        //  app\spider\model\SpiderPostModel
        $spiderPostModel = new SpiderPostModel();

        //获取文章列表数据
        $postData = $spiderPostModel->showList($page, $length);

        //将文章列表页中的时间戳 转换成'Y-m-d H:i:s' 格式
        $cleaRule = array(" ", "　", "\t", "\n", "\r");
        foreach ($postData as $k => $v) {
            $postData[$k]['date']     = date('Y-m-d H:i:s', $postData[$k]['date']);
            $postData[$k]['keywords'] = explode(',', str_replace($cleaRule, '', $postData[$k]['keywords']));
        }

        //将数组数据转换成Json格式 并返回
        return $postData->toJson();
//        dump($postData);

    }


    /**
     * 查询文章总数
     *
     * @author ZhangJun
     */
    public function postCount()
    {

        $spiderPostModel = new SpiderPostModel();
        return $spiderPostModel->count();

    }

    /**
     *  转移文章
     *
     * @author 张俊
     * @return \think\response\Json
     *
     * 接口地址：user/Member/postArticle
     * 参数：
     *      typeid栏目ID
     *      title文章标题
     *      content文章内容
     *      descriptions文章描述
     * 返回参数：
     *          返回成功状态即可
     *
     */
    public function transferArticle()
    {

        //获取爬虫文章参数
        $spiderPostId = $this->request->param('id');

        //实例化模型
        $spiderPostModel    = new SpiderPostModel();
        $portalPostModel    = new PortalPostModel();
        $portalCategoryPost = new PortalCategoryPostModel();


        //获取爬虫文章库中的数据
        $spiderPostData = $spiderPostModel->get($spiderPostId);


        // '更多'属性
        $postData['thumbnail'] = '';
        $more['thumbnail']     = $postData['thumbnail'];
        $more['template']      = '';


        //启动事务处理
        Db::startTrans();
        //拼装成添加文章表中的数组
        $createPostData = [
            'user_id'        => 1,
            'post_title'     => $spiderPostData['title'],
            'post_excerpt'   => $spiderPostData['description'],
            'post_content'   => idckx_post_removexss($spiderPostData['content']),
            'post_status'    => 0,
            'published_time' => $spiderPostData['date'],
            'post_keywords'  => $spiderPostData['keywords'],
            'post_source'    => $spiderPostData['source'],
            'more'           => json_encode($more),
        ];


        //添加文章信息 成功则返回一个文章ID
        $portalId = $portalPostModel->postArticle($createPostData);

        //添加文章分类信息
        $portalCategoryPost = $portalCategoryPost->create([
            'post_id'     => $portalId,
            'category_id' => 14,       // 没有分类 文章不能显示  暂时找不到解决方法  暂时 写死在最新资讯的分类中
        ]);
        //获取文章分类ID
        $portalCategoryId = $portalCategoryPost->id;

        if ($portalId && $portalCategoryId) {
            //提交事务
            Db::commit();
            $res['state'] = 1;

        } else {
            //事务回滚
            Db::rollback();
            $res['state']   = 0;
            $res['message'] = '添加失败,请重试!';
        }


        return json_encode($res);

    }


    /**
     *
     */
    public function test()
    {

//        $tableData['tableData'][0]['date']    = '2016-05-02';
//        $tableData['tableData'][0]['name']    = '王小虎';
//        $tableData['tableData'][0]['address'] = '地址';


        for ($i = 0; $i < 10; $i++) {
            $tableData[$i]['date']    = '2016-05-02';
            $tableData[$i]['name']    = '快讯';
            $tableData[$i]['address'] = '地址';
            $tableData[$i]['aa']      = '地址';
        }
//        $tableData[0]['date']    = '2016-05-02';
//        $tableData[0]['name']    = '王小虎';
//        $tableData[0]['address'] = '地址';

//        dump(json_encode($tableData));
//        return json_encode($tableData);


        $spiderPostModel = new SpiderPostModel();

        $postData = $spiderPostModel->showList();

        foreach ($postData as $k => $v) {

            $postData[$k]['date'] = date('Y-m-d H:i:s', $postData[$k]['date']);

        }

        return $postData->toJson();


    }

}