<?php
namespace Admin\Controller;
use Monolog\Handler\MailHandler;
use Think\Controller;
use Think\Image;
use Think\Think;

class BrandController extends CommonController
{

    /**
     * 品牌列表
     */
    public function index()
    {
        $key=I("get.key");
        $time=I("get.time");
        if(IS_GET){
            if($key!=null){
                $where['name']=array("like","%{$key}%");
            }
            if($time!=null){
                $where['time']=array("like","%{$time}%");
            }
            $brandCount=M("brands")->where($where)->count();
            $Page=new \Think\Page($brandCount,5);
            $brands=M("brands")->where($where)->order("`order`")->limit($Page->firstRow,$Page->listRows)->select();
        }else{
            $brandCount=M("brands")->count();
            $Page=new \Think\Page($brandCount,5);
            $brands=M("brands")->order("`order`")->limit($Page->firstRow,$Page->listRows)->select();
        }

        setPage($Page);
        $listShow=$Page->show();

        $this->assign("listShow",$listShow);
        $this->assign("brands",$brands);
        $this->assign("count",$brandCount);
        $this->display();
    }

    /**
     * 品牌添加
     */
    public function add(){
        $image = new \Think\Image();
        $upload=upload();
        if($_POST!=NULL){
            $info=$upload->upload();
            $_POST['img']="./Public/".$info['goods_img']['savepath'].$info['goods_img']['savename'];
            $_POST['size']=$_POST['height'].'x'.$_POST['width'];
            $_POST['time']=date("Y-m-d H:i:s");
        }
        if($info){
            $image->open($_POST['img']);
            $image->thumb(I('post.width'),I('post.height'),\Think\Image::IMAGE_THUMB_FIXED)->save($_POST['img']);
        }
        if($info!=null){
            M("brands")->add($_POST);
            $this->redirect("Brand/index");
        }
        $this->display();
    }

    /**
     * 品牌修改
     */
    public function editor(){
        $id=I("get.id");
        $upload=upload();
        $Image= new \Think\Image();
        if($_POST){
            $info = $upload->upload();
            if ($_FILES['goods_img']['name']){
                $_POST['img'] = "./Public/" . $info['goods_img']['savepath'] . $info['goods_img']['savename'];
                $Image->open($_POST['img']);
                $Image->thumb(I("post.width"), I("post.height"), \Think\Image::IMAGE_THUMB_FIXED)->save($_POST['img']);
            }
            $_POST['size'] = $_POST['height'] . 'x' . $_POST['width'];
            $_POST['time'] = date("Y-m-d H:i:s");


            $result = M("brands")->where("id=$id")->save($_POST);
            if ($result) {
                $data['brands_name'] = $_POST['name'];
                M("cart")->where("brands_id=$id")->field("brands_name")->save($data);
                M('evaluate')->where("brands_id=$id")->field("brands_name")->save($data);
                $this->redirect("Brand/index");
            } else {
                $this->redirect("Brand/index", '', 1, "<script>alert('修改失败')</script>");
            }
        }

    }

    /**
     * 品牌删除
     */
    public function delete(){
        $id=I("post.id");
        $result=M("brands")->where("id=$id")->delete();
        if($result){
            echo "OK";
        }
    }
    /**
     * 品牌状态修改
     */
    public function stop(){
        $id=I("post.id");
        $status=M("brands")->where("id=$id")->field("status")->find();
        if($status['status']==1){
            $result=M("brands")->where("id=$id")->data("status=0")->save();
        }else{
            $result=M("brands")->where("id=$id")->data("status=1")->save();
        }
        if($result){
            echo 1;
        }else{
            echo 0;
        }
    }
    /**
     * 品牌编辑页面
     */
    public function update(){
        $id=I("get.id");
        $brand=M("brands")->where("id=$id")->find();
        $size=explode("x",$brand['size']);
        $height=$size[0];$width=$size[1];

        $this->assign("width",$width);
        $this->assign("height",$height);
        $this->assign("brand",$brand);
        $this->display();
    }
    /**
     * 全部删除
     */
    public function delete_all(){
        $str=I("get.id");
        $id=rtrim($str,',');
        $result=M("brands")->where("id in ({$id})")->delete();
        if($result){
            $this->redirect("Brand/index");
        }
    }
    /**
     * 品牌排序修改
     */
    public function reorder(){
        $id=I("post.id");
        $data['order']=I("post.order");
        $result=M("brands")->where("id=$id")->data($data)->save();
        if($result){
            echo $data['order'];
        }
    }

    /**
     * 车系列表
     */
    public function demio(){

        if(IS_POST){
            if($_POST['brands_id']){
                $where['brands_id']=$_POST['brands_id'];
            }
            if($_POST['demio_name']){
                $where['demio_name']=array('like',"%".$_POST['demio_name']."%");
            }
        }
        $where['pid']=0;

        $count=M('Demio')->where($where)->count();
        $Page= new \Think\Page($count,15);
        setPage($Page);
        $show= $Page->show();
        $list=M('Demio')->where($where)->order('id desc')->limit($Page->firstRow.','.$Page->listRows)->select();
        if($list){
            $id_str=implode(',',array_column($list,'id'));
            $map['pid']=array('in',$id_str);
            $child_all=M('Demio')->where($map)->select();
            foreach($list as $k=>$v){
                $arr[$v['id']]=$v;
            }
            foreach($child_all as $k=>$v){
                $arr[$v['pid']]['child'][]=$v;
            }
        }else{
            $arr=array();
        }
        $count=M('demio')->count();
        $brands=M('brands')->field('id,name')->select();
        $this->assign('count',$count);
        $this->assign('brands',$brands);
        $this->assign('show',$show);
        $this->assign('category',$arr);
        $this->display();
    }

    /**
     * 添加子级分类
     */
    public function addChild(){
        if($_POST==NULL) {
            $id = I('get.id');
            $demio = M('demio')->where("id=$id")->find();
        }else {
            $str = explode('/', I('post.pid'));
            $data = array(
                'brands_id' => $str[1],
                'demio_name' => I('post.demio_name'),
                'pid' => $str[0],
                'add_time' => date('Y-m-d H:i:s', time()),
                'status' => I('post.status')
            );
            $result=M('demio')->add($data);
            if(result){
                $this->redirect('Brand/demio');
            }
        }

        $this->assign('demio',$demio);
        $this->display();
    }

    /**
     * 车系修改
     */
    public function demio_update(){
        $id=I('get.id');
        $res=M('demio')->where("id=$id")->find();
        if($res['pid']==0){
           $demio_name=M('brands')->field('id,name')->select();
        }else{
            $demio_name=M('demio')->where("pid=0")->field('id,demio_name')->select();
        }
        if($_POST){
            if($res['pid']==0){
                $data=array(
                  'brands_id' =>I('post.brands_id'),
                  'demio_name'=>I('post.demio_name'),
                   'add_time' =>date('Y-m-d H:i:s',time()),
                    'status'  =>I('post.status')
                );
             $re=M('demio')->where("id=$id")->save($data);
              if($re){
                  $data['demio']=I('post.demio_name');
                  M('cart')->where("demio_id=$id")->field('demio')->save($data);
                  M('evaluate')->where("demio_id=$id")->field('demio')->save($data);
                     }else{
                  $this->redirect('Brand/demio','',1,"<script>alert('修改失败')</script>");
                     }
            }else{
                $data=array(
                  'brands_id' =>$res['brands_id'],
                  'demio_name'=>I('post.demio_name'),
                  'pid'       =>I('post.pid'),
                  'add_time'  =>date('Y-m-d H:i:s',time()),
                  'status'    =>I('post.status')
                );
             $re=M('demio')->where("id=$id")->save($data);
                if($re){
                    $data['model']=I('post.demio_name');
                    M('cart')->where("model_id=$id")->field('model')->save($data);
                    M('evaluate')->where("model_id=$id")->field('model')->save($data);
                       }else{
                    $this->redirect('Brand/demio','',1,"<script>alert('修改失败')</script>");
                        }
            }
            $this->redirect('Brand/demio');
        }

        $this->assign("res",$res);
        $this->assign('pid',$res['pid']);
        $this->assign('demio_name',$demio_name);
        $this->display('editor');
    }

    /**
     * 车系删除
     */
    public function demio_delete(){
        $id=I('get.id');
        $result=M('demio')->where("id=$id")->delete();
        if($result){
            $re=M('demio')->where("pid=$id")->delete();
        }
        $this->redirect('Brand/demio');
    }
    /**
     * 车系添加
     */
    public function  add_demio(){
        $brands=M("brands")->field("id,name")->select();
        $add_time=date('Y-m-d H:i:s',time());
        $data=array(
            'brands_id'=>I('post.brands_id'),
            'demio_name'=>I('post.demio_name'),
            'status'=>I('post.status'),
            'add_time'=>$add_time
        );
        if($_POST) {
            $result = M('demio')->add($data);
            if ($result) {
                $this->redirect('Brand/demio');
            }
        }

        $this->assign("brands",$brands);
        $this->display();
    }
}

