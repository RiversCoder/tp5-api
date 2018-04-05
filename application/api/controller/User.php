<?php

namespace app\api\controller;

class User extends Common
{

    public $datas;

    /**
     * [用户登陆时接口请求的方法]
     * @return [null]
     */
    public function login()
    {
        $this->datas = $this->params;

        //检测用户名类型
        $userType = $this->checkUsername($this->datas['user_name']);

        //在数据库中查询数据 (用户名和密码匹配)
        $this->matchUserAndPwd($userType);
    }

    /**
     * [用户注册时接口请求的方法]
     * @return [null]
     */
    public function register()
    {
        $this->datas = $this->params;

        //检测验证码
        $this->checkCode($this->datas['user_name'], $this->datas['code']);

        //检测用户名
        $this->checkRegisterUser();

        //将信息写入数据库
        $this->insertDataToDB();
    }

    /**
     * [检测用户名类型]
     * @return [null]
     */
    private function checkRegisterUser()
    {

        //获取用户名的类型 ( phone | email )
        $userType = $this->checkUsername($this->datas['user_name']);

        //检测是否已经存在于数据库
        $this->checkExist($this->datas['user_name'], $userType, 0);

        //将数据存入数组对象 ( 为了给数据库添加用户信息 )
        $this->datas['user_' . $userType] = $this->datas['user_name'];

    }

    /**
     * [插入数据至数据库]
     * @return [json] [注册行为产生的结果]
     */
    private function insertDataToDB()
    {
        //删除user_name字段
        unset($this->datas['user_name']);
        $this->datas['user_rtime'] = time();
        $this->datas['user_pwd'] = md5($this->datas['user_pwd']);

        //往api_user表中插入用户数据
        $res = db('user')->insert($this->datas);

        //返回执行结果
        if (!$res) {
            $this->returnMsg(400, '用户注册失败！');
        } else {
            $this->returnMsg(200, '用户注册成功！');
        }
    }

    /**
     * [登陆验证匹配]
     * @param  [string] $type [用户名类型 phone/email]
     * @return [json]       [登陆返回信息]
     */
    private function matchUserAndPwd($type)
    {
        $res = db('user')->where('user_' . $type, $this->datas['user_name'])->where('user_pwd', md5($this->datas['user_pwd']))->find();

        if (!empty($res)) {
            unset($res['user_pwd']);
            $this->returnMsg(200, '登陆成功！', $res);
        } else {
            $this->returnMsg(200, '登陆失败！', $res);
        }
    }

}
