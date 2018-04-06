<?php

namespace app\api\controller;

//引入第三方邮件类、短信类
use phpmailer\phpmailer;
use submail\messagexsend;

class Code extends Common
{
    public function get_code()
    {
        $username = $this->params['username'];
        $exist = $this->params['is_exist'];

        $username_type = $this->checkUsername($username);

        switch ($username_type) {
            case 'email':
                $this->getCodeByUsername($username, 'email', $exist);
                break;

            case 'phone':
                $this->getCodeByUsername($username, 'phone', $exist);
                break;
        }
    }

    /**
     * 通过手机/邮箱获取验证码
     * @param  [string] $username [手机号/邮箱]
     * @param  [string] $type [值：phone/email]
     * @param  [int] $exist [手机号是否应该存在数据库中 1：是 0: 否]
     * @return [json] [api返回的json数据]
     */
    private function getCodeByUsername($username, $type, $exist)
    {

        /* 判断类型 */
        if ($type == 'phone') {
            $type_name = '手机';
        } else {
            $type_name = '邮箱';
        }

        /* 检测手机号/邮箱是否存在与数据库 */
        $this->checkExist($username, $type, $exist);

        /* 检测验证码请求频率 30秒一次 */
        if (session($username . '_last_send_time')) {
            if (time() - session($username . '_last_send_time') < 30) {
                $this->returnMsg(400, $type_name . '验证码，每30s只能发送一次');
            }
        }

        /* 生成验证码 */
        $code = $this->makeCode(6);

        /* 使用session存储验证码,方便对比，md5加密 */
        $md5_code = md5($username . '_' . md5($code));
        session($username . '_code', $md5_code);

        /* 使用session存储验证码的发送时间 */
        session($username . '_last_send_time', time());

        /* 发送验证码 */
        if ($type == 'phone') {
            $this->sendCodeToPhone($username, $code);
        } else {
            $this->sendCodeToEmail($username, $code);
        }

    }

    /**
     * [向邮箱发送验证码]
     * @param  [String] $email [目标emial]
     * @param  [Number] $code     [验证码]
     * @return [json]           [执行结果]
     */
    private function sendCodeToEmail($email, $code)
    {

        $toemail = $email;
        $mail = new PHPMailer();

        $mail->isSMTP();
        $mail->CharSet = 'utf8';
        $mail->Host = 'smtp.163.com';
        $mail->SMTPAuth = true;
        $mail->Username = "lgc928091232@163.com";
        $mail->Password = "movi103103";
        $mail->SMTPSecure = 'ssl';
        $mail->Port = 994;
        $mail->setFrom('lgc928091232@163.com', 'movi');
        $mail->addAddress($toemail, '您好！');
        $mail->addReplyTo('lgc928091232@163.com', 'Replay');
        $mail->Subject = "您有新的验证码!";
        $mail->Body = "您的验证码时" . $code . "，验证码的有效期为600秒，本邮件请勿回复！";

        //如果发送失败
        if (!$mail->send()) {
            $this->returnMsg(400, $mail->ErrorInfo);
        } else {
            $this->returnMsg(200, '验证码发送成功，请注意查收！');
        }
    }

    /**
     * [使用 submail SDK 向手机发送短信验证码]
     * @param  [String] $phone [用户的手机号码]
     * @param  [Number] $code     [验证码]
     * @return [json]           [执行结果]
     */
    private function sendCodeToPhone($phone, $code)
    {
        $submail = new MESSAGEXsend();
        $submail->setTo($phone);
        $submail->SetProject('FoJ494');
        $submail->AddVar('time', 600);
        $submail->AddVar('code', $code);
        $xsend = $submail->xsend();

        //判断返回结果
        if ($xsend['status'] !== 'success') {
            $this->returnMsg(400, $xsend['msg']);
        } else {
            $this->returnMsg(200, '手机验证码发送成功，每天发送5次，请在十分钟内验证！');
        }
    }

    /**
     * [curl请求资源数据]
     * @param  [Array] $data [要传递的数据]
     * @return [Array]       [执行后返回的结果]
     */
    private function httpRequest($data)
    {
        $curl = curl_init();
        curl_setopt($curl, CURLOPT_URL, $this->RequestUrl);
        curl_setopt($curl, CURLOPT_HEADER, 0);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
        if (isset($data)) {
            curl_setopt($curl, CURLOPT_POST, 1);
            curl_setopt($curl, CURLOPT_POSTFIELDS, $data);
        }

        $res = curl_exec($curl);
        var_dump(curl_error($curl));
        curl_close($curl);

        return $res;
    }

    /**
     * 生成验证码
     * @param  [int] $num [验证法的位数]
     * @return [init] [生成的验证码]
     */
    private function makeCode($num)
    {
        // 100000 - 999999
        $max = pow(10, $num) - 1;
        $min = pow(10, $num - 1);

        return rand($min, $max);
    }
}
