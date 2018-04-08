Use ThinkPHP 5.0 create restful api
===============


## 通过使用ThinkPHP来创建Restful风格API，实现移动端，服务端分离CS架构

* 工具：Wampserver、ThinkPHP v5.0.10、CA证书、Postman、Navicat
* 工具下载地址：[百度网盘下载](https://pan.baidu.com/s/1WDi2yApUyqxazGtLSaEcGQ '百度网盘') 密码：zqd0
* 使用方法： 
```
git clone git@github.com:RiversCoder/tp5-api.git
cd tp5-api
```

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

* 接口路由：Route::post('user/register', 'user/register')
* 用该用户名(手机/邮箱)获取验证码，然后用这个用户名该调用该接口，才会匹配上验证码
* url请求(POST) : api.movi.com/user/register
* post参数：user_name 、 user_pwd、code
    
    | user_name | user_pwd | code | 
    | :-: | :-: | :-: | 
    | string | string| int | 
    | 用户名 | 用户密码 | 验证码 | 
* 返回数据参考:
```js
{
    "code": 200,
    "msg": "用户注册成功！",
    "data": []
}
```

### 用户登陆接口API

* 接口路由：Route::post('user/login', 'user/login')
* url请求(POST) : api.movi.com/user/login
* post参数：user_name 、 user_pwd

    | time | token | user_name | user_pwd |
    | :-: | :-: | :-: | :-: |
    | int | int | string | string|
    | 时间戳 | 验证身份 | 用户名 | 用户密码 |

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

### 用户上传头像接口API

* 接口路由：Route::post('user/icon', 'user/uploadHeadImg')
* url请求(POST) : api.movi.com/user/icon
* post参数：


    | time | token | user_id | user_icon |
    | :-: | :-: | :-: | :-: |
    | int | int | int | object |
    | 时间戳 | 验证身份 | 用户id | 上传的图片资源 |

* 返回数据参考:

```js
{
    "code": 200,
    "msg": "上传头像成功",
    "data": "/uploads/20180405/efa4c44b4dae92c092f66b4384f787d3.jpg"
}
``` 

### 用户修改密码接口API

* 接口路由：Route::post('user/change_pwd', 'user/changePwd')
* url请求(POST) : api.movi.com/user/change_pwd
* post参数：


    | time | token | user_name | user_old_pwd | user_pwd |
    | :-: | :-: | :-: | :-: | :-: |
    | int | int | string | string | string |
    | 时间戳 | 验证身份 | 用户名 | 旧密码 | 新密码 |

* 返回数据参考:

```js
{
    "code": 200,
    "msg": "密码修改成功!",
    "data": []
}
``` 

### 用户找回密码接口API

* 接口路由：Route::post('user/find_pwd', 'user/findPwd')
* url请求(POST) : api.movi.com/user/find_pwd
* post参数：


    | time | token | user_name | user_pwd | code |
    | :-: | :-: | :-: | :-: | :-: |
    | int | int | string | string | int |
    | 时间戳 | 验证身份 | 用户名 | 新密码 | 验证码 |

* 返回数据参考:

```js
{
    "code": 200,
    "msg": "密码修改成功!",
    "data": []
}
``` 


### 用户绑定邮箱/手机接口API

* 接口路由：Route::post('user/bind_phone_email', 'user/bindPhoneEmail')
* url请求(POST) : api.movi.com/user/bind_phone_email
* post参数：


    | time | token | user_name | user_id | code |
    | :-: | :-: | :-: | :-: | :-: |
    | int | int | string | int | int |
    | 时间戳 | 验证身份 | 要绑定的手机号/邮箱 | 用户ID | 验证码 |

* 返回数据参考:

```js
{
    "code": 200,
    "msg": "绑定邮箱成功！",
    "data": []
}
``` 

### 用户设置昵称接口API

* 接口路由：Route::post('user/nickname', 'user/modifyUsername')
* url请求(POST) : api.movi.com/user/nickname
* post参数：


    | time | token | user_nickname | user_id |
    | :-: | :-: | :-: | :-: |
    | int | int | string | int |
    | 时间戳 | 验证身份 | 昵称 | 用户ID |

* 返回数据参考:

```js
{
    "code": 200,
    "msg": "昵称设置成功！",
    "data": []
}
``` 

### 新增文章接口API

* 接口路由：Route::post('article', 'article/addArticle')
* url请求(POST) : api.movi.com/article
* post参数： * 表示必须字段


    | time | token | article_uid | article_title | artcle_ctime | article_content |
    | :-: | :-: | :-: | :-: | :-: | :-: |
    | int | int | int |  string | int | string |
    | *时间戳 | *验证身份 | *用户ID | *文章标题 | *发布时间 | 文章内容 |

* 返回数据参考: (data为文章的id)

```js
{
    "code": 200,
    "msg": "新增文章成功！",
    "data": "5" 
}
``` 

### 文章列表接口API

* 接口路由：Route::get('articles/:time/:token/:user_id/[:num]/[:page]', 'article/getArticles')
* url请求(GET) : api.movi.com/articles/1/1/2/2/1
* post参数： * 表示必须字段


    | time | token | user_id | num | page |
    | :-: | :-: | :-: | :-: | :-: |
    | int | int | int |  int | int |
    | *时间戳 | *验证身份 | *用户ID | 查询条数 | 查询页数 |

* 返回数据参考: 

```js
{
    "code": 200,
    "msg": "查询成功！",
    "data": {
        "articles": [
            {
                "article_id": 1,
                "article_ctime": 1523030209,
                "article_title": "太平洋战争",
                "user_nickname": "cici"
            },
            {
                "article_id": 2,
                "article_ctime": 1523030405,
                "article_title": "太平洋战争",
                "user_nickname": "cici"
            }
        ],
        "page_num": 4
    }
}
``` 

### 获取文章详情接口API

* 接口路由：Route::get('article/:time/:token/:article_id', 'article/articleDetail')
* url请求(GET) : api.movi.com/article/1/1/8
* post参数： * 表示必须字段


    | time | token | article_id |
    | :-: | :-: | :-: |
    | int | int | int |
    | *时间戳 | *验证身份 | *文章ID |

* 返回数据参考: 

```js
{
    "code": 200,
    "msg": "查询成功！",
    "data": {
        "article_id": 8,
        "article_ctime": 1523159078,
        "article_title": "那年那月",
        "article_content": "<script>console.log('风华雪月，大漠孤烟直!')</script>",
        "user_nickname": "cici"
    }
}
``` 

### 修改文章接口API

* 接口路由：Route::put('article', 'article/updateArticle')
* url请求(PUT) : api.movi.com/article
* put参数： * 表示必须字段  * x-www-form-urlencoded


    | time | token | article_id | article_title | article_content |
    | :-: | :-: | :-: | :-: | :-: |
    | int | int | int | string | string |
    | *时间戳 | *验证身份 | *文章ID | 文章标题 | 文章内容 |

* 返回数据参考: 

```js
{
    "code": 200,
    "msg": "修改文章成功!",
    "data": []
}
``` 

### 删除文章接口API

* 接口路由：Route::delete('article', 'article/deleteArticle')
* url请求(PUT) : api.movi.com/article
* put参数： * 表示必须字段  * x-www-form-urlencoded


    | time | token | article_id | 
    | :-: | :-: | :-: |
    | int | int | int |
    | *时间戳 | *验证身份 | *文章ID |

* 返回数据参考: 

```js
{
    "code": 200,
    "msg": "删除文章成功!",
    "data": []
}
``` 



> 欢迎关注我的个人博客 ： [小青蛙的博客](http://blog.sina.com.cn/riversfrog '小青蛙的博客')