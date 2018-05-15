<?php
// +----------------------------------------------------------------------
// | ThinkCMF [ WE CAN DO IT MORE SIMPLE ]
// +----------------------------------------------------------------------
// | Copyright (c) 2013-2017 http://www.thinkcmf.com All rights reserved.
// +----------------------------------------------------------------------
// | Licensed ( http://www.apache.org/licenses/LICENSE-2.0 )
// +----------------------------------------------------------------------
// | Author: Dean <zxxjjforever@163.com>
// +----------------------------------------------------------------------
namespace app\user\controller;

use app\user\model\VerificationCodeModel;
use cmf\controller\HomeBaseController;
use think\Validate;

class VerificationCodeController extends HomeBaseController
{


    /**
     * 发送邮箱验证码   TODO
     *
     * 通过发送随机码到 数据库(idckx_verification_code表中)  记录验证信息   验证时
     *
     * 参数:
     *      email:邮箱
     *
     * @author ZhangJun
     */
    public function sendEmailCode()
    {

        //获取参数
        $data['email'] = $this->request->param('email');

        //实例验证类
        $validate = new Validate([
            'email' => 'require|email',
        ]);

        //设置错误提示
        $validate->message([
            'email.require' => '邮箱不能为空!',
            'email.email' => '邮箱格式错误!',
        ]);

        //若验证不通过 则返回错I物提示
        if (!$validate->check($data)) {
            return idckx_ajax_echo(null, $validate->getError(), 0);
        }

        //实例化模型
        $verificationCodeModel = new VerificationCodeModel();

        $codeData = $verificationCodeModel->get(['account' => $data['email']]);

        //生成验证码
        $code = mt_rand(1000, 9999);

        //判断数据库中是否存在此帐号的验证码
        if (empty($codeData)) {
            //空

            //添加验证码到数据库中
            $verificationCodeModel->addCode($data['email'], $code);

            //向邮箱发送验证码
            cmf_send_email($data['email'], "IDC快讯", '您的验证码为:' . $code . '(24小时内有效)');

            return idckx_ajax_echo(null, "发送成功", 1);

        } else {
            //不为空

            if ($codeData['count'] > 9 && $codeData['send_time'] > strtotime('-1day')) {
                //发送已经超过次数

                return idckx_ajax_echo(null, '验证码发送超过10次,请24小时之后尝试', 0);


            } else {
                //重新发送验证码


                //如果发送次数  高于10次 并且  第一次发送时间 在24小时 之值 重置   发送时间数据
                if ($codeData['send_time'] < strtotime('-1day')) {
                    $verificationCodeModel->save([
                        'send_time' => time(),
                        'count' => 0
                    ], ['account' => $data['email']]);
                }


                //更新验证码
                $verificationCodeModel->updateCode($data['email'], $code);
                //向邮箱发送验证码
                cmf_send_email($data['email'], "IDC快讯", '您的验证码为:' . $code . '(24小时内有效)');

                return idckx_ajax_echo(null, "发送成功", 1);

            }

        }

    }

    /**
     * 手机发送验证码  TODO 必须功能  (延后开发)
     */


//=================================无用代码=========================================================================================


    /**
     *
     */
    public function send()
    {
        $validate = new Validate([
            'username' => 'require',
        ]);

        $validate->message([
            'username.require' => '请输入手机号或邮箱!',
        ]);

        $data = $this->request->param();
        if (!$validate->check($data)) {
            $this->error($validate->getError());
        }

        $accountType = '';

        if (Validate::is($data['username'], 'email')) {
            $accountType = 'email';
        } else if (preg_match('/(^(13\d|15[^4\D]|17[013678]|18\d)\d{8})$/', $data['username'])) {
            $accountType = 'mobile';
        } else {
            $this->error("请输入正确的手机或者邮箱格式!");
        }

        //TODO 限制 每个ip 的发送次数

        $code = cmf_get_verification_code($data['username']);
        if (empty($code)) {
            $this->error("验证码发送过多,请明天再试!");
        }

        if ($accountType == 'email') {

            $emailTemplate = cmf_get_option('email_template_verification_code');

            $user = cmf_get_current_user();
            $username = empty($user['user_nickname']) ? $user['user_login'] : $user['user_nickname'];

            $message = htmlspecialchars_decode($emailTemplate['template']);
            $message = $this->display($message, ['code' => $code, 'username' => $username]);
            $subject = empty($emailTemplate['subject']) ? 'ThinkCMF验证码' : $emailTemplate['subject'];
            $result = cmf_send_email($data['username'], $subject, $message);

            if (empty($result['error'])) {
                cmf_verification_code_log($data['username'], $code);
                $this->success("验证码已经发送成功!");
            } else {
                $this->error("邮箱验证码发送失败:" . $result['message']);
            }

        } else if ($accountType == 'mobile') {

            $param = ['mobile' => $data['username'], 'code' => $code];
            $result = hook_one("send_mobile_verification_code", $param);

            if ($result !== false && !empty($result['error'])) {
                $this->error($result['message']);
            }

            if ($result === false) {
                $this->error('未安装验证码发送插件,请联系管理员!');
            }

            cmf_verification_code_log($data['username'], $code);

            if (!empty($result['message'])) {
                $this->success($result['message']);
            } else {
                $this->success('验证码已经发送成功!');
            }

        }


    }

}
