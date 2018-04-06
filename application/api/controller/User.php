<?php

namespace app\api\controller;

class User extends Common
{

    public $datas;

    /*------------------ 接口方法 -------------------*/

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
     * [用户上传头像接口请求的方法]
     * @return [type] [description]
     */
    public function uploadHeadImg()
    {
        //1. 接收参数
        $this->datas = $this->params;

        //print_r($this->datas);

        //2. 上传文件获取路径
        $head_img_path = $this->uploadFiles($this->datas['user_icon'], 'head_img');

        //3. 存入数据库
        $res = db('user')->where('user_id', $this->datas['user_id'])->update(['user_icon' => $head_img_path]);

        //4. 返回结果给客户端
        if (!empty($res)) {
            $this->returnMsg(200, '上传头像成功', $head_img_path);
        } else {
            $this->returnMsg(400, '上传头像失败');
        }
    }

    /**
     * [用户修改密码接口请求的方法]
     * @return [null]
     */
    public function changePwd()
    {
        //1. 接受参数
        $this->datas = $this->params;

        //2. 确定用户名类型
        $userType = $this->checkUsername($this->datas['user_name']);

        //3. 确定该用户名是否已经存在数据库
        $this->checkExist($this->datas['user_name'], $userType, 1);

        //4. 同时匹配用户名和密码
        $res = db('user')->where(['user_' . $userType => $this->datas['user_name'], 'user_pwd' => md5($this->datas['user_old_pwd'])])->find();

        //5. 匹配成功则将新密码加密后更新该用户密码
        if (!empty($res)) {

            //更新user_pwd字段
            $resu = db('user')->where('user_' . $userType, $this->datas['user_name'])->update(['user_pwd' => md5($this->datas['user_pwd'])]);

            if (!empty($resu)) {
                $this->returnMsg(200, '密码修改成功!');
            } else {
                $this->returnMsg(400, '密码修改失败!');
            }
        } else {
            $this->returnMsg(400, '密码错误!');
        }
    }

    /**
     * [用户找回密码接口请求的方法]
     * @return [type] [description]
     */
    public function findPwd()
    {
        //1. 接收参数
        $this->datas = $this->params;
        //2. 检测用户名类型
        $userType = $this->checkUsername($this->datas['user_name']);
        //3. 检测验证码
        $this->checkCode($this->datas['user_name'], $this->datas['code']);
        //4. 如果验证码匹配成功 就更新密码字段
        $res = db('user')->where('user_' . $userType, $this->datas['user_name'])->update(['user_pwd' => md5($this->datas['user_pwd'])]);
        //5. 返回执行结果
        if (!empty($res)) {
            $this->returnMsg(200, '密码修改成功!');
        } else {
            $this->returnMsg(400, '密码修改失败!');
        }
    }

    /**
     * [用户绑定邮箱/手机接口请求的方法]
     * @return [type] [description]
     */
    public function bindPhoneEmail()
    {
        //1. 接收参数
        $this->datas = $this->params;
        //2. 检测用户名类型
        $userType = $this->checkUsername($this->datas['user_name']);
        //3. 匹配验证码
        $this->checkCode($this->datas['user_name'], $this->datas['code']);
        //4. 更新数据库
        $res = db('user')->where('user_id', $this->datas['user_id'])->update(['user_' . $userType => $this->datas['user_name']]);

        //返回执行结果
        $returnStr = $userType == 'phone' ? '手机' : '邮箱';
        if (!empty($res)) {
            $this->returnMsg(200, '绑定' . $returnStr . '成功！');
        } else {
            $this->returnMsg(400, '绑定' . $returnStr . '失败！');
        }
    }

    /**
     * [用户设置昵称接口请求的方法]
     * @return [type] [description]
     */
    public function modifyUsername()
    {
        //1. 接收参数
        $this->datas = $this->params;
        //2. 检测该昵称是否被占用
        $res = db('user')->where('user_nickname', $this->datas['user_nickname'])->find();
        //返回执行结果
        if (!empty($res)) {
            $this->returnMsg(400, '该昵称已被暂用！');
        }
        //3. 修改user_nickname
        $ress = db('user')->where('user_id', $this->datas['user_id'])->update(['user_nickname' => $this->datas['user_nickname']]);
        //返回执行结果
        if (!empty($ress)) {
            $this->returnMsg(200, '昵称设置成功！');
        } else {
            $this->returnMsg(400, '昵称设置失败！');
        }
    }

    /* ---------------- 执行方法  ---------------- */

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
        if (!empty($res)) {
            $this->returnMsg(200, '用户注册成功！');
        } else {
            $this->returnMsg(400, '用户注册失败！');
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
