<?php
// +----------------------------------------------------------------------
// | IDCKX
// +----------------------------------------------------------------------
// | Author: 张俊
// +----------------------------------------------------------------------
// | 自定义公共函数库
// +----------------------------------------------------------------------


/**
 * IDCKC  AJAX 格式工具
 *
 * @author 张俊
 * @param array  $data
 * @param string $info
 * @param int    $code
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
 * 接口调用地址： http://data.zz.baidu.com/urls?site=www.idckx.com&token=rEKdIMKiU1WITWD1
 * 参数名称    是否必选    参数类型    说明
 * site       是        string    在搜索资源平台验证的站点，比如www.example.com
 * token      是        string    在搜索资源平台申请的推送用的准入密钥
 */
function idckx_api_baidupush($urls)
{
//	$urls    = array(
//		'http://www.example.com/1.html',
//		'http://www.example.com/2.html',
//	);
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
	echo $result;
}

/**
 *
 * @return \think\response\Json
 */
function linkUrl()
{

	$cid = $this->request->param('cid', 0, 'intval');

	$aid = $this->request->param('aid', 0, 'intval');


	return json(array("state" => "1", "msg" => "生成成功", "data" => cmf_url('portal/Article/index', ['id' => $aid, 'cid' => $cid])));

}



