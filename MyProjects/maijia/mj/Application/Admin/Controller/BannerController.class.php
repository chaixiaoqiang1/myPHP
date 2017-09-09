<?php
namespace Admin\Controller;
use Think\Controller;
class BannerController extends CommonController
{

    /**
     * 图片列表
     */
    public function index()
    {

        $this->display();
    }

    /**
     * 图片添加
     */
    public function add(){
        $this->display();
    }

    /**
     * 图片修改
     */
    public function editor(){

    }

    /**
     * 图片删除
     */
    public function delete(){

    }
}

