<?php
namespace Admin\Controller;

use Think\Controller;

class IndexController extends CommonController
{
    public function index()
    {
        $this->assign('wait', 0);
        $this->assign('zrNum', 0);
        $this->assign('zrMoney', 0);
        $this->display();
    }
}

