<?php
namespace Admin\Controller;
use Think\Controller;
class CartController extends CommonController
{

    /**
     * 车辆列表
     */
    public function index()
    {
        if(IS_POST){
            if($_POST['brands']){
                $where['brands_id']=$_POST['brands'];
            }
            if($_POST['demio_id']){
                $where['demio_id']=$_POST['demio_id'];
            }
            if($_POST['model_id']){
                $where['model_id']=$_POST['model_id'];
            }
            if($_POST['add_time']){
                $where['add_time']=array('like',"{$_POST['add_time']}%");
            }

            $count=M('cart')->where($where)->count();
            $Page=new \Think\Page($count,5);
            setPage($Page);
            $show=$Page->show();
            $cartinfo=M('cart')->where($where)->limit($Page->firstRow.','.$Page->listRows)->select();
            foreach($cartinfo as $k=>$v){
                $cartinfo[$k]['image']=explode('*',substr($v['image'],0,-1))[0];
            }
        }else{
            $cartinfo=M('cart')->select();
            $count=M('cart')->count();
            $Page=new \Think\Page($count,5);
            setPage($Page);
            $show=$Page->show();
            $cartinfo=M('cart')->limit($Page->firstRow.','.$Page->listRows)->select();
            foreach($cartinfo as $k=>$v){
                $cartinfo[$k]['image']=explode('*',substr($v['image'],0,-1))[0];
            }
        }

        $brands=M('brands')->field('id,name')->select();
        $this->assign('show',$show);
        $this->assign('count',$count);
        $this->assign('brands',$brands);
        $this->assign('cartinfo',$cartinfo);
        $this->display();
    }

    /**
     * 车辆添加
     */
    public function add(){
        $brands=M('brands')->field('id,name')->select();
        if($_POST){
            $img_list=$this->gain_file($_FILES['goods_img']['name'],'goods_img');
            $infos=$this->public_upload($img_list);
            foreach($infos as $key=>$val){
                $img = './Public/' . $infos[$key]['savepath'] . $infos[$key]['savename'];
                $image = new \Think\Image();
                $image->open($img);
                $image->thumb(I('post.width'),I('post.height'),\Think\Image::IMAGE_THUMB_FIXED)->save('./Public/' . $infos[$key]['savepath'] . 'thumb_' . $infos[$key]['savename']);
                $thumb_url = './Public/' . $infos[$key]['savepath'] . 'thumb_' . $infos[$key]['savename'];
                $img_path[] = $thumb_url;
                unlink($img);
            }
            $str="";
            foreach($img_path as $v){
                $str.=$v.'*';
            }

            if($str){
                $brands_id=I('post.brands');
                $demio_id=I('post.demio_id');
                $model_id=I('post.model_id');
                $size=I('post.height').'x'.I('post.width');
                $brands_name=M('brands')->where("id=$brands_id")->getField('name');
                $demio_name=M('demio')->where("id=$demio_id")->getField('demio_name');
                $model_name=M('demio')->where("id=$model_id")->getField('demio_name');
                $data=array(
                    'size'  =>$size,
                    'price' =>I('post.price'),
                    'demio_id'      =>$demio_id,
                    'demio'         =>$demio_name,
                    'brands_id'     =>$brands_id,
                    'brands_name'   =>$brands_name,
                    'model_id'      =>$model_id,
                    'model'         =>$model_name,
                    'status'        =>I('post.status'),
                    'image'         =>$str,
                    'add_time'      =>date('Y-m-d : H:i:s',time())
                );
                $result=M('cart')->add($data);
                if($result){
                    $this->redirect('Cart/index');
                }else{
                    $this->redirect('Cart/add','',1,"<script>alert('添加失败')</script>");
                }

            }
        }
        $this->assign('brands',$brands);
        $this->display();
    }

    /**
     * 车辆修改
     */
    public function editor()
    {
        $id = I('get.id');
        $brands = M('brands')->field('id,name')->select();
        $cartInfo = M('cart')->where("id=$id")->find();
        $demio_list = M('Demio')->where(array('brands_id' => $cartInfo['brands_id'], 'pid' => 0))->select();
        $mode_list = M('Demio')->where(array('pid' => $cartInfo['demio_id']))->select();
        $size = explode('x', $cartInfo['size']);

        $cartInfo['image']=explode('*',substr($cartInfo['image'],0,-1));

        $this->assign('size', $size);
        $this->assign('demio_list', $demio_list);
        $this->assign('mode_list', $mode_list);
        $this->assign('cartInfo', $cartInfo);
        $this->assign("brands", $brands);
        $this->display();
    }

    public function update(){
        $id=I('post.id');
        if($_FILES['goods_img']['name'][0]){
            $imginfo=M('cart')->where("id=$id")->getField("image");
            $imageinfo=explode('*',$imginfo);
            foreach($imageinfo as $k=>$v){
                unlink($v);
            }
         //上传图片操作  unlink();
            $img_list=$this->gain_file($_FILES['goods_img']['name'],'goods_img');
            $infos=$this->public_upload($img_list);
            foreach($infos as $key=>$val){
                $img = './Public/' . $infos[$key]['savepath'] . $infos[$key]['savename'];
                $image = new \Think\Image();
                $image->open($img);
                $image->thumb(I('post.width'),I('post.height'),\Think\Image::IMAGE_THUMB_FIXED)->save('./Public/' . $infos[$key]['savepath'] . 'thumb_' . $infos[$key]['savename']);
                $thumb_url = './Public/' . $infos[$key]['savepath'] . 'thumb_' . $infos[$key]['savename'];
                $img_path[] = $thumb_url;
                unlink($img);
            }
            $str="";
            foreach($img_path as $v){
                $str.=$v.'*';
            }

            if($str){
                $brands_id=I('post.brands');
                $demio_id=I('post.demio_id');
                $model_id=I('post.model_id');
                $size=I('post.height').'x'.I('post.width');
                $brands_name=M('brands')->where("id=$brands_id")->getField('name');
                $demio_name=M('demio')->where("id=$demio_id")->getField('demio_name');
                $model_name=M('demio')->where("id=$model_id")->getField('demio_name');
                $data=array(
                    'size'  =>$size,
                    'price' =>I('post.price'),
                    'demio_id'      =>$demio_id,
                    'demio'         =>$demio_name,
                    'brands_id'     =>$brands_id,
                    'brands_name'   =>$brands_name,
                    'model_id'      =>$model_id,
                    'model'         =>$model_name,
                    'status'        =>I('post.status'),
                    'image'         =>$str,
                    'add_time'      =>date('Y-m-d : H:i:s',time())
                );
                $result=M('cart')->where("id=$id")->save($data);
                if($result){
                    $this->redirect('Cart/index');
                }else{
                    $this->redirect('Cart/editor','',1,"<script>alert('修改失败')</script>");
                }

            }
        }else{
            $brands_id=I('post.brands');
            $demio_id=I('post.demio_id');
            $model_id=I('post.model_id');
            $size=I('post.height').'x'.I('post.width');
            $brands_name=M('brands')->where("id=$brands_id")->getField('name');
            $demio_name=M('demio')->where("id=$demio_id")->getField('demio_name');
            $model_name=M('demio')->where("id=$model_id")->getField('demio_name');
            $data=array(
                'size'  =>$size,
                'price' =>I('post.price'),
                'demio_id'      =>$demio_id,
                'demio'         =>$demio_name,
                'brands_id'     =>$brands_id,
                'brands_name'   =>$brands_name,
                'model_id'      =>$model_id,
                'model'         =>$model_name,
                'status'        =>I('post.status'),
                'add_time'      =>date('Y-m-d : H:i:s',time())
            );
            $result=M('cart')->where("id=$id")->field('size,price,demio_id,demio,brands_id,brands_name,model_id,model,status,add_time')->save($data);
            if($result){
                $this->redirect('Cart/index');
            }else{
                $this->redirect('Cart/editor','',1,"<script>alert('修改失败')</script>");
            }
        }

        print_r($_FILES);
        print_r($_POST);die;
    }

    /**
     * 车辆删除
     */
    public function delete(){
        $id=I('post.id');
        $result=M('cart')->where("id=$id")->delete();
        if($result){
            echo OK;die;
        }else{
            echo NO;die;
        }
    }
    /**
     * 车辆选择
     */
    public function query(){
        if(in_array('',$_POST)){
            echo 0;die;
        }
        if($_POST['type'] == 1){
            $where['brands_id']=$_POST['id'];
            $where['pid']=0;
        }else{
            $where['pid']=$_POST['id'];
        }
        $list=M('demio')->where($where)->select();
        if($list){
            echo json_encode($list);die;
        }else{
            echo 0;die;
        }
    }
    /**
     * 车辆状态修改
     */
    public function status(){
        $id=I('post.id');
        $status=M('cart')->where("id=$id")->getField('status');
        if($status==1){
            $result=M('cart')->where("id=$id")->data("status=0")->save();
        }else{
            $result=M('cart')->where("id=$id")->data("status=1")->save();
        }
        if($result){
            echo 1;die;
        }else{
            echo 0;die;
        }
    }
    /**
     * 车辆全部删除
     */

    public function delete_all(){
        $str=I('get.id');
        $where=rtrim($str,',');
        $result=M("cart")->where("id in ({$where})")->delete();
        if($result){
            $this->redirect("Cart/index");
        }else{
            $this->redirect('Cart/index','',1,"<script>alert('删除失败')</script>");
        }
    }
}

