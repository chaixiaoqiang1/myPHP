<?php
namespace Admin\Model;
use Think\Model\RelationModel;
class AdminModel extends RelationModel{
    
    protected $_validate = array(
       array('username', '/^[0-9a-zA-Z]+$/', '用户名只限数字和字母', 1, 'regex', 3),
       array('username','','用户名已经存在！',0,'unique',1),
       array('password', 'require', '密码不能为空',1),
    );
    
    protected $_auto = array(
            array('password', 'md5', 3, 'function'),
    );

  
    public function update(){
        $data = $this->create();
        if(!$data){ //数据对象创建错误
            return false;
        }

        /* 添加或更新数据 */
        if(empty($data['id'])){
            $res = $this->add();
        }else{
            $res = $this->save();
        }
        return $res;
    }
}