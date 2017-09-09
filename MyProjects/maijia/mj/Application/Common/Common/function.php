<?php
//接口函数
function returnArray($ResCode, $ResData = '', $ResMsg,$count)
{
    $array = array(
        'resCode' => $ResCode,
        'resData' => $ResData?$ResData:'0',
        'resMsg' => $ResMsg,
    );
    if(is_array($ResData) && !$ResData){
        $array['resCode'] = 0;
    }

    if($count){
        $array['count'] = $count;
    }
    return json_encode($array);
}
//判断手机号码的合法性
function is_mobile($phoneNumber)
{
    if(preg_match("/^1[34578]\d{9}$/", $phoneNumber) ){
        return true;
    } else {
        return false;
    }
}


//上传文件配置
function upload()
{

        $config = array(
            //规定上传附件大小,约3M
            'maxSize'    =>    3145728,
            //规定上传的目录
            'savePath'   =>    'upload/',
            //规定上传根目录
            'rootPath'	 =>		'./Public/',
            //规定附件上传的格式
            'exts'       =>    array('jpg', 'gif', 'png', 'jpeg'),
            'autoSub'    =>    true,
            'subName'    =>    array('date','Ymd'),
        );
        // 实例化上传类
        return new \Think\Upload($config);
}

/**
 * 获取商品详情图片按比例缩放
 *  width / height 比例
 *  400 / 比例 获取缩放高度  
 * @return height
 */
function size($img){
   $data = getimagesize($img); 
   $rate = $data[0] / $data[1];
   return (int)(400 / $rate );
}


//上传多张图片
function duo_pic($file,& $err)
{
    $data = ims($file);
    $list = array();
    $up = upload();
    foreach($data as $k=>$v){
          $up->saveName = time().'_'.mt_rand();
          $im =  $up->uploadOne($v);
//        print_r($im);
        if($im){
            $list[] = $im;
//            var_dump($list);
        }else{
            foreach($list as $k=>$v){  //如果失败删除以前上传的图片
                $img = './Public/' . $v['savepath'] . $v['savename'];
                unlink($img);
            }
            $err = $up->getError();
            return false;
        }
    }
  
    return $list;
}

function ims($file){
    $data = array();
    $n = count($file['name']);
    if ($n == 1) {  //一张转成多张格式
        $data[0]['name'] = $file['name'];
        $data[0]['type'] = $file['type'];
        $data[0]['tmp_name'] = $file['tmp_name'];
        $data[0]['error'] = $file['error'];
        $data[0]['size'] = $file['size'];
    } else {    //多张
        for ($i = 0; $i < $n; $i++) {
            $data[$i]['name'] = $file['name'][$i];
            $data[$i]['type'] = $file['type'][$i];
            $data[$i]['tmp_name'] = $file['tmp_name'][$i];
            $data[$i]['error'] = $file['error'][$i];
            $data[$i]['size'] = $file['size'][$i];
        }
    }
    return $data;
}
//上传一张图片
function one_pic($file){
    $up = upload();
    $info  = upload()->uploadOne($file);
    if($info){
        return $info; //ok
    }else{
        return false;
    }
}
//一张缩略图
function oneThumb($info,$site,$w,$h){
    $img = './Public/' . $info['savepath'] . $info['savename'];
    if(trim($site) ){
        $suo = './Public/upload/' .$site.'/' . 'thumb' . $info['savename'];
    }else{
        $suo = './Public/upload/' . 'thumb' . $info['savename'];
    }
    $image = new \Think\Image();
    $image->open($img);
    $image->thumb($w, $h)->save($suo);
    unlink($img);
    return $suo;
}

//生成多张缩略图
/*
 * Thumbs($p,'goods',array(
                                                array('width'=>450,'height=>300'),
                                                array('width'=>200,'height=>200'),
                                            ));
 */
function Thumbs($info,$site,$arr){
    $img = './Public/' . $info['savepath'] . $info['savename'];
    $image = new \Think\Image();
    $image->open($img);
    $pic = array();
    for($i=0,$num = count($arr); $i<$num; $i++){
        if(trim($site) ){
            $suo = './Public/upload/' .$site.'/' . 'thumb'.$i . $info['savename'];
        }else{
            $suo = './Public/upload/' . 'thumb'.$i . $info['savename'];
        }
        $image->thumb($arr[$i]['width'], $arr[$i]['height'])->save($suo);
        $pic[] = $suo;
    }
    unlink($img);
    return $pic;
}

function p($data){
    if(empty($data) ){
        dump($_REQUEST);
    }else{
        dump($data);
    }
}
