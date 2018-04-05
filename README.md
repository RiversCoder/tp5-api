Use ThinkPHP 5.0 Create Restful API
===============


## 通过使用ThinkPHP来创建Restful风格API，实现移动端，服务端分离CS架构

### API编写前的相关配置(参数过滤)

* 在applicaton/route.php中配置路由：实现api二级域名访问指定模块; 配置域名参数简写风格
* 在api模块的Common.php中配置公共方法：
    1. 验证请求时间戳是否过期
    2. 验证token是否匹配
    3. 验证参数是否合理

* 在调用的控制器方法内(例如：User)，继承Common类
* 涉及具体url参数的定义，可以查阅application目录下route.php文件

### 验证码接口API

* 创建Code类（继承Common类）实现创建验证码
* 判断用户输入的邮箱或者手机号，匹配数据库，判断是否已经存在
* 检测用户的输入信息，配合TP自带的检测机制完成检测结果，确认用户输入的是邮箱或者是手机号
* 如果是邮箱，则将该验证码发送至用户输入的邮箱地址
* 如果是手机号，则将该验证码发送至用户的手机
* 验证接口路由配置：Route::get('code/:time/:token/:username/:is_exist', 'code/get_code')

* 调用参考： 
1. api.movi.com/code/11/1/13368669852/0
```js
{
    "code": 200,
    "msg": "手机验证码发送成功，每天发送5次，请在一分钟内验证！",
    "data": []
}  
```  
2. api.movi.com/code/11/1/88888888@qq.com/0
```js
{
    "code": 200,
    "msg": "验证码发送成功，请注意查收！",
    "data": []
}
```      

### 用户注册接口API

* 传递参数有默认的 token,time 还有 用户名、密码、验证码
* 注册接口路由：Route::post('user/register', 'user/register');
* 用该用户名(手机/邮箱)获取验证码,然后用这个用户名该调用该接口，才会匹配上验证码
* url请求(POST) : api.movi.com/user/register
* post参数：user_name 、 user_pwd、code
    
    | user_name | user_pwd | code | 
    | :-: | :-: | :-: | 
    | string | string| int | 
    | phone/email | 8 < length < 32 | length=6 | 
* 返回数据参考:
```js
{
    "code": 200,
    "msg": "用户注册成功！",
    "data": []
}
```

### 用户登陆接口API

* 传递参数有默认的 token,time 还有 用户名、密码
* 登陆接口路由：Route::post('user/login', 'user/login');
* url请求(POST) : api.movi.com/user/login
* post参数：

    | user_name | user_pwd |
    | :-: | :-: |
    | string | string|
    | phone/email | 8 < length < 32 |

* 返回数据参考:

```js
{
    "code": 200,
    "msg": "登陆成功！",
    "data": {
        "user_id": 3,
        "user_phone": "",
        "user_name": "",
        "user_email": "1569853706@qq.com",
        "user_rtime": 1522822718
    }
}
```