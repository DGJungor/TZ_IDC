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
 * 添加用于的系统消息
 */
function idckx_message_send()
{
	$test = new \app\common\model\DemoModel();

	$test2 = new \app\common\controller\SystemMessageController();

//	$resTest1 = $test2->idckx_message_send();

	$resTest2 = $test->demo();

	return '1';

}


