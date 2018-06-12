<?php
// +----------------------------------------------------------------------
// | IDCKX
// +----------------------------------------------------------------------
// | Author: 张俊
// +----------------------------------------------------------------------
namespace app\tools\controller;

use app\tools\model\PluginReptilePostModel;
use cmf\controller\BaseController;
use think\Controller;
use think\Db;
use think\Loader;
use think\Request;

/**
 * 百度资源推送控制器    https://ziyuan.baidu.com/
 *
 * Class BaiduPushController
 * @author 张俊
 * @package app\tools\controller
 */
class BaiduPushController extends BaseController
{


    /**
     * 站点推送测试方法
     */
    public function test()
    {

        //实例化模型
        $pluginReptilePostModel = new PluginReptilePostModel();

//		$test = $pluginReptilePostModel->queryPush(30772);
        $test = $pluginReptilePostModel->queryPush(30772222);
        dump($test);

    }

    /**
     * 单挑链接推送
     *
     * 接口 : /tools/Baidu_public/pushOneUrl
     * 类型 : POST
     * 参数 :
     *        $pid  文章ID
     * 返回参数 :
     *        百度接口默认返回的参数
     *
     * @return mixed
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     *
     */
    public function pushOneUrl($postId = null)
    {
        //实例化模型
        $pluginReptilePostModel = new PluginReptilePostModel();

        //获取文章id
        $pid = $this->request->param('pid');

        //根据方式选择使用的参数
        if ($pid) {
            $postId = $pid;
        }

        //根据文章id   获取分类id
        $cid = idckx_get_category_id($pid);

        //生成文章链接
        $urls[] = $this->request->domain() . cmf_url('portal/Article/index', ['id' => $postId, 'cid' => $cid]);

        //记录 推送
        $pluginReptilePostModel->editPushState($pid);

        //通过公共函数推送url到百度api
        $res = idckx_api_baidupush($urls);

        return $res;

    }


    /**
     * 百度站长批量提交
     *    自动提交最新发布的300条文章地址
     *
     * 接口 : /tools/Baidu_Push/pushNewPortal
     *
     *
     * @author 张俊
     * @param int $num
     * @return mixed
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    public function pushNewPortal($num = 2000)
    {
        //实例化模型
        $pluginReptilePostModel = new PluginReptilePostModel();

        //获取域名
        $domain = $this->request->domain();

        //从数据库中取出需要提交的文章数据
        $portalData = Db::name('portal_post')
            ->field('id,published_time')
            ->where('post_status', 1)
            ->where('delete_time', 0)
            ->order('published_time desc')
            ->limit($num)
            ->select();

        //根据接口规则  重新拼装新数组
        foreach ($portalData as $value => $item) {
            $cid    = idckx_get_category_id($item['id']);
            $urls[] = $domain . cmf_url('portal/Article/index', ['id' => $item['id'], 'cid' => $cid]);
            //记录 推送
            $pluginReptilePostModel->editPushState($item['id']);
        }

        //提交到百度站长接口
        $res = idckx_api_baidupush($urls);

        return $res;
    }


    /**
     * 接口： /tools/Baidu_Push/pushPostPortal
     * 请求类型：POST
     * 参数：
     *      post_array
     *              [
     *                  'id'提交的唯一标识（可以忽略）
     *                  'title'文章名称
     *                  'url'文章URL
     *                  'post_id'文章ID
     *                  'category_id'栏目ID
     *              ]
     *
     * 返回参数：
     *      无（状态必须要有）
     * */
    public function pushPostPortal()
    {

        //实例化模型
        $pluginReptilePostModel = new PluginReptilePostModel();

        //获取参数
        $post_array = $this->request->param();

        //获取域名
        $domain = $this->request->domain();

        //根据接口规则  重新拼装新数组
        foreach ($post_array['post_array'] as $value => $item) {
            $urls[] = $domain . cmf_url('portal/Article/index', ['id' => $item['id'], 'cid' => $item['category_id']]);

            //记录 推送
            $pluginReptilePostModel->editPushState($item['id']);
        }

        //提交到百度站长接口
        $res = idckx_api_baidupush($urls);

        //判断提交是否成功  (成功0条则为失败)
        if (json_decode($res, true)['success'] == 0) {
            return idckx_ajax_echo($res, '失败', 0);
        } else {
            return idckx_ajax_echo($res, '成功', 1);
        }

    }

    /**
     * 熊掌号 提交历史内容
     */
    public function pushOldPostPortalXZH($num = 200)
    {


        //实例化模型
        $pluginReptilePostModel = new PluginReptilePostModel();

        //获取域名
        $domain = $this->request->domain();

        //从数据库中取出需要提交的文章数据
        $portalData = Db::name('portal_post')
            ->field('id,published_time')
            ->where('post_status', 1)
            ->where('delete_time', 0)
            ->order('published_time desc')
            ->page(1, $num)
            ->select();

        //根据接口规则  重新拼装新数组
        foreach ($portalData as $value => $item) {
            $cid    = idckx_get_category_id($item['id']);
            $urls[] = $domain . cmf_url('portal/Article/index', ['id' => $item['id'], 'cid' => $cid]);
            //记录 推送
            $pluginReptilePostModel->editPushState($item['id']);
        }


        $api = 'http://data.zz.baidu.com/urls?appid=1549306121774721&token=0l4dfIiEDJ9atoeW&type=batch';
//        $api = 'http://data.zz.baidu.com/urls?appid=1549306121774721&token=0l4dfIiEDJ9atoeW&type=realtime';
        $ch      = curl_init();
        $options = array(
            CURLOPT_URL            => $api,
            CURLOPT_POST           => true,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_POSTFIELDS     => implode("\n", $urls),
            CURLOPT_HTTPHEADER     => array('Content-Type: text/plain'),
        );
        curl_setopt_array($ch, $options);
        $result = curl_exec($ch);
        echo $result;

    }
}