<?php

class OfficeMailsController extends \BaseController {

	public function index(){
		if($filename = Input::get('filename')){
            $file = storage_path() . "/cache/" . $filename . ".csv";
            $data = array(
                    'content' => View::make('download', 
                            array(
                                    'file' => $file
                            ))
            );
            return View::make('main', $data);
        }
        $data = array(
                'content' => View::make('officemails', 
                        array(
                        ))
        );
        return View::make('main', $data);
	}

	public function maildeal(){
		$type = Input::get('type');
        $game_id = Session::get('game_id');
        if('search' == $type){	//查询邮件
            $page = Input::get('page');
            $page = $page > 0 ? $page : 1;
            $mail_type = Input::get('mail_type');
            $operator = Input::get('operator');
            $sender = Input::get('sender');
            $start_time = strtotime(Input::get('start_time'));
            $end_time = strtotime(Input::get('end_time'));

            $sql = DB::table('Office_Mails')->whereBetween('mail_time', array($start_time, $end_time))->orderby('mail_time', 'desc');
            if($operator){
            	$sql->where('operator', $operator);
            }
            if($sender){
            	$sql->where('sender', $sender);
            }
            if($mail_type){
            	$sql->where('mail_type', $mail_type);
            }

            $count = $sql->count();
            $result = $sql->forpage($page, 50)->get();
            $mailtype2name = array(
            	1 => '请假',
            	2 => '加班',
            	3 => '调班',
            	);

            if($count){	//查询有结果
            	foreach ($result as &$value) {
            		if(isset($value->mail_time)){
            			$value->mail_time = date("Y-m-d H:i:s", $value->mail_time);
            		}
            		if(isset($value->mail_type)){
            			$value->mail_type = isset($mailtype2name[$value->mail_type]) ? $mailtype2name[$value->mail_type] : $value->mail_type;
            		}
            	}              
            	$response = array(
                    'mails' => $result,
                    'current_page' => $page,
                    'count' => $count,
                    );
            	unset($result);
                return Response::json($response);
            }else{
                return Response::json(array('error' => "No data"), 404);
            }
        }

        if('update' == $type){	//编辑数据
        	$id = (int)Input::get('id');
        	if($id){
        		$data2update = array(
        			'sender' => Input::get('sender'),
        			'operator' => Input::get('operator'),
        			'l_type' => Input::get('l_type'),
        			'l_time' => Input::get('l_time'),
        			'l_days' => Input::get('l_days'),
        			'l_reason' => Input::get('l_reason'),
        			'l_result' => Input::get('l_result'),
        			);

        		DB::table('Office_Mails')->where('id', $id)->update($data2update);
        		return Response::json(array('msg' => '更新成功'));
        	}else{
        		return Response::json(array('error' => 'No such Mail'), 404);
        	}
        }

        if('download' == $type){	//下载数据
            $mail_type = Input::get('mail_type');
            $operator = Input::get('operator');
            $sender = Input::get('sender');
            $start_time = strtotime(Input::get('start_time'));
            $end_time = strtotime(Input::get('end_time'));

            $sql = DB::table('Office_Mails')->whereBetween('mail_time', array($start_time, $end_time))->orderby('mail_time', 'desc');
            if($operator){
            	$sql->where('operator', $operator);
            }
            if($sender){
            	$sql->where('sender', $sender);
            }
            if($mail_type){
            	$sql->where('mail_type', $mail_type);
            }

            $result = $sql->get();
            $mailtype2name = array(
            	1 => '请假',
            	2 => '加班',
            	3 => '调班',
            	);

            if(count($result)){	//查询有结果
            	foreach ($result as &$value) {
            		if(isset($value->mail_time)){
            			$value->mail_time = date("Y-m-d H:i:s", $value->mail_time);
            		}
            		if(isset($value->mail_type)){
            			$value->mail_type = isset($mailtype2name[$value->mail_type]) ? $mailtype2name[$value->mail_type] : $value->mail_type;
            		}
            	}
            	$values = array();
            	foreach ($result as $key => $value) {
            		$values[] = array(
	            		'mail_type' => isset($value->mail_type) ? $value->mail_type : '',	
						'mail_time' => isset($value->mail_time) ? $value->mail_time : '',	
						'l_type' => isset($value->l_type) ? $value->l_type : '',	
						'sender' => isset($value->sender) ? $value->sender : '',	
						'operator' => isset($value->operator) ? $value->operator : '',	
						'l_time' => isset($value->l_time) ? $value->l_time : '',	
						'l_days' => isset($value->l_days) ? $value->l_days : '',	
						'l_reason' => isset($value->l_reason) ? $value->l_reason : '',	
						'l_result' => isset($value->l_result) ? $value->l_result : '',	
            			);
            	}
            	$keys = array();
            	if(count($values)){
            		foreach ($values[0] as $key => $value) {
            			$keys[] = Lang::get('office.'.$key);
            		}
            	}
            	unset($result);
                return $this->downloadMails(array(
                	'keys' => $keys,
                	'values' => $values,
                	));
            }else{
                return Response::json(array('error' => "No data"), 404);
            }
        }
	}

	private function downloadMails($result){
        $filename = time();
        $file = storage_path() . "/cache/" . $filename . ".csv";
        $csv = CSV::init($file, $result['keys']);
        $res = $csv->writeData(array());    //编码无法正常显示，加一个空行防止数据和列名出现混乱

        foreach ($result['values'] as $key => $value) {
            $res = $csv->writeData($value);
            unset($value);
        }
        $res = $csv->closeFile();
        $result = array(
            'filename' => $filename,
            );
        return Response::json($result);
    }
}