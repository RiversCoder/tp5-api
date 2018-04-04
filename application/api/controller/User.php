<?php

namespace app\api\controller;

class User extends Common
{

    public $datas;

    public function login()
    {
        $data = $this->params;
    }

    /**
     * [用户注册时接口请求的方法]
     * @return [type] [description]
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

    private function checkRegisterUser()
    {
        $data = array();

        //获取用户名的类型 ( phone | email )
        $userType = $this->checkUsername($this->datas['user_name']);

        //检测是否已经存在于数据库
        $this->checkExist($this->datas['user_name'], $userType, 0);

        //将数据存入数组对象 ( 为了给数据库添加用户信息 )
        $this->datas['user_' . $userType] = $this->datas['user_name'];

    }

    private function insertDataToDB()
    {
        //删除user_name字段
        unset($this->datas['user_name']);
        $this->datas['user_rtime'] = time();

        //往api_user表中插入用户数据
        $res = db('user')->insert($this->datas);

        //返回执行结果
        if (!$res) {
            $this->returnMsg(400, '用户注册失败！');
        } else {
            $this->returnMsg(200, '用户注册成功！');
        }
    }

}
