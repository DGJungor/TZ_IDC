<?php
// +----------------------------------------------------------------------
// | IDCKX
// +----------------------------------------------------------------------
// | Author: 张俊
// +----------------------------------------------------------------------
// | 自定义公共函数库
// +----------------------------------------------------------------------

use think\Config;
use think\Db;
use think\Url;
use dir\Dir;
use think\Route;
use think\Loader;
use think\Request;
use cmf\lib\Storage;

/**
 * IDCKC  AJAX 格式工具
 *
 * @author 张俊
 * @param array $data
 * @param string $info
 * @param int $code
 * @return \think\response\Json
 *
 * 前端对象输出（凡是通过ajax请求的接口都必须调用此函数输出）
 * 方法名: ajaxEcho
 * 参数：$data=[],$info="",$code=0
 * $data为要输出的数据，默认是空数组
 * $info提示信息，默认是空
 * $code错误代码，默认是0
 */
function idckx_ajax_echo($data = [], $info = "", $code = 0)
{
    return json(["state" => $code, "data" => $data, "msg" => $info]);
}


/**
 * 实时推送到百度  调用的是百度提供个推送api接口
 *
 * @author 张俊
 * @param $urls //数组
 * //    $urls    = array(
 * //        'http://www.example.com/1.html',
 * //        'http://www.example.com/2.html',
 * //        );
 * 推送接口
 *
 * 接口调用地址： http://data.zz.baidu.com/urls?site=www.idckx.com&token=Yy23pqlRJFkyBy0Z
 * 参数名称    是否必选    参数类型    说明
 * site       是        string    在搜索资源平台验证的站点，比如www.example.com
 * token      是        string    在搜索资源平台申请的推送用的准入密钥
 */
function idckx_api_baidupush($urls = array([]))
{


//	$urls    = array(
//		'http://www.example.com/1.html',
//		'http://www.example.com/2.html',
//	);

    $apiXZH  = 'http://data.zz.baidu.com/urls?appid=1549306121774721&token=0l4dfIiEDJ9atoeW&type=realtime';
    $ch      = curl_init();
    $options = array(
        CURLOPT_URL            => $apiXZH,
        CURLOPT_POST           => true,
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_POSTFIELDS     => implode("\n", $urls),
        CURLOPT_HTTPHEADER     => array('Content-Type: text/plain'),
    );
    curl_setopt_array($ch, $options);
    $resultXDH = curl_exec($ch);


    $api     = 'http://data.zz.baidu.com/urls?site=www.idckx.com&token=rEKdIMKiU1WITWD1';
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

    
    return $resultXDH;
}


/**
 * 根据文章id获取文章的分类id
 *
 * @author 张俊
 * @param null $portalId
 * @return array|false|PDOStatement|string|\think\Model
 * @throws \think\db\exception\DataNotFoundException
 * @throws \think\db\exception\ModelNotFoundException
 * @throws \think\exception\DbException
 */
function idckx_get_category_id($portalId = null)
{

    $categoryData = Db::name('portal_category_post')
        ->field('category_id')
        ->where('post_id', $portalId)
        ->find();

    return $categoryData['category_id'];
}


/**
 * 添加Token
 *
 * @author 张俊
 * @param        $userId
 * @param        $token
 * @param int $expireTime 默认过期时间  例:一小时:3600   7天:86400
 * @param string $deviceType 设备类型
 */
function idckx_token_add($userId, $token, $expireTime = 3600, $deviceType = "web")
{

    //实例化
    $userTokenModel = new \app\common\model\UserTokenModel();

    //数据库操作
    $res = $userTokenModel->addUserTokenData($userId, $token, $expireTime, $deviceType);

    return $res;
}


/**
 * 根据条件类型 删除token
 *
 * @param $type  条件类型     1:token   2:user_id
 * @param $par  条件值
 * @return int
 */
function idckx_token_del($type, $par)
{

    //实例化token模型
    $userTokenModel = new \app\common\model\UserTokenModel();

    switch ($type) {

        //token
        case 1:
            $res = $userTokenModel->deleteToken('token', $par);
            return $res;
            break;

        //user_id
        case 2:
            $res = $userTokenModel->deleteToken('user_id', $par);
            return $res;
            break;

        //未知类型
        default:
            break;
    }
}


/**
 * 查询token  是否存在
 */
function idckx_token_exist($token)
{
    //实例化token模型
    $userTokenModel = new \app\common\model\UserTokenModel();

    //查询token
    $res = $userTokenModel->getTokenData($token);

    //三元运算符判断是否存在数据
    return $res ? true : false;

}

/**
 * 根据token 获取token相关 所有的数据
 *
 * @author 张俊
 * @param $token
 * @return array|false|PDOStatement|string|\think\Model
 * @throws \think\db\exception\DataNotFoundException
 * @throws \think\db\exception\ModelNotFoundException
 * @throws \think\exception\DbException
 */
function idckx_token_get($token)
{
    //实例化
    $userTokenModel = new \app\common\model\UserTokenModel();

    //获取数据
    $res = $userTokenModel->getTokenData($token);

    return $res;
}

/**
 *  判断token是否有效
 *
 *  true :有效未过期    false: 无效或者过期并删除过期token
 * @Author ZhangJun
 * @param $token
 * @return bool
 * @throws \think\db\exception\DataNotFoundException
 * @throws \think\db\exception\ModelNotFoundException
 * @throws \think\exception\DbException
 */
function idckx_token_valid($token)
{
    //判断token是否存在
    if (idckx_token_exist($token)) {
        //存在
        //获取获取token数据
        $tokenData = idckx_token_get($token);
        if ($tokenData['expire_time'] > time()) {
            //未过期
            return $tokenData;
        } else {
            //过期
            //删除此过期token
            idckx_token_del(1, $token);
            return false;
        }
    } else {
        //不存在
        return false;
    }

}

/**
 * 查询用户是否绑定的公用函数   TODO 若参数为空 取值不正确
 *
 * @author ZhangJun
 * @param $type //例:  wechat:微信   weibo:新浪微博    qq:腾讯qq
 * @param $openId //开房平台中拿到的openid
 * @return bool
 * @throws \think\db\exception\DataNotFoundException
 * @throws \think\db\exception\ModelNotFoundException
 * @throws \think\exception\DbException
 */
function idckx_verify_binding($type, $openId)
{
    //实例化
    $userExtensionModel = new \app\common\model\UserExtensionModel();

    //查询用户扩张信息
    $extensionInfo = $userExtensionModel->queryBinding($type, $openId);

    //三元运算符判断是否存在数据
    return $extensionInfo ? $extensionInfo['user_id'] : false;

}

/**
 * 检查帐号中的帐号或者邮箱是否存在并唯一
 *
 * @author ZhangJun
 * @param $type
 */
function idckx_check_account_only($account = null, $type)
{

    //实例化
    $userModel = new \app\common\model\UserModel();

    $count = $userModel->where($type, $account)->count();

    return $count == 1 ? true : false;

}

/**
 * 过滤XSS
 */
function idckx_post_removexss($html)
{
    $removeXssC = new \app\common\controller\RemoveXssController();
    return $removeXssC->string_remove_xss(cmf_replace_content_file_url(htmlspecialchars_decode($html), true));

}

/**
 * @param $url
 * @param null $data
 * @return mixed
 */
function https_request($url, $data = null)
{
    $curl = curl_init();
    curl_setopt($curl, CURLOPT_URL, $url);
    curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, FALSE);
    curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, FALSE);
    if (!empty($data)) {
        curl_setopt($curl, CURLOPT_POST, 1);
        curl_setopt($curl, CURLOPT_POSTFIELDS, $data);
    }
    curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
    $output = curl_exec($curl);
    curl_close($curl);
    return $output;
}

/**
 * 测试函数
 */
function idckx_test($par1, $par2)
{

    if (empty($par1) && empty($par2)) {

        return 1;
    } else {

        return 2;
    }

}






