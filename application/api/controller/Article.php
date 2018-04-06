<?php

namespace app\api\controller;

class Article extends Common
{

    public $datas;

    /**
     * [新增文章接口方法]
     */
    public function addArticle()
    {
        //1. 接收参数
        $this->datas = $this->params;
        $this->datas['article_ctime'] = time();
        //2. 往数据库插入文章信息
        $res = db('article')->insertGetId($this->datas);

        //3. 返回执行结果
        if (!empty($res)) {
            $this->returnMsg(200, '新增文章成功！', $res);
        } else {
            $this->returnMsg(400, '新增文章失败！');
        }
    }

}
