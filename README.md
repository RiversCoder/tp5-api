Use ThinkPHP 5.0 Create Restful API
===============


## 通过使用ThinkPHP来创建Restful风格API，实现移动端，服务端分离CS架构

* 在applicaton/route.php中配置路由：实现api二级域名访问指定模块; 配置域名参数简写风格
* 在api模块的Common.php中配置公共方法：
    1. 验证请求时间戳是否过期
    2. 验证token是否匹配
    3. 验证参数是否合理

* 在调用的控制器方法内(例如：User)，继承Common类 
