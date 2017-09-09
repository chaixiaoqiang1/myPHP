<?php
/**
	通过IMAP方式收取邮件，需要php5-imap扩展支持
*/
class AFLMailController extends \BaseController{  
    protected $server;  
    protected $username;  
    protected $password;  
    protected $marubox;                      
    protected $email;

    public function connect($params = array()){//Connect To the Mail Box  
    	$this->server = $params['server'];
    	$this->username = $params['username'];
    	$this->password = $params['password'];
        $this->marubox = @imap_open($this->server,$this->username,$this->password); //开启邮箱 
        return $this->marubox; 
    }  
      
    public function getTotalMails(){ //Get Total Number off Unread Email In Mailbox
        if(!$this->marubox) return false;  

        return imap_num_msg($this->marubox);  
    }  

    public function getCentainMails($condition){
    	if(!$this->marubox) return false;  

        return imap_search($this->marubox, $condition, 0);  
    }

    public function getText($mid){
    	if(!$this->marubox) return false;  

        return imap_body($this->marubox, $mid);  
    }

    public function uid2mid($uid){
    	if(!$this->marubox) return false;  

        return imap_msgno($this->marubox, $uid);  
    }

    public function getHeader($mid){ // Get Header info  
        if(!$this->marubox) return false;  
  
        $mail_header = imap_headerinfo($this->marubox,$mid);  
        $sender = $mail_header->from[0];  
        $sender_replyto = $mail_header->reply_to[0];  
        if(strtolower($sender->mailbox)!='mailer-daemon' && strtolower($sender->mailbox)!='postmaster'){//mailer-daemon：邮件病毒，postmaster：退信 
            $subject = $this->decode_mime($mail_header->subject);  
  
            $ccList = array();  
            if(isset($mail_header->cc)){
	            foreach($mail_header->cc as $k => $v){  
	                $ccList[]=$v->mailbox.'@'.$v->host;  
	            }  
            }
            $toList = array();  
            foreach($mail_header->to as $k => $v){  
                $toList[]=$v->mailbox.'@'.$v->host;  
            }  
            $ccList = implode(",", $ccList);  
            $toList = implode(",", $toList);  
            $mail_details=array(  
                    'fromBy'=>strtolower($sender->mailbox).'@'.$sender->host,  
                    'fromName'=>$this->decode_mime($sender->personal),  
                    'ccList'=>$ccList,
                    'toNameOth'=>$this->decode_mime($sender_replyto->personal),  
                    'subject'=>$subject,  
                    'mailDate'=>date("Y-m-d H:i:s", $mail_header->udate),  
                    'udate'=>$mail_header->udate,  
                    'toList'=>$toList, 
                );  
        }  
        return $mail_details;  
    }

    //转换邮件标题的字符编码，处理乱码  
    private function decode_mime($str){  
        $mime_obj = imap_mime_header_decode($str);
        if($mime_obj[0]->charset != "default"){
        	// $res_text = iconv($mime_obj[0]->charset, 'utf8', $mime_obj[0]->text);
        	$res_text = mb_convert_encoding($mime_obj[0]->text, 'utf-8', $mime_obj[0]->charset);
        }
        else{
        	$res_text = $mime_obj[0]->text;
        }
        return $res_text; 
    }

    public function getBody($mid, &$path, $imageList, $onlytext = 0){ // Get Message Body  
        if(!$this->marubox) return false;  
  
        $body = $this->get_part($this->marubox, $mid, "TEXT/HTML");  
        
        if($body == "") $body = $this->get_part($this->marubox, $mid, "TEXT/PLAIN");  
        
        if($body == "")  return "";  
        //处理图片  
        if(!$onlytext){
        	$body = $this->embed_images($body, $path, $imageList);  
        }
        return $body;  
    }

    private function get_part($stream, $msg_number, $mime_type, $structure=false, $part_number=false){ //Get Part Of Message Internal Private Use  
        if(!$structure){   
            $structure = imap_fetchstructure($stream, $msg_number);   
        }   
        if($structure){  
        	$prefix = ''; 
            if($mime_type == $this->get_mime_type($structure)){   
                if(!$part_number){   
                    $part_number = "1";   
                }   
                $text = imap_fetchbody($stream, $msg_number, $part_number);  
                
                //encoding：0:七位(7 bit)，1:八位(8 bit)，2:二进位(binary)，3:BASE64 编码，4:QP 编码(QuotedPrintable)，5:其它
                if($structure->encoding == 3){
                    return imap_base64($text);
                }  
                else if($structure->encoding == 4){  
                    return mb_convert_encoding(imap_qprint($text), 'utf-8', 'gb2312');  
                }  
                else{  
                    return mb_convert_encoding($text, 'utf-8', 'gb2312');  
                }  
            }   
            //type：0:文字 text，1:复合 multipart，2:信息 message，3:程序 application，4:声音 audio，5:图形 image，6:影像 video，7:其它 other 
            if($structure->type == 1){
                while(list($index, $sub_structure) = each($structure->parts)){   
                    if($part_number){   
                        $prefix = $part_number.'.';   
                    }   

                    $data = $this->get_part($stream, $msg_number, $mime_type, $sub_structure, $prefix.($index + 1));   
                    if($data){   
                        return $data;   
                    }   
                }   
            }   
        }  
        return false;   
    }   
    
    //获取mime类型
    private function get_mime_type(&$structure){ //Get Mime type Internal Private Use  
        $primary_mime_type = array("TEXT", "MULTIPART", "MESSAGE", "APPLICATION", "AUDIO", "IMAGE", "VIDEO", "OTHER");   
          
        if($structure->subtype && $structure->subtype!="PNG") {   
            return $primary_mime_type[(int)$structure->type].'/'.$structure->subtype;   
        }   
        return "TEXT/PLAIN";   
    }

    private function embed_images(&$body, &$path, $imageList){  
        // get all img tags  
        preg_match_all('/<img.*?>/', $body, $matches);  
        if(!isset($matches[0])) return;  
          
        foreach($matches[0] as $img){  
            // replace image web path with local path  
            preg_match('/src="(.*?)"/', $img, $m);  
            if(!isset($m[1])) continue; 

            $arr = parse_url($m[1]);  
            if(!isset($arr['scheme']) || !isset($arr['path'])) continue;  
              
			//if(!isset($arr['host']) || !isset($arr['path'])) continue;  
            if($arr['scheme']!="http"){  
                $filename=explode("@", $arr['path']);  
                $body = str_replace($img, '<img alt="" src="'.$path.$imageList[$filename[0]].'" style="border: none;" />', $body);  
            }  
        }  
        return $body;  
    } 

    //获取附件并存储到指定路径
    public function GetAttach($mid, $path){ // Get Atteced File from Mail  
        if(!$this->marubox) return false;  
  
        $struckture = imap_fetchstructure($this->marubox, $mid);  
          
        $files = array();  
        if($struckture->parts){  
            foreach($struckture->parts as $key => $value){  
                $enc=$struckture->parts[$key]->encoding;  
                  
                //取邮件附件  
                if($struckture->parts[$key]->ifdparameters){  
                    //命名附件,转码  
                    $name=$this->decode_mime($struckture->parts[$key]->dparameters[0]->value);                    
                    $extend =explode("." , $name);  
                    $file['extension'] = $extend[count($extend)-1];  
                    $file['pathname']  = $this->setPathName($key, $file['extension']);  
                    $file['title']     = !empty($name) ? htmlspecialchars($name) : str_replace('.'.$file['extension'], '', $name);  
                    $file['size']      = $struckture->parts[$key]->dparameters[1]->value;  
//                  $file['tmpname']   = $struckture->parts[$key]->dparameters[0]->value;  
                    if(@$struckture->parts[$key]->disposition=="ATTACHMENT"){  
                        $file['type'] = 1;       
                    }  
                    else{  
                        $file['type'] = 0;  
                    }             
                    $files[] = $file;                     
                      
                    $message = imap_fetchbody($this->marubox,$mid,$key+1);  
                    if($enc == 0) $message = imap_8bit($message);  
                    if($enc == 1) $message = imap_8bit($message);  
                    if($enc == 2) $message = imap_binary($message);  
                    if($enc == 3) $message = imap_base64($message);//图片
                    if($enc == 4) $message = quoted_printable_decode($message);  
                    if($enc == 5) $message = $message; 

                    $fp = fopen($path.$file['pathname'], "w");  
                    fwrite($fp,$message);  
                    fclose($fp);  
                }  
                // 处理内容中包含图片的部分  
                if($struckture->parts[$key]->parts){  
                    foreach($struckture->parts[$key]->parts as $keyb => $valueb){  
                        $enc = $struckture->parts[$key]->parts[$keyb]->encoding;  
                        if($struckture->parts[$key]->parts[$keyb]->ifdparameters){  
                            //命名图片  
                            $name=$this->decode_mime($struckture->parts[$key]->parts[$keyb]->dparameters[0]->value);  
                            $extend =explode("." , $name);  
                            $file['extension'] = $extend[count($extend)-1];  
                            $file['pathname']  = $this->setPathName($key, $file['extension']);  
                            $file['title']     = !empty($name) ? htmlspecialchars($name) : str_replace('.'.$file['extension'], '', $name);  
                            $file['size']      = $struckture->parts[$key]->parts[$keyb]->dparameters[1]->value;  
//                          $file['tmpname']   = $struckture->parts[$key]->dparameters[0]->value;  
                            $file['type']      = 0;  
                            $files[] = $file;  
                              
                            $partnro =($key+1).".".($keyb+1);  
                              
                            $message = imap_fetchbody($this->marubox,$mid,$partnro);  
                            if($enc == 0) $message = imap_8bit($message);  
                            if($enc == 1) $message = imap_8bit($message);  
                            if($enc == 2) $message = imap_binary($message);  
                            if($enc == 3) $message = imap_base64($message);  
                            if($enc == 4) $message = quoted_printable_decode($message);  
                            if($enc == 5) $message = $message;

                            $fp = fopen($path.$file['pathname'], "w");  
                            fwrite($fp,$message);  
                            fclose($fp);  
                        }  
                    }  
                }                 
            }  
        }  
        //move mail to taskMailBox  
        $this->move_mails($mid, $this->marubox);        
        return $files;  
    }  
      
    private function setPathName($fileID, $extension){//Set path name of the uploaded file to be saved.
        return date('Ym/dHis', time()) . $fileID . mt_rand(0, 10000) . '.' . $extension;  
    }  

    //移动邮件到指定分组  
    public function move_mails($msglist, $mailbox){  
        if(!$this->marubox) return false;  
      
        imap_mail_move($this->marubox, $msglist, $mailbox);  
    }    
      
    public function deleteMails($mid){ // Delete That Mail  
        if(!$this->marubox) return false;  
          
        imap_delete($this->marubox,$mid);  
    }

    public function close_mailbox(){ //Close Mail Box  
        if(!$this->marubox) return false;  
  
        imap_close($this->marubox,CL_EXPUNGE);  
    }  
      
    public function creat_mailbox($mailbox){
        if(!$this->marubox) return false;  
          
        imap_createmailbox($this->marubox, $mailbox);  
    }    
    
}

/* End of file Users.php */
/* Location: ./libraries/Users.php */
