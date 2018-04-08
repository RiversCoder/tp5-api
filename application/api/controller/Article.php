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

    /**
     * [获取文章列表接口方法]
     * @return [null]
     */
    public function getArticles()
    {
        //1. 接收参数
        $this->datas = $this->params;

        //2.检查参数
        if (!isset($this->datas['num'])) {
            $this->datas['num'] = 10;
        }

        if (!isset($this->datas['page'])) {
            $this->datas['page'] = 1;
        }

        //3. 查询数据库
        $where['article_uid'] = $this->datas['user_id'];
        $count = db('article')->where($where)->count();
        $page_num = ceil($count / $this->datas['num']);
        $field = 'article_id,article_ctime,article_title,user_nickname';
        $join = [['api_user u', 'u.user_id = a.article_uid']];
        $res = db('article')->alias('a')->field($field)->join($join)->where($where)->page($this->datas['page'], $this->datas['num'])->select();
        if ($res === false) {
            $this->returnMsg(400, '查询失败！');
        } else if (empty($res)) {
            $this->returnMsg(200, '暂无数据！');
        } else {
            //响应数据给客户端
            $return_data['articles'] = $res;
            $return_data['page_num'] = $page_num;
            $this->returnMsg(200, '查询成功！', $return_data);
        }
    }

}
