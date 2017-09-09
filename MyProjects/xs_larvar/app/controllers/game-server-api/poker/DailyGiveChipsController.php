<?php
/*
每日发放筹码
*/
class DailyGiveChipsController extends \BaseController
{
	public function chipIndex()
	{
		$data = array(
			'content' => View::make('serverapi.poker.dailyGiveChips'),
		);
		return View::make('main', $data);
	}
	public function dailyGiveChip()
	{
		$msg = array(
			'error' => 'error code'
		);
		$start_time = Input::get('start_time');
		$end_time = Input::get('end_time');
		$start_time_arr = $this->findNum($start_time);//拆成年、月、日三个元素的数组。（）start_time_arr:array (0 => '2015',1 => '03',2 => '10',)
		$end_time_arr = $this->findNum($end_time);
		$game_id = Session::get('game_id');

        $server = Server::getServerByGameId($game_id)->take(1)->get();
		foreach ($server as $key => $v) {
			$api_server_ip = $v->api_server_ip;
			$api_server_port = $v->api_server_port;
			break;
		}
		$api = PokerGameServerApi::connect($api_server_ip, $api_server_port);
		$result = array();
		while($start_time_arr<$end_time_arr)
		{
			if($start_time_arr[0]<=$end_time_arr[0]){
				if($start_time_arr[1]<=12){
					if($start_time_arr[2]<=31)
					{
						$response = $api->dailyChips($this->arrTTString($start_time_arr));//arrTTstring后:string：2015-03-10
						$body = $response->system_send;
						// var_dump($body);var_dump('hello');die();
						$total = $body->signin + $body->login + $body->sday
						 	+$body->smallgame + $body->turntable + $body->bankrupity
						 	+$body->timebox + $body->dailytask + $body->robotlose;
						// var_dump($total);die();
						$result[] = array(
							'date' => $response->date,
							'total' => $total,
							'signin' => $body->signin,
							'login' => $body->login,
							'sday' => $body->sday,
							'smallgame'=>$body->smallgame,
							'turntable'=>$body->turntable,
							'bankrupity'=>$body->bankrupity,
							'timebox'=>$body->timebox,
							'dailytask'=>$body->dailytask,
							'robotlose' => $body->robotlose
						);
						
						if(!isset($response->error_code)){
							//array_push($returnArr,$result);
							//return $api->sendResponse();
						}else{
							return Response::json($msg, 403);
						}
						$start_time_arr[2]++;
					}else {
						$start_time_arr[2]=1;
						$start_time_arr[1]++;
					}
				}else {
					$start_time_arr[1]==1;
					$start_time_arr[0]++;
				}
			}else{
				return Response::json($msg,403);
			}	
		}
		//最后一天
		$response = $api->dailyChips($end_time);
		// var_dump($response);die();
		$body = $response->system_send;
		// var_dump($body);
		$total = $body->signin + $body->login + $body->sday
		 		+$body->smallgame + $body->turntable + $body->bankrupity
		 		+$body->timebox + $body->dailytask + $body->robotlose;

		$result[] = array(
							'date' => $response->date,
							'total' => $total,
							'signin' => $body->signin,
							'login' => $body->login,
							'sday' => $body->sday,
							'smallgame'=>$body->smallgame,
							'turntable'=>$body->turntable,
							'bankrupity'=>$body->bankrupity,
							'timebox'=>$body->timebox,
							'dailytask'=>$body->dailytask,
							'robotlose' =>$body->robotlose
						);
		if(!isset($response->error_code)){
			//array_push($returnArr,$result);
		}else{
			return Response::json($msg, 403);
		}
		//var_dump($result);die();
		return Response::json($result);
	}

	function tfRecoverIndex()
	{
		$data = array(
			'content' => View::make('serverapi.poker.dailyTfRecover'),
		);
		return View::make('main', $data);
	}
	function dailyTfRecover()
	{
		$msg = array(
			'code'  => Config::get('error_code.unknow'),
			'error' =>Lang::get('error.basic_input_error'), 
		);
		$start_time = Input::get('start_time');
		$end_time = Input::get('end_time');
		$start_time_arr = $this->findNum($start_time);
		$end_time_arr = $this->findNum($end_time);
		
		$server = Server::find(13);
		//var_dump($server->server_name);die();
		if(!$server){
			return Response::json($msg,403);
		}
		$api = PokerGameServerApi::connect($server->api_server_ip, $server->api_server_port);
		$result = array();
		while($start_time_arr!=$end_time_arr)
		{
			if($start_time_arr[0]<=$end_time_arr[0]){
				if($start_time_arr[1]<12){
					if($start_time_arr[2]<31)
					{
						$response = $api->dailyTfRecover($this->arrTTString($start_time_arr));
						//var_dump($response);die();
						$body = $response->table_fee;
						$blind10     =isset($body->blind10    )?$body->blind10    :'';
						$blind20     =isset($body->blind20    )?$body->blind20    :'';
						$blind50     =isset($body->blind50    )?$body->blind50    :'';
						$blind100    =isset($body->blind100   )?$body->blind100   :'';
						$blind200    =isset($body->blind200   )?$body->blind200   :'';
						$blind500    =isset($body->blind500   )?$body->blind500   :'';
						$blind1000   =isset($body->blind1000  )?$body->blind1000  :'';
						$blind2000   =isset($body->blind2000  )?$body->blind2000  :'';
						$blind2500   =isset($body->blind2500  )?$body->blind2500  :'';
						$blind5000   =isset($body->blind5000  )?$body->blind5000  :'';
						$blind10000  =isset($body->blind10000 )?$body->blind10000 :'';
						$blind20000  =isset($body->blind20000 )?$body->blind20000 :'';
						$blind25000  =isset($body->blind25000 )?$body->blind25000 :'';
						$blind50000  =isset($body->blind50000 )?$body->blind50000 :'';
						$blind100000 =isset($body->blind100000)?$body->blind100000:'';
						$blind200000 =isset($body->blind200000)?$body->blind200000:'';
						$blind500000 =isset($body->blind500000)?$body->blind500000:'';
						$total       =$blind10+$blind20+$blind50+$blind100+$blind200+$blind500+$blind1000+$blind2000+$blind2500+$blind5000+$blind10000+$blind20000+$blind25000+$blind50000+$blind100000+$blind200000+$blind500000;
						if($total!=0){
							$result[] = array(
								'date' => $response->date,
								'total'=> $total,
								'blind10'    =>$blind10    ,
								'blind20'    =>$blind20    ,
	          					'blind50'    =>$blind50    ,
						        'blind100'   =>$blind100   ,
						        'blind200'   =>$blind200   ,
						        'blind500'   =>$blind500   ,
						        'blind1000'  =>$blind1000  ,
						        'blind2000'  =>$blind2000  ,
						        'blind2500'  =>$blind2500  ,
								'blind5000'  =>$blind5000  ,
								'blind10000' =>$blind10000 ,
								'blind20000' =>$blind20000 ,
								'blind25000' =>$blind25000 ,
								'blind50000' =>$blind50000 ,
								'blind100000'=>$blind100000,
								'blind200000'=>$blind200000,
								'blind500000'=>$blind500000,
							);
						}
						
						if(!isset($response->error_code)){
							//array_push($returnArr,$result);
							//return $api->sendResponse();
						}else{
							return Response::json($msg, 403);
						}
						$start_time_arr[2]++;
					}elseif ($start_time_arr[2]==31) {
						$response = $api->dailyTfRecover($this->arrTTString($start_time_arr));
						$body = $response->table_fee;
						$blind10     =isset($body->blind10    )?$body->blind10    :'';
						$blind20     =isset($body->blind20    )?$body->blind20    :'';
						$blind50     =isset($body->blind50    )?$body->blind50    :'';
						$blind100    =isset($body->blind100   )?$body->blind100   :'';
						$blind200    =isset($body->blind200   )?$body->blind200   :'';
						$blind500    =isset($body->blind500   )?$body->blind500   :'';
						$blind1000   =isset($body->blind1000  )?$body->blind1000  :'';
						$blind2000   =isset($body->blind2000  )?$body->blind2000  :'';
						$blind2500   =isset($body->blind2500  )?$body->blind2500  :'';
						$blind5000   =isset($body->blind5000  )?$body->blind5000  :'';
						$blind10000  =isset($body->blind10000 )?$body->blind10000 :'';
						$blind20000  =isset($body->blind20000 )?$body->blind20000 :'';
						$blind25000  =isset($body->blind25000 )?$body->blind25000 :'';
						$blind50000  =isset($body->blind50000 )?$body->blind50000 :'';
						$blind100000 =isset($body->blind100000)?$body->blind100000:'';
						$blind200000 =isset($body->blind200000)?$body->blind200000:'';
						$blind500000 =isset($body->blind500000)?$body->blind500000:'';
						$total       =$blind10+$blind20+$blind50+$blind100+$blind200+$blind500+$blind1000+$blind2000+$blind2500+$blind5000+$blind10000+$blind20000+$blind25000+$blind50000+$blind100000+$blind200000+$blind500000;
						if($total!=0){
							$result[] = array(
								'date' => $response->date,
								'total'=> $total,
								'blind10'    =>$blind10    ,
								'blind20'    =>$blind20    ,
	          					'blind50'    =>$blind50    ,
						        'blind100'   =>$blind100   ,
						        'blind200'   =>$blind200   ,
						        'blind500'   =>$blind500   ,
						        'blind1000'  =>$blind1000  ,
						        'blind2000'  =>$blind2000  ,
						        'blind2500'  =>$blind2500  ,
								'blind5000'  =>$blind5000  ,
								'blind10000' =>$blind10000 ,
								'blind20000' =>$blind20000 ,
								'blind25000' =>$blind25000 ,
								'blind50000' =>$blind50000 ,
								'blind200000'=>$blind100000,
								'blind200000'=>$blind200000,
								'blind500000'=>$blind500000,
							);
						}
						if(!isset($response->error_code)){
							//array_push($returnArr,$result);
						}else{
							return Response::json($msg, 403);
						}
						$start_time_arr[2]=1;
						$start_time_arr[1]++;
					}
				}elseif ($start_time_arr[1]==12) {
					$start_time_arr[1]==1;
					$start_time_arr[0]++;
				}
			}else{
				return Response::json($msg,403);
			}	
		}
		//最后一天
		$response = $api->dailyTfRecover($end_time);
		$body = $response->table_fee;
		$blind10     =isset($body->blind10    )?$body->blind10    :'';
		$blind20     =isset($body->blind20    )?$body->blind20    :'';
		$blind50     =isset($body->blind50    )?$body->blind50    :'';
		$blind100    =isset($body->blind100   )?$body->blind100   :'';
		$blind200    =isset($body->blind200   )?$body->blind200   :'';
		$blind500    =isset($body->blind500   )?$body->blind500   :'';
		$blind1000   =isset($body->blind1000  )?$body->blind1000  :'';
		$blind2000   =isset($body->blind2000  )?$body->blind2000  :'';
		$blind2500   =isset($body->blind2500  )?$body->blind2500  :'';
		$blind5000   =isset($body->blind5000  )?$body->blind5000  :'';
		$blind10000  =isset($body->blind10000 )?$body->blind10000 :'';
		$blind20000  =isset($body->blind20000 )?$body->blind20000 :'';
		$blind25000  =isset($body->blind25000 )?$body->blind25000 :'';
		$blind50000  =isset($body->blind50000 )?$body->blind50000 :'';
		$blind100000 =isset($body->blind100000)?$body->blind100000:'';
		$blind200000 =isset($body->blind200000)?$body->blind200000:'';
		$blind500000 =isset($body->blind500000)?$body->blind500000:'';
		$total       =$blind10+$blind20+$blind50+$blind100+$blind200+$blind500+$blind1000+$blind2000+$blind2500+$blind5000+$blind10000+$blind20000+$blind25000+$blind50000+$blind100000+$blind200000+$blind500000;
		if($total!=0){
			$result[] = array(
				'date' => $response->date,
				'total'=> $total,
				'blind10'    =>$blind10    ,
				'blind20'    =>$blind20    ,
	         	'blind50'    =>$blind50    ,
			    'blind100'   =>$blind100   ,
			    'blind200'   =>$blind200   ,
			    'blind500'   =>$blind500   ,
			    'blind1000'  =>$blind1000  ,
			    'blind2000'  =>$blind2000  ,
			    'blind2500'  =>$blind2500  ,
				'blind5000'  =>$blind5000  ,
				'blind10000' =>$blind10000 ,
				'blind20000' =>$blind20000 ,
				'blind25000' =>$blind25000 ,
				'blind50000' =>$blind50000 ,
				'blind100000'=>$blind100000,
				'blind200000'=>$blind200000,
				'blind500000'=>$blind500000,
			);
		}
		if(!isset($response->error_code)){
			//array_push($returnArr,$result);
		}else{
			return Response::json($msg, 403);
		}
		//var_dump($result);die();
		return Response::json($result);
	}

	function findNum($str='')
	{
	    $str=trim($str);
	    if(empty($str)){
	    	return NUll;
	    }
	    $result=array('','','');
		for($i=0,$j=0;$i<strlen($str);$i++)
		{
		    if(is_numeric($str[$i])){
		        $result[$j].=$str[$i];
		    }
		    else{
		    	$j++;
		    }	        
		}
		return $result;
	}
	function arrTTString($arrTime=array())
	{
		$arrT = $arrTime;
		$arrT[1]=sprintf("%02d", $arrT[1]);
		$arrT[2]=sprintf("%02d", $arrT[2]);
		$stringTime = $arrT[0].'-'.$arrT[1].'-'.$arrT[2];
		return $stringTime;
	}
}
?>