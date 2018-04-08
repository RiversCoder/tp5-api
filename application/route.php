<?php
// +----------------------------------------------------------------------
// | ThinkPHP [ WE CAN DO IT JUST THINK ]
// +----------------------------------------------------------------------
// | Copyright (c) 2006~2016 http://thinkphp.cn All rights reserved.
// +----------------------------------------------------------------------
// | Licensed ( http://www.apache.org/licenses/LICENSE-2.0 )
// +----------------------------------------------------------------------
// | Author: liu21st <liu21st@gmail.com>
// +----------------------------------------------------------------------

use think\Route;

// api.movi.com -> www.movi.com/index.php/api
Route::domain('api', 'api');

//配置验证码请求路径
Route::get('code/:time/:token/:username/:is_exist', 'code/get_code');

//配置用户注册的请求路径
Route::post('user/register', 'user/register');

//配置用户登录的请求路径
Route::post('user/login', 'user/login');

//配置用户上传头像请求路径
Route::post('user/icon', 'user/uploadHeadImg');

//配置用户修改密码请求路径
Route::post('user/change_pwd', 'user/changePwd');

//配置用户找回密码请求路径
Route::post('user/find_pwd', 'user/findPwd');

//配置用户绑定手机号/邮箱
Route::post('user/bind_phone_email', 'user/bindPhoneEmail');

//配置用户设置用户昵称
Route::post('user/nickname', 'user/modifyUsername');

//配置添加文章请求路径
Route::post('article', 'article/addArticle');

//配置文章列表请求路径
Route::get('articles/:time/:token/:user_id/[:num]/[:page]', 'article/getArticles');

//配置单篇文章详情请求路径
Route::get('article/:time/:token/:article_id', 'article/articleDetail');

//修改/更新文章请求路径
Route::put('article', 'article/updateArticle');

//删除文章
Route::delete('article', 'article/deleteArticle');

/*return [
'__pattern__' => [
'name' => '\w+',
],
'[hello]' => [
':id' => ['index/hello', ['method' => 'get'], ['id' => '\d+']],
':name' => ['index/hello', ['method' => 'post']],
],

];*/
