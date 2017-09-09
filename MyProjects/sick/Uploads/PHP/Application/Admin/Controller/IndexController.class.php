<?php
namespace Admin\Controller;
use Think\Controller;
class IndexController extends CommonController
{
    public function index()
    {

            $this->assign('wait',0);
            $this->assign('zrNum',0);
            $this->assign('zrMoney',0);
            $this->display();
    }
    /**
     * 店铺设置
     */
    public function set()
    {
        if(IS_POST)
        {

        }
        else
        {

            $this->display();
        }
    }




    public function help(){

        $this->display();
    }



    /**
     * 反馈意见
     */
    public function comment(){

        $this->display();
    }

}

