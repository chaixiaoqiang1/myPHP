<?php
namespace Admin\Controller;
use Monolog\Handler\MailHandler;
use Think\Controller;
class BannerController extends CommonController
{

    /**
     * Í¼Æ¬¹ÜÀíÊ×Ò³
     */
    public function index()
    {
        $bannerCount=M("banner")->count("content");
        $Page=new \Think\Page($bannerCount,5);
        setPage($Page);
        $show=$Page->show();
        $banners=M("banner")->limit($Page->firstRow,$Page->listRows)->select();
        
        $this->assign("show",$show);
        $this->assign("Count",$bannerCount);
        $this->assign("banners",$banners);
        $this->display();
    }

    /**
     * Ìí¼ÓÍ¼Æ¬
     */
    public function add(){
        $image = new \Think\Image();
        $upload=upload();
        if($_POST!=NULL) {
            $info = $upload->upload();
            $_POST['content']="./Public/".$info['photo']['savepath'].$info['photo']['savename'];
            $_POST['size']=I("post.height")."x".I("post.width");
            $_POST['time']=date("Y-m-d H:i:s");
        };
        if($info){
            $image->open($_POST['content']);
            $image->thumb(I("post.width"), I("post.height"),\Think\Image::IMAGE_THUMB_FIXED)->save($_POST['content']);
        }
        if($info!=NULL){
            $result=M("banner")->add($_POST);
            $this->redirect("Banner/index");
        }



        $this->display();
    }

    /**
     * Í¼Æ¬ĞŞ¸Ä
     */
    public function editor(){
        $id=I("get.id");
        $image = new \Think\Image();
        $upload=upload();
        
        if($_POST){
            $info=$upload->upload();
            if($_FILES['photo']['name']){
                $_POST['content']="./Public/".$info['photo']['savepath'].$info['photo']['savename'];
                $image->open($_POST['content']);
                $image->thumb(I("post.width"), I("post.height"),\Think\Image::IMAGE_THUMB_FIXED)->save($_POST['content']);
            }
            $_POST['size']=$_POST['height'].'x'.$_POST['width'];
            $_POST['time']=date("Y-m-d H:i:s");
            $re=M("banner")->where("id=$id")->save($_POST);
            if($re){
                $this->redirect("Banner/index");
            }else{
                $this->redirect('Banner/index','',0,"<script>alert('ĞŞ¸ÄÊ§°Ü')</script>");
            }
        }

    }

    /**
     * Í¼Æ¬É¾³ı
     */
    public function delete(){
        $id=I("post.id");
        $result=M("banner")->where("id=$id")->delete();
        if($result){
            echo "OK";
        }
    }
    /**
    *   Í¼Æ¬×´Ì¬ĞŞ¸Ä
     **/
    public function stop(){
        $id=I("post.id");
        $status=M("banner")->where("id=$id")->field("status")->find();
        if($status['status']==1){
            $result=M("banner")->where("id=$id")->data("status=0")->save();
        }else{
            $result=M("banner")->where("id=$id")->data("status=1")->save();
        }
        if(result){
            echo 1;
        }else{
            echo 0;
        }
    }
    /**
     *   Í¼Æ¬±à¼­Ò³Ãæ
     **/
    public function update(){
        $id=I("get.id");
        $banner=M("banner")->where("id=$id")->find();
        $size=explode("x",$banner['size']);
        $width=$size[1];$height=$size[0];

        $this->assign("width",$width);
        $this->assign("height",$height);
        $this->assign("banner",$banner);
        $this->display(update);
    }


}

