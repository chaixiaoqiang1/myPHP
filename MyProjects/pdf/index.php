<?php
ini_set('gd.jpeg_ignore_warning',1);
/**
 * 默认展示页面
 *
 *
 **@copyright  Copyright (c) 2007-2013 ShopNC Inc.*/

defined('InShopNC') or exit('Access Invalid!');
class indexControl extends CollectionControl{
	 var $pdfformat = 0;//PDF导出是否是一个证件一个PDF,默认否
    public function __construct(){
        $table="card";
        parent::__construct($table);
        Language::read('index');
    }
    
    public function indexOp(){
        Language::read('home_index_index');
        Tpl::output('index_sign','index');
        Tpl::showpage('index');
    }
    
    public function checkOp(){
        Language::read('home_index_index');
        Tpl::output('index_sign','index');
        Tpl::showpage('check');
    }

    //判断是否登录
    public function loginOp(){
        echo ($_SESSION['is_login'] == '1')? '1':'0';
    }
    
    /**
    * 退出
    */
   public function logoutOp(){
       session_destroy();
       redirect('index.php?act=login');
   }
   /**
     * 上传文件
     *
     * @param null
     * @return json 下面数组的json串  
        array(            
            'status'=>1, //0失败1成功   
            'msg'=>'',//提示信息
            'pdfurl'=>'',//生成的pdf文件的网址
            'err_nums'=>'',//处理失败的编号
        )
    */
    public function card_upOp(){
        $model_obj = $this->_getModelMain();
        $msg_arr = array(
            'file_arr'=>$_FILES['file'],//文件数据数组$_FILES['file']
            'err_not_file'=>"没有选择文件 请您选择文件!",//$lang['nc_not_seled_file'],//没有选择文件 请您选择文件!
            'upload_dir'=>BASE_UPLOAD_PATH .DS,//BASE_DATA_PATH."/upload/",//上传全路径 E:/www/shoping/web/data/upload/
            'max_file_size'=>3*1024*1024,//3M
            'err_file_size'=>'文件大小超过限定值',//$lang['nc_max_file_size'],//超出文件大小提示 文件大小超过限定值
            'file_type'=>array('xls','xlsx'),//文件扩展名数组
            'err_file_type'=>'文件格式错误,允许格式为97-2003格式excel文件',//$lang['nc_excel_file_type'],//文件扩展名出错提示 '文件格式错误,允许格式为97-2003格式excel文件'
            'new_file_name'=>'input'.time(),//最新文件名称
        );
        $head_arr = array(
        	'page_number'=>!empty($_REQUEST['page_number'])?intval($_REQUEST['page_number']):1
        );//$model_obj->getImportHeadArr();
        if(isset($_REQUEST['card_id'])) $head_arr['card_id'] = intval($_REQUEST['card_id']);
        if(isset($_REQUEST['waybill'])) $head_arr['waybill'] = intval($_REQUEST['waybill']);
        if(isset($_REQUEST['card_number'])) $head_arr['card_number'] = intval($_REQUEST['card_number']);
        if(isset($_REQUEST['card_name'])) $head_arr['card_name'] = intval($_REQUEST['card_name']);
		if(isset($_REQUEST['type']) && $_REQUEST['type']== 'export') {
			$head_arr['type'] = $_REQUEST['type'];
			$head_arr['card_id'] = '';
			$head_arr['waybill'] = '';
			$head_arr['card_name'] = '';
			$head_arr['card_number'] = '';
		}
		if(isset($_REQUEST['pdfformat']) && intval($_REQUEST['pdfformat']) > 0) $this->pdfformat = intval($_REQUEST['pdfformat']);//PDF导出是一个证件一个PDF
        $re_arr = $this->fileUpXMLExcel($msg_arr,$head_arr);
        exit(json_encode($re_arr));
    }

    //批量输入 上传
    /*上传excel列的内容及顺序如下，顺序不能变动
     *  @param array $msg_arr 参数
        array(
            'continent_name'=>$aaa,//洲名称
            'continent_initial'=>$aaa,//洲首字母
            'continent_class'=>$aaa,//类别名称
            'class_id'=>$aaa,//所属地区id
            'continent_pic'=>$aaa,//图片
            'continent_sort'=>$aaa,//排序
            'continent_recommend'=>$aaa,//推荐，0为否，1为是，默认为0
            'store_id'=>$aaa,//店铺ID
            'continent_apply'=>$aaa,//洲申请，0为申请中，1为通过，默认为1，申请功能是会员使用，系统后台默认为1
            'show_type'=>$aaa,//洲展示类型 0表示图片 1表示文字 
        );
     * @param array $head_arr 数据库写入的数据头 array('id',......)

     * @return array 数组 
        array(            
            'status'=>1, //0失败1成功   
            'msg'=>'',//提示信息
            'pdfurl'=>'',//生成的pdf文件的网址
            'err_nums'=>'',//处理失败的编号
        )
     */
    protected function fileUpXMLExcel($msg_arr = array(),$head_arr = array()){ 
        $lang	= Language::getLangContent();
        //chmod($file, 777);
        $uplod_arr = upFileSingle($msg_arr);
        if(isset($uplod_arr['err_msg'])){
            return array(           
                'status'=>0, //0失败1成功   
                'msg'=>$uplod_arr['err_msg'],//提示信息
            );
        }
        $file = $uplod_arr['file'];
        $re_data = $this->dealExcelFileExcel($file,$head_arr);//解析文件
        return $re_data; 
    }
    
    /**
     * 解析文件
     *
     * @param string $file 解析文件//E:/www/shoping/web/data/upload/input.xls 
     * @param array $head_arr 数据库写入的数据头 array('id',......)
     * @return array 数组 
        array(            
            'status'=>1, //0失败1成功   
            'msg'=>'',//提示信息
            'pdfurl'=>'',//生成的pdf文件的网址
            'err_nums'=>'',//处理失败的编号
        )
     * @return  array  
        array(            
            'succ'=>array(), //有成功的   
            'fail'=>array()//有失败的
        )
     */
    public function dealExcelFileExcel($file,$head_arr){
        //$xls_arr=dealExcel($file);
        $xls_arr= $this->resolveExcel($file);
        unlink($file);//删除文件
        if(!is_array($xls_arr)){
            $xls_arr = array();
        }
        foreach($xls_arr as  $k=>$v){
            $is_need= false;
            foreach($v as $t_k=>$t_v){
                if(!empty($t_v) && trim($t_v)!=''){
                    $is_need= true;
                    break;
                }
            }
            if(!$is_need){
                unset($xls_arr[$k]) ;
            }
            
        }
        return $this->insertExcelDataExcel($xls_arr,$head_arr);
    }
    //解析excel文件
    public function resolveExcel($text_file){
        require_once BASE_RESOURCE_PATH . DS.'PHPExcel-1.8/Classes/PHPExcel.php';
        $text_file=iconv('UTF-8','GB2312',$text_file);
        $objPHPExcel = new PHPExcel();
        $objPHPExcel = PHPExcel_IOFactory::load($text_file);
        $xls_arr = $objPHPExcel->getActiveSheet()->toArray();
        if(isset($xls_arr[0])){unset($xls_arr[0]);}
        return $xls_arr;
    }
    /**
     * 插入数据
     *
     * @param array $data 数据库写入的数据 array('字段值')
     * @param array $head_arr 数据库写入的数据头 array('id',......)
     * @return array 数组 
        array(            
            'status'=>1, //0失败1成功   
            'msg'=>'',//提示信息
            'pdfurl'=>'',//生成的pdf文件的网址
            'err_nums'=>'',//处理失败的编号
        )
     * @return  array  
        array(            
            'succ'=>array(), //有成功的   
            'fail'=>array()//有失败的
        )
     */
    public function insertExcelDataExcel($data,$head_arr){
        $need_arr = array();
        foreach($data as  $k=>$v){
            foreach($v as $t_k=>$t_v){
                switch ($t_k)
                {
                    case 0://序号
                        $need_arr[$k]['card_id']=$t_v;
                        break;  
                    case 1://运单号
                        $need_arr[$k]['waybill']=$t_v;
                        break;  
                    case 2://姓名
                        $need_arr[$k]['card_name']=$t_v;
                        break;  
                    case 3://证件号码
                        $need_arr[$k]['card_number']=$t_v;
                        break;  
                    default:
                }
            }            
        }
        return $this->getExcelFile($need_arr,0,$head_arr);
    }

    public function getExcelFile($arr_per= array(),$is_img = 0,$head_arr = array()){  
        //$model_obj = $this->_getModelMain();
        $care_key= C('care_key');
        $date_dir = judge_date(TIMESTAMP,"Y/m/d");
        $dir_root=  DS . ATTACH_CARDTMP . DS .$date_dir;//  /card/2016/03/09
 
        $card_dir = BASE_UPLOAD_PATH ;    // D:/www/yundaex/data/upload 
        $file_dir = $card_dir .$dir_root .DS; // D:/www/yundaex/data/upload/card/2016/03/09/
		$page_number = isset($head_arr['page_number'])?$head_arr['page_number']:1;//每页生成个数
        //创建目录
        if (!is_dir($file_dir)) mk_dir($file_dir);
        
        //变量
        $pmz = "汉";	
        
        $rspic_path = $file_dir;//"rspic/";      
        import('libraries.idcart');
        $idcart_obj = new IdCart();
        //初始数据
        $arr_error =array();//找不到数据的card_id数组
        $idcard_arr=array();//放置图片的数组
		
        /************以上测试数据**********/
        foreach ($arr_per as $per )
        {
            $a = preg_replace('/^[(\xc2\xa0)|\s]+/', '',$per['card_name']);//str_replace(' ','',$per['card_name']);//姓名
            $b = preg_replace('/^[(\xc2\xa0)|\s]+/', '',$per['card_number']);//str_replace(' ','',$per['card_number']);//身份证
            $c = preg_replace('/^[(\xc2\xa0)|\s]+/', '',$per['card_id']);//str_replace(' ','',$per['card_id']);//序号
			$d = preg_replace('/^[(\xc2\xa0)|\s]+/', '',$per['waybill']);//str_replace(' ','',$per['waybill']);//运单号
			
            if($b!="" && $a!="")
            {
                //如果有身份证号码根据名称和号码查询，否则只根据姓名查询
                $sql="  Select t2.card_image_a,t2.card_image_b,t1.id_number  ";
                $sql.=" from yd_card as t1 ";
                $sql.=" left join yd_card_images as t2 on t1.card_id = t2.card_id ";
                $sql.=" where t1.state = 1 and t1.id_number='".cardEncrypt($b,$care_key)."' limit 0,1";
            	$sqlex = $this->getRsBySql($sql);
            	$nums = count($sqlex);
            	$rs = $this->getInfoByRs($sqlex);
             	if($nums!=0)
	            {
	            	$str = '';
					$card_number = empty($b)?cardDecrypt($rs['id_number'],$care_key):$b;
	            	if(isset($head_arr['card_id'])) $str .= $c;
	            	if(isset($head_arr['waybill'])) $str .= ' '.$d;
	            	if(isset($head_arr['card_name'])) $str .= ' '.$a;
	            	if(isset($head_arr['card_number'])) $str .=' '.$card_number;
	                //图片放入数组
	                $images = array(
	                    $card_dir. $rs['card_image_a'],
	                    $card_dir. $rs['card_image_b'],
	                    $str
	                );
	                if($this->pdfformat == 1 || $this->pdfformat == 2) $images[] = $card_number;
	                array_push($idcard_arr,$images);//主数组
	               
	            }else{
	            	
	            	
	                //查询临时表
	                $sqlt="Select * from yd_card_tmp where id_number='" .cardEncrypt($b,$care_key)."' limit 0,1";
	
	                $sqltex = $this->getRsBySql($sqlt);
	                $numst = count($sqltex);
	                $rst = $this->getInfoByRs($sqltex);
	                if($numst!=0)
	                {
	                	$str = '';
		            	if(isset($head_arr['card_id'])) $str .= $c;
		            	if(isset($head_arr['waybill'])) $str .= ' '.$d;
		            	if(isset($head_arr['card_name'])) $str .= ' '.$a;
		            	if(isset($head_arr['card_number'])) $str .=' '.$b;
	                    //图片放入数组
	                    $imagest = array(
	                    	$card_dir.$rst['card_image_a'],
	                    	$card_dir.$rst['card_image_b'],
	                    	$str
	                    );
	                    if($this->pdfformat == 1 || $this->pdfformat == 2) $imagest[] = $b;
	                    array_push($idcard_arr,$imagest);
	                }else {
	                	
	                        //如果主表和临时表都没有数据，按照身份证号码生成
	                        $b_str=substr($b,0,6);//获取区域代码
	                        $sqly="select * from yd_address_database where areacode='$b_str'";
	                        $sqlyex = $this->getRsBySql($sqly);
	                      
	                        if(empty($sqlyex)){
								$rand_num = $idcart_obj->getIdCardRand();
								
	                    		$sqladd = "select * from yd_address_database where address_id = '$rand_num'";//地址库
	                    		$sqlyex = $this->getRsBySql($sqladd);           
							}
	                        $row_nums = count($sqlyex);
	                        $array_row = array();
	                        $i=0;
	                        foreach($sqlyex as $ns_k=>$ns_v_arr){	
	                            $array_row[$i]=$ns_v_arr;
	                            $i++;
	                        }
	                        //如果区域代码表查出数据则生成，查不出则不生成
	                        if($row_nums>0)
	                        {
	                        
	                            //获取随机一条数据
	                            //if($row_nums>1){$row_nums=$row_nums-1;}
	                            //$s_num=rand(0,$row_nums);
	                            $s_num = array_rand($array_row,1);
	                            $arrar_rows = $array_row[$s_num];
	      
	                            $padd=$arrar_rows['address'];//地址
	                            $spart=$arrar_rows['fromdepart'];//发证机关
	
	                            //根据身份证获取性别
	                            $sex_w = substr($b,16,1);
	                            if($sex_w%2=='0'){$sex_flag='0';}else {$sex_flag='1';}
	
	                            if($sex_flag=='0')
	                            {
	                                $name_sex ="女";$name_sex_w=$idcart_obj->getEvenNum();
	                            }
	
	                            else if($sex_flag=='1') {
	                                $name_sex ="男";$name_sex_w=$idcart_obj->getOddNum();
	                            }
	
	                            else if($sex_flag==''){
	                                $sex_flag ='1';$name_sex ="男";$name_sex_w=$idcart_obj->getOddNum();
	                            }
	
	                            //取年月日
	                            $pbYMD=substr($b,6,8);
	                            $pbY=substr($pbYMD,0,4);//年
	                            $pbM=(int)substr($pbYMD,4,2);//月
	                            $pbD=(int)substr($pbYMD,6,2);//日
	
	                            //获取头像
	                            $head_image=$idcart_obj->getHeadImage($sex_flag,$pbY,$c);
	
	                            //放置图片的路径
	                            $b_encry = cardEncrypt($b,$care_key); 
	                            $path_z=$rspic_path.$b_encry.'A.jpg';
	                            $path_z_db = $dir_root .DS . $b_encry.'A.jpg';// /2016/03/09
	                            $path_b=$rspic_path.$b_encry.'B.jpg';
	                            $path_b_db = $dir_root .DS . $b_encry.'B.jpg';// /2016/03/09
	                            $str = '';
				            	if(isset($head_arr['card_id'])) $str .= $c;
				            	if(isset($head_arr['waybill'])) $str .= ' '.$d;
				            	if(isset($head_arr['card_name'])) $str .= ' '.$a;
				            	if(isset($head_arr['card_number'])) $str .=' '.$b;
	                            $images2 = array(
	                            	$card_dir.$path_z_db,
	                            	$card_dir.$path_b_db,
	                            	$str
	                            );
								if($this->pdfformat == 1 || $this->pdfformat == 2) $images2[] = $b;	
	                            //开始结束日期处理
	                            $start_end = $idcart_obj->getBTime($pbYMD); 
	                            $start_t=  str_replace('.','-',$start_end[0]);
	                            $end_t=  str_replace('.','-',$start_end[1]);
	                            $start_ends = $start_end[0]."-".$start_end[1];
	                            $start_ts = $idcart_obj->toUnixTime($start_t);
	                            if($end_t=="长期"){$end_ts='0';}else{$end_ts = $idcart_obj->toUnixTime($end_t);}
							
	                            $flag1 = $idcart_obj->getIdCarda($a,$b,$name_sex,$pmz,$pbY,$pbM,$pbD,$padd,$head_image,$path_z);//生成正面图
	                            $flag2 = $idcart_obj->getIdCardb($spart, $start_ends, $b,$path_b);//生成反面图
	
	
	                            //写入临时表
	                            if($flag1&&$flag2)
	                            {
	                                $sqlin="insert into yd_card_tmp set ";
	                                $sqlin .= "name='$a',";
	                                $sqlin .= "nation='$pmz',";
	                                $sqlin .= "id_number='$b_encry',";
	                                $sqlin .= "valid_start='$start_ts',";
	                                $sqlin .= "valid_end='$end_ts',";
	                                $sqlin .= "card_image_a='$path_z_db',";
	                                $sqlin .= "card_image_b='$path_b_db',";
	                                $sqlin .= "create_time=".time();
	                                if(!$this->updateBySql($sqlin)){echo "dia-!!!";}
	                            }
	                            array_push($idcard_arr,$images2);
	                           
	                        }else{
	                            array_push($arr_error, $c);
	                        }
	                 }
	              }
            }else{
            	array_push($arr_error, $c);
            }
            
           
        } 
        if(isset($head_arr['type']) && $head_arr['type'] == 'export'){
			$excel_name = DS ."excel" .  DS . TIMESTAMP . ".xlsx"; // /pdf/1456989937.excel
			$excel_path = BASE_UPLOAD_PATH . $excel_name;//pdf生成的路径 D:/www/yundaex/data/upload/pdf/1456989937excel
			//$excel_site_url = UPLOAD_SITE_URL. $pdf_name ; //http://www.yundaex.com/data/upload /card
			$excel_site_url = $excel_name; 
			//创建目录
			$excel_dir = dirname($excel_path);
			if (!is_dir($excel_dir)) mk_dir($excel_dir);
			$this->create_excel($idcard_arr,$excel_path);
			if (is_file($excel_path)){
				$re = array(            
					'status'=>1, //0失败1成功   
					'msg'=>'成功',//提示信息
					'excelurl'=>urlencode(cardEncrypt($excel_site_url,$care_key)),//生成的pdf文件的网址
					'err_nums'=>implode(',', $arr_error)//处理失败的编号
				);
				if($is_img == 1) $re['img_arr'] = $idcard_arr;
				return  $re;
			}else{  
				return array(            
					'status'=>0, //0失败1成功   
					'msg'=>'生成excel文件失败',//提示信息
				);
			}
		}else{
			if($this->pdfformat == 1){
				
				$dir_tmp_name = TIMESTAMP .'_'.mt_rand(1,99);
				$pdf_name = DS ."pdf" .  DS . $dir_tmp_name. ".zip";
				$pdf_path = BASE_UPLOAD_PATH.DS ."pdf" .  DS . $dir_tmp_name.DS;
				$pdf_site_url = $pdf_name; 
				//创建目录
				if (!is_dir($pdf_path)) mk_dir($pdf_path);
				if(!$this->create_one_pdf($idcard_arr,$pdf_path,'F',$page_number,BASE_UPLOAD_PATH.DS ."pdf" .  DS .$dir_tmp_name.".zip")){
					return array(            
						'status'=>0, //0失败1成功   
						'msg'=>'生成pdf压缩文件失败',//提示信息
					);
				}
				$pdf_path = BASE_UPLOAD_PATH.DS ."pdf" .  DS .$dir_tmp_name. ".zip";
			}else if($this->pdfformat == 2){
		
				$dir_tmp_name = TIMESTAMP .'_'.mt_rand(1,99);
				$pdf_name = DS ."pdf" .  DS . $dir_tmp_name. ".zip";
				$pdf_path = BASE_UPLOAD_PATH.DS ."pdf" .  DS . $dir_tmp_name.DS;
				$pdf_site_url = $pdf_name; 
				//创建目录
				if (!is_dir($pdf_path)) mk_dir($pdf_path);
				if(!$this->create_one_jpg($idcard_arr,$pdf_path,'F',$page_number,BASE_UPLOAD_PATH.DS ."pdf" .  DS .$dir_tmp_name.".zip")){
					return array(            
						'status'=>0, //0失败1成功   
						'msg'=>'生成jpg压缩文件失败',//提示信息
					);
				}
				$pdf_path = BASE_UPLOAD_PATH.DS ."pdf" .  DS .$dir_tmp_name. ".zip";
			}else{
				$pdf_name = DS ."pdf" .  DS . TIMESTAMP .'_'.mt_rand(1,99). ".pdf"; // /pdf/1456989937.pdf
				//exit;
				$pdf_path = BASE_UPLOAD_PATH . $pdf_name;//pdf生成的路径 D:/www/yundaex/data/upload/pdf/1456989937.pdf
				//$pdf_site_url = UPLOAD_SITE_URL. $pdf_name ; //http://www.yundaex.com/data/upload /card
				$pdf_site_url = $pdf_name; 
				//创建目录
				$pdf_dir = dirname($pdf_path);
				if (!is_dir($pdf_dir)) mk_dir($pdf_dir);
				
				$this->create_pdf($idcard_arr,$pdf_path,'F',$page_number);
			}
			if (is_file($pdf_path)){
			
				$re = array(            
					'status'=>1, //0失败1成功   
					'msg'=>'成功',//提示信息
					'pdfurl'=>urlencode(cardEncrypt($pdf_site_url,$care_key)),//生成的pdf文件的网址
					'err_nums'=>implode(',', $arr_error)//处理失败的编号
				);
				
				if($is_img == 1) $re['img_arr'] = $idcard_arr;
				return  $re;
			}else{
				return array(            
					'status'=>0, //0失败1成功   
					'msg'=>'生成pdf文件失败',//提示信息
				);
			}
		}
    }
    /*
    *下载Pdf
    */
	public function downPdfOp(){
		$care_key= C('care_key');
		$pdfurl = isset($_GET['pdfurl']) && !empty($_GET['pdfurl'])?urldecode($_GET['pdfurl']):'';
		$fileinfo = cardDecrypt($pdfurl,$care_key);//生成的pdf文件的网址
		$filename = basename($fileinfo);
		$fileinfo = BASE_UPLOAD_PATH.$fileinfo;
		$filesize = sprintf("%u", filesize($fileinfo));
		if(ob_get_length() !== false) @ob_end_clean();
		header('Pragma: public');
		header('Last-Modified: '.gmdate('D, d M Y H:i:s') . ' GMT');
		header('Cache-Control: private, max-age=0, must-revalidate');
		header('Cache-Control: pre-check=0, post-check=0, max-age=0');
		header('Content-Transfer-Encoding: binary');
		header('Content-Encoding: none');
		header('Content-Type: application/x-download');//为文件类型
		header('Content-Disposition: attachment; filename="'.$filename.'"');//文件名称
		header('Content-length: '.$filesize);//文件大小
		readfile($fileinfo);
		unlink($fileinfo);
		exit;
	}
	/*
    *下载excel
    */
	public function downExcelOp(){
		$care_key= C('care_key');
		$excelurl = isset($_GET['excelurl']) && !empty($_GET['excelurl'])?urldecode($_GET['excelurl']):'';
		$fileinfo = cardDecrypt($excelurl,$care_key);//生成的pdf文件的网址
		$filename = basename($fileinfo);
		$fileinfo = BASE_UPLOAD_PATH.$fileinfo;
		$filesize = sprintf("%u", filesize($fileinfo));
		if(ob_get_length() !== false) @ob_end_clean();
		// Redirect output to a client’s web browser (Excel2007)
		header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
		header('Content-Disposition: attachment;filename="'.$filename.'"');
		header('Cache-Control: max-age=0');
		// If you're serving to IE 9, then the following may be needed
		header('Cache-Control: max-age=1');
		
		// If you're serving to IE over SSL, then the following may be needed
		header ('Expires: Mon, 26 Jul 1997 05:00:00 GMT'); // Date in the past
		header ('Last-Modified: '.gmdate('D, d M Y H:i:s').' GMT'); // always modified
		header ('Cache-Control: cache, must-revalidate'); // HTTP/1.1
		header ('Pragma: public'); // HTTP/1.0
		header('Content-length: '.$filesize);//文件大小
		readfile($fileinfo);
		unlink($fileinfo);
		exit;
	}
    //获取性别
    public function getSex($a)
    {
        $a_prex= mb_substr($a,1,30,"utf-8");
        $sqlsex="select person_sex from yd_person where person_name='$a_prex' limit 0,1 ";
        $rssex=$this->getInfoBySql($sqlsex);        
        return $sex_flag=$rssex['person_sex'];
    }
    /*
	 *一条数据一个pdf文件
	 * */
    public function create_one_pdf($idcard_arr,$pdf_path,$output_type="I",$page_number = 1,$pdf_name = ''){
    	define('FPDF_FONTPATH',BASE_RESOURCE_PATH.DS.'fpdf/font/');
        require(BASE_RESOURCE_PATH.DS.'fpdf/fpdf.php');
        $idcard_count = count($idcard_arr);
        $x= 60;
		$y = 50;
		$x1= 60;
		$y1 =120;
		for ($i=0;$i<$idcard_count;$i++){
			
			
			$fpdf = new tFPDF('P', 'mm', 'A4');//创建新的FPDF对象，竖向放纸，单位为毫米，纸张大小A4
        	$fpdf->AddPage();
        	$fpdf->AddFont('DejaVu','','simfang.ttf',true);
			$fpdf->SetFont('DejaVu','',14);
			$pdf_path_name = $pdf_path.$idcard_arr[$i][3].'.pdf';
			$str = $idcard_arr[$i][2];
			$fpdf->Text(15,15,$str);
			
			if(file_exists($idcard_arr[$i][0])) $fpdf->Image($idcard_arr[$i][0], $x, $y);
			if(file_exists($idcard_arr[$i][1])) $fpdf->Image($idcard_arr[$i][1], $x1, $y1);
			$fpdf->Output($pdf_path_name, $output_type);
		}
		/*
		 * 压缩目录
		 * */
		$archive_name = $pdf_name; // name of zip file
 		$archive_folder = $pdf_path; // the folder which you archivate
		$zip = new ZipArchive; 
		if ($zip->open($archive_name, ZipArchive::CREATE) === TRUE) 
		{  
			$dir = $pdf_path;
			if (is_dir($dir)) {
			    if ($dh = opendir($dir)) {
			        while (($file = readdir($dh)) !== false) {
			        	 if ($file != '.' && $file != '..'){
			        	 	if(file_exists($dir.$file)){
			        	 		$zip->addFile($dir.$file,$file); 
			        	 	}
			        	 }
			        }
			        closedir($dh);
			    }
			}
		    $zip -> close(); 
		    $this->delDirAndFile($pdf_path,true);
		    return true;
		} 
		else 
		{ 
		    return false;
		}
    }
    
/*
	 *一条数据一个jpg文件
	 * */
    public function create_one_jpg($idcard_arr,$pdf_path,$output_type="I",$page_number = 1,$pdf_name = ''){
        $idcard_count = count($idcard_arr);
        $x= 60;
		$y = 50;
		$x1= 60;
		$y1 =120;
		for ($i=0;$i<$idcard_count;$i++){
			$pdf_path_name = $pdf_path.$idcard_arr[$i][3].'.jpg';
			
			//图片不存在时默认生成
			if(!file_exists($idcard_arr[$i][0])) $idcard_arr[$i][0] = BASE_ROOT_PATH.'/'.DIR_RESOURCE.'/'.'img/mr.jpg';
			if(!file_exists($idcard_arr[$i][1])) $idcard_arr[$i][1] = BASE_ROOT_PATH.'/'.DIR_RESOURCE.'/'.'img/mr.jpg';
			$this->create_jpg($idcard_arr[$i][0],$idcard_arr[$i][1],$pdf_path_name);
		}
		/*
		 * 压缩目录
		 * */
		$archive_name = $pdf_name; // name of zip file
 		$archive_folder = $pdf_path; // the folder which you archivate
		$zip = new ZipArchive; 
		if ($zip->open($archive_name, ZipArchive::CREATE) === TRUE) 
		{  
			$dir = $pdf_path;
			if (is_dir($dir)) {
			    if ($dh = opendir($dir)) {
			        while (($file = readdir($dh)) !== false) {
			        	 if ($file != '.' && $file != '..'){
			        	 	if(file_exists($dir.$file)){
			        	 		$zip->addFile($dir.$file,$file); 
			        	 	}
			        	 }
			        }
			        closedir($dh);
			    }
			}
		    $zip -> close(); 
		    $this->delDirAndFile($pdf_path,true);
		    return true;
		} 
		else 
		{ 
		    return false;
		}
    }
    
     public function create_jpg($jpe_path_z,$jpe_path_b,$create_jpg_name){
     
		//将人物和装备图片分别取到两个画布中
		$path_1_info   = getimagesize($jpe_path_z);
		$path_1_mime   = $path_1_info['mime'];
		$path_2_info   = getimagesize($jpe_path_b);
		$path_2_mime   = $path_2_info['mime'];
		switch ($path_1_mime)
		{
			case 'image/gif':
				$image_1 = imagecreatefromgif($jpe_path_z);
				break;
			case 'image/jpeg':
				$image_1 = imagecreatefromjpeg($jpe_path_z);
				break;
			case 'image/png':
				$image_1 = imagecreatefrompng($jpe_path_z);
				break;
			default:
				return false;
				break;
		}
		switch ($path_2_mime)
		{
			case 'image/gif':
				$image_2 = imagecreatefromgif($jpe_path_b);
				break;
			case 'image/jpeg':
				$image_2 = imagecreatefromjpeg($jpe_path_b);
				break;
			case 'image/png':
				$image_2 = imagecreatefrompng($jpe_path_b);
				break;
			default:
				return false;
				break;
		}
		//创建一个和人物图片一样大小的真彩色画布（ps：只有这样才能保证后面copy装备图片的时候不会失真）
		$image_3 = imageCreatetruecolor(334,440);
		//为真彩色画布创建白色背景，再设置为透明
		$color = imagecolorallocate($image_3, 255, 255, 255);
		imagefill($image_3, 0, 0, $color);
		imageColorTransparent($image_3, $color);
		//首先将人物画布采样copy到真彩色画布中，不会失真
		imagecopyresampled($image_3,$image_1,3,5,0,0,imagesx($image_1),imagesy($image_1),imagesx($image_1),imagesy($image_1));
		//再将装备图片copy到已经具有人物图像的真彩色画布中，同样也不会失真
		imagecopymerge($image_3,$image_2, 3,222,0,0,imagesx($image_2),imagesy($image_2), 100);
		//将画布保存到指定的jpg文件
		imagejpeg($image_3,$create_jpg_name);//文字生成的图片
		imagedestroy($image_3);
     }
	/*
	 *多条数据一个pdf文件
	 * */
    public function create_pdf($idcard_arr,$pdf_path,$output_type="I",$page_number = 1){
		define('FPDF_FONTPATH',BASE_RESOURCE_PATH.DS.'fpdf/font/');
        require(BASE_RESOURCE_PATH.DS.'fpdf/fpdf.php');
        $fpdf = new tFPDF('P', 'mm', 'A4');//创建新的FPDF对象，竖向放纸，单位为毫米，纸张大小A4
        $fpdf->AddPage();
        $fpdf->AddFont('DejaVu','','simfang.ttf',true);
		$fpdf->SetFont('DejaVu','',14);
        $idcard_count = count($idcard_arr);
        switch ($page_number){
			case 1:
				$x= 60;
				$y = 50;
				$x1= 60;
				$y1 =120;
				$j = 1;
				break;
			case 2:
				$x= 15;
				$y = 50;
				$x1= 105;
				$y1 =50;
				$x2= 15;
				$y2 = 150;
				$x3= 105;
				$y3 =150;
				$j = 2;
			break;
			case 3:
				$x= 15;
				$y = 30;
				$x1= 105;
				$y1 = 30;
				$x2= 15;
				$y2 = 115;
				$x3= 105;
				$y3 =115;
				$x4= 15;
				$y4 = 205;
				$x5= 105;
				$y5 =205;
				$j = 3;
				break;
			case 4:
				$x = 15;
				$y  = 15;
				$x1 = 105;
				$y1 = 15;
				$x2 = 15;
				$y2 = 85;
				$x3 = 105;
				$y3 = 85;
				$x4 = 15;
				$y4 = 155;
				$x5 = 105;
				$y5 =155;
				$x6 = 15;
				$y6 = 225;
				$x7 = 105;
				$y7 = 225;
				$j 	= 4;
				break;		
		}
		
		$page = 1;
		$total_page = ceil($idcard_count/$j);
		for ($i=0;$i<$idcard_count;$i+=$j){
			
			if($page_number == 1){
				$str = $idcard_arr[$i][2];
				$fpdf->Text(15,15,$str);
				if(file_exists($idcard_arr[$i][0])) $fpdf->Image($idcard_arr[$i][0], $x, $y);
				if(file_exists($idcard_arr[$i][1])) $fpdf->Image($idcard_arr[$i][1], $x1, $y1);
			}else if($page_number == 2){
				$str = $idcard_arr[$i][2];
				$fpdf->Text(15,15,$str);
				if(file_exists($idcard_arr[$i][0])) $fpdf->Image($idcard_arr[$i][0], $x, $y);
				if(file_exists($idcard_arr[$i][1])) $fpdf->Image($idcard_arr[$i][1], $x1, $y1);
				if(isset($idcard_arr[$i+1])){
					$str1 = $idcard_arr[$i+1][2];
					$fpdf->Text(15,125,$str1);	
					if(file_exists($idcard_arr[$i+1][0])) $fpdf->Image($idcard_arr[$i+1][0], $x2, $y2);
					if(file_exists($idcard_arr[$i+1][1])) $fpdf->Image($idcard_arr[$i+1][1], $x3, $y3);		
				}
			}else if($page_number == 3){
				$str = $idcard_arr[$i][2];
				$fpdf->Text(15,15,$str);
				if(file_exists($idcard_arr[$i][0])) $fpdf->Image($idcard_arr[$i][0], $x, $y);
				if(file_exists($idcard_arr[$i][1])) $fpdf->Image($idcard_arr[$i][1], $x1, $y1);	
				if(isset($idcard_arr[$i+1])){
					$str1 = $idcard_arr[$i+1][2];
					$fpdf->Text(15,105,$str1);
					if(file_exists($idcard_arr[$i+1][0])) $fpdf->Image($idcard_arr[$i+1][0], $x2, $y2);
				    if(file_exists($idcard_arr[$i+1][1])) $fpdf->Image($idcard_arr[$i+1][1], $x3, $y3);
				}
				if(isset($idcard_arr[$i+2])){
					$str2 = $idcard_arr[$i+2][2];
					$fpdf->Text(15,195,$str2);
					if(file_exists($idcard_arr[$i+2][0])) $fpdf->Image($idcard_arr[$i+2][0], $x4, $y4);
				    if(file_exists($idcard_arr[$i+2][1])) $fpdf->Image($idcard_arr[$i+2][1], $x5, $y5);   
				}
			}else if($page_number == 4){
				$str = $idcard_arr[$i][2];
				$fpdf->Text(15,10,$str);
				if(file_exists($idcard_arr[$i][0])) $fpdf->Image($idcard_arr[$i][0], $x, $y);
				if(file_exists($idcard_arr[$i][1])) $fpdf->Image($idcard_arr[$i][1], $x1, $y1);
				if(isset($idcard_arr[$i+1])){
					$str1 = $idcard_arr[$i+1][2];
					$fpdf->Text(15,80,$str1);
					if(file_exists($idcard_arr[$i+1][0])) $fpdf->Image($idcard_arr[$i+1][0], $x2, $y2);
				    if(file_exists($idcard_arr[$i+1][1])) $fpdf->Image($idcard_arr[$i+1][1], $x3, $y3);	
				}
				if(isset($idcard_arr[$i+2])){
					$str2 = $idcard_arr[$i+2][2];
					$fpdf->Text(15,150,$str2);
					if(file_exists($idcard_arr[$i+2][0])) $fpdf->Image($idcard_arr[$i+2][0], $x4, $y4);
				    if(file_exists($idcard_arr[$i+2][1])) $fpdf->Image($idcard_arr[$i+2][1], $x5, $y5);			
				}
				if(isset($idcard_arr[$i+3])){
					$str3 = $idcard_arr[$i+3][2];
					$fpdf->Text(15,220,$str3);
					if(file_exists($idcard_arr[$i+3][0])) $fpdf->Image($idcard_arr[$i+3][0], $x6, $y6);
				    if(file_exists($idcard_arr[$i+3][1])) $fpdf->Image($idcard_arr[$i+3][1], $x7, $y7);
					
				}
			}
			if($page < $total_page){$fpdf->AddPage();$page++;}
		}
		
	
        $fpdf->Output($pdf_path, $output_type);//I输出到浏览器D存送到浏览器F储存到本机档案
    }
	//生成excel
	public function create_excel($data,$excel_path){
		if(empty($data)) $data = array();
		foreach($data as $key =>$value){
			$data[$key] = explode(" ", $value[2]);
		}
		require_once BASE_RESOURCE_PATH . DS.'PHPExcel-1.8/Classes/PHPExcel.php';
		//创建新的PHPExcel对象
		$objPHPExcel = new PHPExcel();
		$objProps = $objPHPExcel->getProperties();
		$headArr = array("序号","运单号","姓名","证件号码");
		//设置表头
		$key = ord("A");
		foreach($headArr as $v){
			$colum = chr($key);
			$objPHPExcel->setActiveSheetIndex(0)->setCellValue($colum.'1', $v);
			$key += 1;
		}
		$column = 2;
		$objActSheet = $objPHPExcel->getActiveSheet();
		foreach($data as $key => $rows){ //行写入
			$span = ord("A");
			foreach($rows as $keyName=>$value){// 列写入
				$j = chr($span);
				$objActSheet->setCellValue($j.$column, $value);
				$span++;
			}
			$column++;
		}
		$objPHPExcel->getActiveSheet()->setTitle('Simple');
		$objPHPExcel->setActiveSheetIndex(0);
		$objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel2007');
		$objWriter->save($excel_path);
	}
    //根据sql获得rs
    public function getRsBySql($query_sql){
        return  Model()->query($query_sql);
    }
    
    //根据sql获得第一条记录
    public function getInfoBySql($query_sql){
        $rs = Model()->query($query_sql);
        if(count($rs)>0){
            return isset($rs[0])?$rs[0]:array();
        }
        return array();
    }
    //根据rs获得第一条记录
    public function getInfoByRs($rs){
         return isset($rs[0])?$rs[0]:array();
    }
    //根据更新sql更新数据记录
    public function updateBySql($update_sql){
        return  Model()->execute($update_sql);
    }

    public function searchOp(){
        Language::read('home_index_index');
        $lang	= Language::getLangContent();
        if (chksubmit()){
        	    $model_obj = $this->_getModelMain();
		        $operate_is_call = true;//是否调用 true 调用 false post
		        if(empty($param_arr)){$param_arr = array_merge($_GET, $_POST);$operate_is_call = false;}//如果为空，则用post的数据
		        $data_list = array();       
		        $arr_per = array();
	            $name = trim($param_arr['name']);
	            $id_number = trim($param_arr['id_number']);
	            if(empty($name)){
	            	 showDialog('姓名不能为空！','','error');
	            }
	            $need_arr = array();
	            $need_arr[0]['card_id'] = rand(1,200);
	            if(!empty($name)){
	                $need_arr[0]['card_name'] = $name;
	            }
	            if(!empty($id_number)){
	            	$need_arr[0]['card_number'] = $id_number;
	            }
	            $re_arr = $this->getExcelFile($need_arr,1);
        		if(isset($re_arr['img_arr']) && !empty($re_arr['img_arr'][0])){
        			$img1 = explode("upload",$re_arr['img_arr'][0][0]);
        			$img2 = explode("upload",$re_arr['img_arr'][0][1]);
        			$re_arr['img_arr'][0] = UPLOAD_SITE_URL.$img1[1];
        			$re_arr['img_arr'][1] = UPLOAD_SITE_URL.$img2[1];
        		}
        		Tpl::output('re_arr',$re_arr);
        		Tpl::showpage('check');
        }else{
        	showDialog('提交数据异常！','index.php','error');
        }
    }
   
   //删除压缩前文件夹及文件
   function delDirAndFile($path, $delDir = FALSE) {
    $handle = opendir($path);
    if ($handle) {
        while (false !== ( $item = readdir($handle) )) {
            if ($item != "." && $item != "..")
                is_dir("$path/$item") ? $this->delDirAndFile("$path/$item", $delDir) : unlink("$path/$item");
        }
        closedir($handle);
        if ($delDir)
            return rmdir($path);
    }else {
        if (file_exists($path)) {
            return unlink($path);
        } else {
            return FALSE;
        }
    }
	}
}
