<?php
namespace Admin\Controller;
use Think\Controller;
class QuestionController extends CommonController
{
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * 课程分类
     */
    public function index()
    {
        $question = M('question');
        $count = $question->count();
        $Page = new \Think\Page($count,15);
        setPage($Page);
        $show = $Page->show();
        $info = $question->order('id desc')->limit($Page->firstRow.','.$Page->listRows)->select();
        $this->assign('count',$count);
        $this->assign('page',$show);
        $this->assign('info',$info);
        $this->display();
    }

    /**
     *  批量删除
     */
    public function piliangdel(){
        $shanchu =  I("shanchu");
        $shanchu_two = implode(',',$shanchu);
        $question = M('question');
        $result = $question->delete($shanchu_two);
        if($result){
            $this->redirect('Question/index','',1,"<script>alert('删除成功')</script>");
        }else{
            $this->redirect('Question/index','',1,"<script>alert('删除失败')</script>");
        }
    }


    /**
     * 课程分类添加
     */
    public function add(){
        $question = M('question');
        if(IS_POST){
            $upload = $this->upload();// 实例化上传类
            $info   =   $upload->uploadOne($_FILES['img']);
            if (!$info) {
                echo "<script>alert('图片上传错误')</script>";
                die;
            } else {
                $img = './Public/' . $info['savepath'] . $info['savename'];
                $image = new \Think\Image();
                $image->open($img);
                $image->thumb($_POST['width'],$_POST['height'],\Think\Image::IMAGE_THUMB_FIXED)->save('./Public/' . $info['savepath'] . 'thumb_' . $info['savename']);
                $thumb_url = './Public/' . $info['savepath'] . 'thumb_' . $info['savename'];
            }
            $data = $question->create(I('post.'));
            $data['size'] = $_POST['width']."x".$_POST['height'];
            $data['img'] = $thumb_url;
            $data['addtime'] = time();
            $result = $question->add($data);
            if($result){
                $this->redirect('Question/index','',1,"<script>alert('添加成功')</script>");
            }else{
                $this->redirect('Question/add','',1,"<script>alert('添加失败')</script>");
            }
        }
            $this->display();
    }

    /**
     * 课程分类修改
     */
    public function editor(){
        $question = M('question');
        $id = I('id');
        if(IS_POST){
            $qid = $_POST['id'];
            $upload = $this->upload();// 实例化上传类
            $info   =   $upload->uploadOne($_FILES['img']);
            $data =  $question->create(I('post.'));
            $data['size'] = $_POST['width'].'x'.$_POST['height'];
            if ($info) {
                $img = './Public/' . $info['savepath'] . $info['savename'];
                $image = new \Think\Image();
                $image->open($img);
                $image->thumb($_POST['width'],$_POST['height'],\Think\Image::IMAGE_THUMB_FIXED)->save('./Public/' . $info['savepath'] . 'thumb_' . $info['savename']);
                $thumb_url = './Public/' . $info['savepath'] . 'thumb_' . $info['savename'];
                $data['img'] = $thumb_url;
            }
            $data['updatetime'] = time();
            $result = $question->where(array('id'=> $qid))->save($data);
            if($result){
                $this->redirect('Question/index','',1,"<script>alert('修改成功')</script>");
            }else{
                $this->redirect('Question/editor',array('id'=> $qid),1,"<script>alert('修改失败')</script>");
            }
        }
        $info = $question->where(array('id'=> $id))->find();
        $info['size'] = explode('x',$info['size']);
        $this->assign('info',$info);
        $this->display();
    }

    /**
     * 课程分类删除
     */
    public function delete(){
        if (IS_POST) {
            $question = M('question');
            $id = I('id');
            $result = $question->where(array('id' => $id))->delete();
            if ($result) {
                echo 'ok';die;
            } else {
                $this->redirect('Question/index', '', 1, "<script>alert('修改失败')</script>");
            }
        }
    }

    /**
     * 课程状态修改
     */
    public function edit_statu(){
        $question = M('question');
        $id = I('id');
        $status = $question->where(array('id' => $id))->getField('status');
        if($status == 1){
            $status = 2;
        }else{
            $status = 1;
        }
        $result = $question->where(array('id' => $id))->setField('status',$status);
        if($result){
            $this->redirect('Question/index', '', 1, "<script>alert('修改成功')</script>");
        }else{
            $this->redirect('Question/index', '', 1, "<script>alert('修改失败')</script>");
        }
    }

    /**
     * 课程列表
     */
    public function course(){
        if(IS_POST){
            $post = I('post.');
            $this->search_course($post);
        }
        $course = M('course');
        $count  = $course->where($post)->count();
        $Page       = new \Think\Page($count,10);
        setPage($Page);
        $show       = $Page->show();
        $info = $course->where($post)->order('id desc')->limit($Page->firstRow.','.$Page->listRows)->select();
        $this->assign('count',$count);
        $this->assign('page',$show);
        $this->assign('info',$info);
        $this->display();
    }

    /**
     * 添加课程
     */
    public function add_course(){
        $course = M('course');
        $question = M('question');
        $info = $question->where(array('status'=> 1))->field('id,title,type')->select();
        foreach($info as $k=>&$v){
            if($v['type'] == 1){
                $v['type'] = "青葱课";
            }elseif($v['type'] == 2){
                $v['type'] = "政治课";
            }elseif($v['type'] == 3){
                $v['type'] = "物理课";
            }elseif($v['type'] == 4){
                $v['type'] = "是非课";
            }elseif($v['type'] == 5){
                $v['type'] = "魔法课";
            }else{
                $v['type'] = "青葱课";
            }
        }
        if(IS_POST){
            $data['question_id'] = $_POST['question_id'];
            $data['title'] = $_POST['title'];
            $data['content'] = $_POST['shop_desc'];
            $data['course_type'] = $question->where(array('id'=> $_POST['question_id']))->getField('type');
            $data['time'] = date('Y-m-d H:i:s',time());
            $result = $course->add($data);
            if($result){
                $this->redirect('Question/course','',1,"<script>alert('添加成功')</script>");
            }else{
                $this->redirect('Question/add_course','',1,"<script>alert('添加失败')</script>");
            }
        }
        $this->assign('info',$info);
        $this->display();
    }

    /**
     * 修改课程
     */
    public function edit_course(){
        $course = M('course');
        $question = M('question');
        $info = $question->where(array('status'=> 1))->field('id,title,type')->select();
        foreach($info as $k=>&$v){
            if($v['type'] == 1){
                $v['type'] = "青葱课";
            }elseif($v['type'] == 2){
                $v['type'] = "政治课";
            }elseif($v['type'] == 3){
                $v['type'] = "物理课";
            }elseif($v['type'] == 4){
                $v['type'] = "是非课";
            }elseif($v['type'] == 5){
                $v['type'] = "魔法课";
            }else{
                $v['type'] = "青葱课";
            }
        }
        $id = I('id');
        if(IS_POST){
            $data = I('post.');
            $data['time'] = date('Y-m-d H:i:s',time());
            $data['course_type'] = $question->where(array('id'=> $_POST['question_id']))->getField('type');
            $result = $course->save($data);
            if($result){
                $this->redirect('Question/course','', 1, "<script>alert('修改成功')</script>");
            }else{
                $this->redirect('Question/edit_course',array('id'=>$cid), 1, "<script>alert('修改失败')</script>");
            }
        }
        $list = $course->where(array('id'=>$id))->find();
        $list['question_id'] =  $question->where(array('id'=>$list['question_id']))->field('id,type,title')->find();
        $this->assign('info',$info);
        $this->assign('list',$list);
        $this->display();
    }


    /**
     * 删除课程
     */
    public function del_course(){
        $course = M('course');
        $id = I('id');
        $result = $course->where(array('id'=>$id))->delete();
        if($result){
            echo 'ok';die;
        }else{
            $this->redirect('Question/course', '', 1, "<script>alert('删除失败')</script>");
        }
    }

    /**
     * 批量删除课程
     */
    public function del_list(){
        $course = M('course');
        if(IS_POST){
            $id = implode(',',$_POST['dellist']);
            $result = $course->delete($id);
            if($result){
                $this->redirect('Question/course', '', 1, "<script>alert('删除成功')</script>");
            }else{
                $this->redirect('Question/course', '', 1, "<script>alert('删除失败')</script>");
            }
        }
    }


    /**
     *  课程列表状态设置
     */
    public function edit_course_status(){
        $course = M('course');
        $id = I('id');
        $status = $course->where(array('id' => $id))->getField('status');
        if($status == 1){
            $status = 2;
        }else{
            $status = 1;
        }
        $result =   $course->where(array('id' => $id))->setField('status',$status);
        if($result){
            echo "ok";
            die;
        }else{
            $this->redirect('Question/course', '', 1, "<script>alert('修改失败')</script>");
        }
    }

    /**
     *课程列表搜索查询
     */
    public function search_course(&$where){
        $data  = $where;
        $data = array_filter($data);
        if($data['title']){
            $data['title'] = array('like',"%{$data['title']}%");
        }
        if($data['time']){
            $data['time'] = array('like',"{$data['time']}%");
        }
        $where = $data;
    }


    /**
     * 课程评论
     */
    public function course_com(){
        $id = I('id');
        if(IS_POST){
            $where = I('post.');
            $this->search_course_com($where);
        }
        $course_msg = M('course_msg');
        $user = M("user");
        if($id){
            $where['course_id'] = $id;
        }
        $count = $course_msg->where($where)->count();
        $Page       = new \Think\Page($count,15);
        setPage($Page);
        $show       = $Page->show();
        $info = $course_msg->where($where)->order('id desc')->limit($Page->firstRow.','.$Page->listRows)->select();
        foreach($info as $k=>&$v){
            $v['user_id'] = $user->where(array('id'=> $v['user_id']))->getField('name');
//            $v['time'] = date("Y-m-d",$v['time']);
        }
        $this->assign('count',$count);
        $this->assign('page',$show);
        $this->assign('info',$info);
        $this->display();
    }

    /**
     *   留言页面
     */
    public function liuyan(){
        $course_msg = M('course_msg');
        $user = M('user');
        $id = I('id');
        $info = $course_msg->where(array('id'=>$id))->find();
        $info['user_id'] = $user->where(array('id'=>$info['user_id']))->getField('name');
        $this->assign('info',$info);
        $this->display();
    }

    /**
     * 课程评论删除
     */
     public function  del_course_com(){
         $course_msg = M('course_msg');
         $id = I('id');
         $result = $course_msg->where(array('id'=> $id))->delete();
         if($result){
             $data = 'ok';
             $this->ajaxReturn($data);
         }else{
            $this->redirect('Question/course_com','',1,"<script>alert('删除失败')</script>");
         }
     }

    /**
     * 课程评论状态更改
     */
    public function status_course_com(){
        $course_msg = M("course_msg");
        $id = I('id');
        $status = $course_msg->where(array('id' => $id))->getField('status');
        if($status == 1){
            $status = 2;
        }else{
            $status = 1;
        }
        $result =   $course_msg->where(array('id' => $id))->setField('status',$status);
        if($result){
            $data = 'ok';
            $this->ajaxReturn($data);
        }else{
            $this->redirect('Question/course_com', '', 1, "<script>alert('修改失败')</script>");
        }
    }

    /**
     * 评论批量删除
     */
    public function delall_course_com(){
        $course_msg = M("course_msg");
        $id_arr = I('allid');
        $id_str = implode(',',$id_arr);
        $result = $course_msg->delete($id_str);
        if($result){
            $this->redirect('Question/course_com', '', 1, "<script>alert('删除成功')</script>");
        }else{
            $this->redirect('Question/course_com', '', 1, "<script>alert('删除失败')</script>");
        }
    }

    /**
     * 查询评论列表
     */
    public function search_course_com(&$where){
        $data = $where;
        $data = array_filter($data);
        if($data['content']){
            $data['content'] = array('like',"%{$data['content']}%");
        }
        if($data['time']){
            $data['time'] = array('like',"{$data['time']}%");
        }
        $where = $data;
    }

    /**
     * 查看评论页面
     */
    public function see(){
        $id = I('id');
        $course = M('course');
        $info = $course->where(array('id'=> $id))->field('title,content')->find();
        $this->assign('info',$info);
        $this->display();
    }
}

