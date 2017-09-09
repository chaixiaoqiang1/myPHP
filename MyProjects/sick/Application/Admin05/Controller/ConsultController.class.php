<?php
namespace Admin\Controller;
use Think\Controller;

/*
 * 咨询信息管理
 */
class ConsultController extends CommonController
{
    //  咨询列表页
    public function list(){
        $consult = M('ty_consult');
        $data = $consult->where()->order()->select();
        $this->assign('info',$data);
        $this->display();
    }

    // 修改咨询信息
    public function edit(){
        $consult = M('ty_consult');
        if(IS_POST){
            $data = $consult->create(I('post.',2));
            $result = $consult->save($data);
            if($result){
                $this->success('数据修改成功',U('Consult/list'));
            }
        }
    }

    // 删除咨询信息
    public function del($id){
        if(isset($id)){
            $comment = M('ty_comment');
            $comment->where(array('id'=>$id))->delete();
        }
    }
}











