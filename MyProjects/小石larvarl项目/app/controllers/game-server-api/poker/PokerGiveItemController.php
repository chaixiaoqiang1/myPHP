<?php

class PokerGiveItemController extends \BaseController {

	
	/*
		发放物品
	*/
	public function itemIndex()
	{
		$table = $this->initTable();
        $items = $table->getData();
		$data = array(
			'content' => View::make('serverapi.poker.item', array('items' => $items))
		);
		return View::make('main', $data);
	} 

	public function giveItem()
	{
		$msg = array(
			'code' => Config::get('errorcode.unknow'),
			'error' => ''
		);
		$rules = array(
			'item_id' => 'required',
		);
		$validator = Validator::make(Input::all(), $rules);
		if ($validator->fails()) {
			$msg['error'] = Lang::get('error.basic_intpu_error');
			return Response::json($msg, 403);
		}
		$player_name = Input::get('player_name');
		$player_id = Input::get('player_id');
		$item_id = Input::get('item_id');
		$num = Input::get('num');
		$platform_id  = Session::get('platform_id');
		$game_id = Session::get('game_id');
		$game = Game::find($game_id);
		$server = Server::find(13);
		if (!$server) {
			$msg['error'] = Lang::get('error.basic_not_found');
			return Response::json($msg, 403);
		}
		$server_internal_id = $server->server_internal_id;
		$slave_api = SlaveApi::connect($game->eb_api_url, $game->eb_api_key, $game->eb_api_secret_key);
		if (isset($player_name) && $player_id == '') {
			$player = $slave_api->getIdByName2($platform_id, $game_id, $server_internal_id,$player_name, '');
			if ($player->http_code == 200 && isset($player->body)) {
				$body = $player->body[0];
				$player_id = $body->player_id;
			}else{
				$msg['error'] = Lang::get('error.basic_not_found');
                return Response::json($msg, 403);
			}
		}
		if ($player_id) {
			$game_api = PokerGameServerApi::connect($server->api_server_ip, $server->api_server_port);
			$response = $game_api->giveItem($player_id, $item_id, $num);
			if (isset($response->result) && $response->result == 'OK') {
				$result = array(
					'status' => 'OK',
					'msg' => (isset($player_name) ? $player_name : '') . '--' . $player_id . '--OK--' . $response->result
				);
			}else{
				$result = array(
					'status' => 'error',
					'msg' =>  (isset($player_name) ? $player_name : '') . '--' . $player_id . '--FAIL'
				);
			}
			return Response::json($result);
		}else{
			$msg['error'] = Lang::get('error.basic_not_found');
            return Response::json($msg, 403);
		}
	}

	//发送自定义礼包
	public function sendLibaoIndex()
	{
		$table = $this->initTable();
        $items = $table->getData();

        $title = array('Congratulations','Gift','Reimbursement');
        $content = array('Thank you for participate in our Facebook event, here is your rewards:',
        				'Here is your rewards for joining our event. Thank you for your participation.',
        				'Thank you for your support, here is a little gift from the house.',
        				'Here is reimbursement for you. Thank you for playing.');
        $game = Game::find(Session::get('game_id'));
        $game_id = Session::get('game_id');
        $slave_api = SlaveApi::connect($game->eb_api_url, $game->eb_api_key, $game->eb_api_secret_key);
        $giftbags = $slave_api->getunsendgiftbags($game_id);
        if('200' != $giftbags->http_code){
        	$giftbags = array();
        }else{
        	$giftbags = $giftbags->body;
        }
        $str = array();
        foreach ($giftbags as $key => $value) {
        	$str[$value->id] = explode('+',$value->items_string);
        }
        $gold = array();
        $chips = array();
        $goods = array();
        $email1 = array();
        $email2 = array();
        $goods_str = array();
        $email1_str = array();
        $email2_str = array();
        foreach ($str as $key => $value) {
        	$email1[$key] = explode('|',$value[0]);
        	$gold[$key] = $value[1];
        	if($gold[$key] == ''){
        		$gold[$key] = '0';
        	}
        	$chips[$key] = $value[2];
        	if($chips[$key] == ''){
        		$chips[$key] = '0';
        	}
        	$email2[$key] = explode('|',$value[4]);
        	$goods[$key] = explode('|', $value[3]);

        }
        
        foreach ($email1 as $key => $value) {
        	$email1_str[$key] = $value[0];
        }
        $cargo = array();
        foreach ($goods as $key => $good) {
        	foreach ($good as $key1 => $value) {
        		$cargo[$key][] = explode('#', $value);
        	}
        }
        unset($goods);
        $goods = array();
        foreach ($cargo as $key1 => $value1) {
        	foreach ($value1 as $key2 => $value2) {
        		foreach ($value2 as $key3 => $value3) {
        			$goods[$key1][] = $value3;
        		}
        	}
        }
        foreach ($goods as $key => $value) {
        	$goods_str[$key] = implode(',', $value);
        	if($goods_str[$key] == '')
        		$goods_str[$key] = '0';
        }
        foreach ($email2 as $key => $value) {
        	$email2_str[$key] = trim($value[0]);
        }

		$data = array(
			'content' => View::make('serverapi.poker.sendlibao',array('items'=>$items,'title'=>$title,'content'=>$content,'giftbags'=>$giftbags,'gold'=>$gold,'chips'=>$chips,'goods_str'=>$goods_str,'email1_str'=>$email1_str,'email2_str'=>$email2_str)),
			);
		return View::make('main',$data);
	}
	public function sendLibao()
	{
		$msg = array(
			'code'  => Config::get('error_code.unknow'),
			'error' =>Lang::get('error.basic_input_error'), 
		);
		$giftbag_pass = (int)Input::get('giftbag_pass');
		$check_record = Input::get('check_record');
		if($check_record){
			return $this->check_sendLibao_record();
		}
		if($giftbag_pass){
			$giftbag_id = Input::get('giftbag_id');
			$statu = Input::get('statu');
			return $this->giftbag_pass($giftbag_id, $statu);
		}
		$sendtotype = Input::get('sendtotype');
		$players = Input::get('players');
		$player_array = explode("\n", $players);
		foreach ($player_array as $key => $value) {
			if(!$value){
				unset($player_array[$key]);
			}
		}
		$chips = Input::get('chips');
		$gold = Input::get('gold');
		$item_id = array(Input::get('item_id0'),Input::get('item_id1'),Input::get('item_id2'),Input::get('item_id3'),Input::get('item_id4'));
		$item_num = array(Input::get('item_num0'),Input::get('item_num1'),Input::get('item_num2'),Input::get('item_num3'),Input::get('item_num4'));
		$title = Input::get('title');
		$content = Input::get('content');
		$diytitle = Input::get('diytitle');
		$diycontent = Input::get('diycontent');
		$game_id = Session::get('game_id'); 
		$game = Game::find($game_id);
		$giftbag_id = Input::get('giftbag_id');

		$items = '';
		$item_id_num = '';
		$num=0;
		for($i = 0;$i <= 4;$i ++){
			if($item_id[$i] == 0){
				continue;
			}
			if($item_num[$i] == 0){
				return Response::json($msg,403);
			}
			$item_id_num = $item_id[$i].'#'.$item_num[$i];	
			if($num != 0){
				$item_id_num = '|'.$item_id_num;
			}
			$num++;
			$items = $items.$item_id_num;
		}
		if(($title == 0 && $diytitle == '') or ($content == 0 && $diycontent == '')){
			return Response::json($msg,403);
		}
        $title_list = array('','Congratulations|Selamat','Gift|Hadiah','Reimbursement|Kompensasi');
        $content_list = array('','Thank you for participate in our Facebook event, here is your rewards:
        						|Terima kasih telah berpartisipasi dalam Even Facebook Joys. Berikut adalah hadiah buat Anda :',
        						'Here is your rewards for joining our event. Thank you for your participation.
        						|Selamat, Anda telah mendapatkan hadiah Karena telah berparitisipasi dalam Event kami. Terima Kasih&Selamat Bermain!',
        						'Thank you for your support, here is a little gift from the house.
        						|Terima kasih atas dukungan Anda, segera ambil hadiah Anda sekarang juga.',
        						'Here is reimbursement for you. Thank you for playing.
        						|Berikut adalah kompensasi buat Anda. Terima Kasih & Selamat Bermain.');
        $title = $diytitle != ''? $diytitle :$title_list[$title];
		$content = $diycontent != ''? $diycontent : $content_list[$content];
		$items_string = $title.'+'.$gold.'+'.$chips.'+'.$items.'+'.$content;
		// var_dump($items_string);
		$server = Server::find(13);
 	  	if (!$server) {
			return Response::json($msg, 403);
		}
		$api = PokerGameServerApi::connect($server->api_server_ip,$server->api_server_port);
		if('0' == $sendtotype){	//给部分玩家发送礼包
			$response = $api->sendLibao($player_array,$items_string);
			$response->player = $player_array;
			if($giftbag_id != 0){
				if(isset($response->is_ok) && $response->is_ok == true){
					$slave_api = SlaveApi::connect($game->eb_api_url, $game->eb_api_key, $game->eb_api_secret_key);
					$result = $slave_api->changePokerGiftbagStatu($game_id, $giftbag_id, 1);
					if('200' != $result->http_code){
						return Response::json(array('error'=>'发送成功但没有成功修改礼包状态，礼包ID为 '.$giftbag_id.'，请联系技术修改'), 403);
					}
				}

			}
			if(isset($response->is_ok) && $response->is_ok == true){
				$itemstr = 'Title-'.$title.', send '.$chips.'chips,'.$gold.'gold ';
				$table = $this->initTable();
        		$table = $table->getData();
        		$itemnames = array();
        		foreach ($table as $value) {
        			$itemnames[$value->Id] = $value->Name;
        			unset($value);
        		}
        		unset($table);
        		$i = 0;
        		foreach ($item_id as $single_item_id) {
        			if($single_item_id){
        				$itemstr .= ', '.$item_num[$i].'-'.$itemnames[$single_item_id];
        			}
        			$i++;
        		}
        		unset($itemnames);
				foreach ($player_array as $single_player_id) {
					$datatostore = array(   //将操作插入数据库中
	                    'operate_time' => time(),
	                    'game_id' => $game_id,
	                    'player_name' => '',
	                    'player_id' => $single_player_id ? $single_player_id : '',
	                    'operator' => Auth::user()->user_id,
	                    'server_name' => $server->server_name,
	                    'operation_type' => 'poker-giftbag',
	                    'extra_msg' => $itemstr,
                    );
	                Operation::insert($datatostore);
	                unset($single_player_id);
	                unset($datatostore);
				}
			}
		}else{	//全服礼包
			$start_time = (int)strtotime(Input::get('start_time'));
        	$end_time = (int)strtotime(Input::get('end_time'));
        	if($start_time >= $end_time){
        		return Response::json(array('error'=>'时间设置错误'), 403);
        	}
        	$response = $api->sendallservergiftbag($start_time, $end_time, $items_string);
        	Log::info('PokerGiveItemController----'.var_export($response, true));
        	$response->player = array('All players send success!');
		}
		return Response::json($response);
	}

	private function check_sendLibao_record(){
		$check_start_time = strtotime((trim(Input::get('check_start_time'))));
		$check_end_time = strtotime((trim(Input::get('check_end_time'))));
		$creater = Input::get('creater');

        $game_id = Session::get('game_id');
		$game = Game::find($game_id);
        $slave_api = SlaveApi::connect($game->eb_api_url, $game->eb_api_key, $game->eb_api_secret_key);
        $giftbags = $slave_api->getunsendgiftbags($game_id, $giftbag_id=0, $check_start_time, $check_end_time, $creater);

        if(200 == $giftbags->http_code){
        	return Response::json(array('records' => $giftbags->body));
        }else{
        	return Response::json(array('error' => 'No records'), 401);
        }
	}

	public function giftbag_pass($giftbag_id, $statu){
		$game_id = Session::get('game_id');
		$game = Game::find($game_id);

		$slave_api = SlaveApi::connect($game->eb_api_url, $game->eb_api_key, $game->eb_api_secret_key);

		if('9' == $statu){	//9代表不通过某个礼包
			$result = $slave_api->changePokerGiftbagStatu($game_id, $giftbag_id, $statu);
			if('200' != $result->http_code){
				return Response::json(array('error'=>'不通过礼包失败，礼包ID为 '.$giftbag_id.'，请联系技术修改'), 403);
			}else{
				return Response::json(array('msg'=>'不通过成功'), 200);
			}
		}elseif('1' == $statu){
			$result = $slave_api->getunsendgiftbags($game_id, $giftbag_id);
			if('200' != $result->http_code){
				return Response::json(array('error'=>'没有此礼包'), 403);
			}
			$result = $result->body[0];
			$server = Server::find(13);
	 	  	if (!$server) {
					return Response::json($msg, 403);
			}
			unset($slave_api);
			$slave_api = SlaveApi::connect($game->eb_api_url, $game->eb_api_key, $game->eb_api_secret_key);
			$player_ids = explode(',', $result->player_ids);
			$items_string = $result->items_string;
			unset($result);
			$api = PokerGameServerApi::connect($server->api_server_ip,$server->api_server_port);
			$response = $api->sendLibao($player_ids, $items_string);
			if(isset($response->is_ok) && $response->is_ok == true){
				$result = $slave_api->changePokerGiftbagStatu($game_id, $giftbag_id, $statu);
				if('200' != $result->http_code){
					return Response::json(array('error'=>'发送成功但没有成功修改礼包状态，礼包ID为 '.$giftbag_id.'，请联系技术修改'), 403);
				}
			}
			$response->player = $player_ids;
			return Response::json($response);
		}else{
			return Response::json(array('error'=>'无法识别的状态'), 403);
		}
	}

	/*
	群发物品
	*/
	public function itemGroupIndex()
	{
		$table = $this->initTable();
        $items = $table->getData();
        $data = array(
        	'content' => View::make('serverapi.poker.users.item-group', array('items' => $items))
        );
        return View::make('main', $data);
	}

	public function itemGroupSend()
	{
		$msg = array(
			'code' => Config::get('errorcode.unknow'),
			'error' => ''
		);
		$rules = array(
			'player' => 'required',
			'item_id' => 'required',
			'num' => 'required'
		);
		$validator = Validator::make(Input::all(), $rules);
		if ($validator->fails()) {
			$msg['error'] = Lang::get('error.basic_intpu_error');
			return Response::json($msg, 403); 
		}
		$server = Server::find(13);
		if (!$server) {
			$msg['error'] = Lang::get('error.basic_not_found');
			return Response::json($msg, 403);
		}
		$server_internal_id = $server->server_internal_id;
		$platform_id = Session::get('platform_id');
		$game_id = Session::get('game_id');
		$game = Game::find($game_id);
		$type = Input::get('type');
		$player = Input::get('player');
		$item_id = Input::get('item_id');
		$num = Input::get('num');
		$arr = explode("\n", $player);
		$success = $fail = '';
		$slave_api = SlaveApi::connect($game->eb_api_url, $game->eb_api_key, $game->eb_api_secret_key);		
		$api = PokerGameServerApi::connect($server->api_server_ip, $server->api_server_port);
		/*foreach ($arr as $key => $value) {
			$response = $api->giveItem($value, $item_id, $num);
			if ($response->result == 'OK') {
				$success .= ''
			}
		}*/
		if ($type == 2) { //name
			foreach ($arr as $key => $value) {
				$response = $slave_api->getIdByName2($platform_id, $game_id, $server_internal_id,$value, '');
	    		if ($response->http_code == 200) {
	    			if (isset($response->body)) {
	    				$body = $response->body;
	    				if (isset($body[0]->player_id)) {
	    					$player_id = $body[0]->player_id;
	    				}else{
	    					$msg['error'] = Lang::get('serverapi.player_id_not_found');
	    					return Response::json($msg, 403);
	    				}
	    			}
	    		}
	    		if ($player_id) {
	    			$res = $api->giveItem($player_id, $item_id, $num);

					if ($res->result == 'OK') {
						$success .= '==='.(isset($value) ? $value : '') . '--' .$player_id . '-- OK--' . $res->result;
					}else{
						$fail .= '==='.(isset($value) ? $value : '') . '--' .$player_id . '-- Fail--' . $res->error_code;
					}
	    		}
	    		unset($response);
	    		unset($res);
	    		unset($player_id);
			}
			$result1 = array(
				'status' => 'OK',
				'msg' => $success
			);
			$result2 = array(
				'status'=> 'error',
				'msg' => $fail
			);
		}
		if ($type == 1) {
			foreach ($arr as $key => $value) {
				$res = $api->giveItem($value, $item_id, $num);
				if ($res->result == 'OK') {
					$success .= "==(" .$value . ')-' . $res->result ;
				}else{
					$fail .= "==(" . $value . ')-' .$res->error_code;
				}
			}
			$result1 = array(
				'status' => 'OK',
				'msg' => $success
			);
			$result2 = array(
				'status' => 'error',
				'msg' => $fail
			);
		}
		$data = array(
			'result1' =>$result1,
			'result2' => $result2
		);
		if (isset($data)) {
			return Response::json($data);
		}else{
			$msg['error'] = '操作失败';
			return Response::json($msg, 403);
		}
	}


	public function initTable()
	{
		 $table = Table::init(
                public_path() . '/table/poker/item.txt');
        return $table;
	}

	public function createLibaoIndex(){	//创建礼包以便用来审核
		$table = $this->initTable();
        $items = $table->getData();

        $title = array('Congratulations','Gift','Reimbursement');
        $content = array('Thank you for participate in our Facebook event, here is your rewards:',
        				'Here is your rewards for joining our event. Thank you for your participation.',
        				'Thank you for your support, here is a little gift from the house.',
        				'Here is reimbursement for you. Thank you for playing.');
		$data = array(
			'content' => View::make('serverapi.poker.createlibao',array('items'=>$items,'title'=>$title,'content'=>$content)),
			);
		return View::make('main',$data);
	}

	public function createLibao(){
		$msg = array(
			'code'  => Config::get('error_code.unknow'),
			'error' =>Lang::get('error.basic_input_error'), 
		);
		$sendtotype = Input::get('sendtotype');
		$players = Input::get('players');
		$player_array = explode("\n", $players);
		foreach ($player_array as $key => $value) {
			if(!$value){
				unset($player_array[$key]);
			}
		}
		$chips = Input::get('chips');
		$gold = Input::get('gold');
		$item_id = array(Input::get('item_id0'),Input::get('item_id1'),Input::get('item_id2'),Input::get('item_id3'),Input::get('item_id4'));
		$item_num = array(Input::get('item_num0'),Input::get('item_num1'),Input::get('item_num2'),Input::get('item_num3'),Input::get('item_num4'));
		$title = Input::get('title');
		$content = Input::get('content');
		$diytitle = Input::get('diytitle');
		$diycontent = Input::get('diycontent');

		$items = '';
		$item_id_num = '';
		$num = 0;
		for($i = 0;$i <= 4;$i ++){
			
			if($item_id[$i] == 0){
				continue;
			}
			if($item_num[$i] == 0){
				return Response::json($msg,403);
			}
			$item_id_num = $item_id[$i].'#'.$item_num[$i];	
			if($num != 0){
				$item_id_num = '|'.$item_id_num;
			}
			$num++;
			$items = $items.$item_id_num;
		}
		if(($title == 0 && $diytitle == '') or ($content == 0 && $diycontent == '')){
			return Response::json($msg,403);
		}
        $title_list = array('','Congratulations|Selamat','Gift|Hadiah','Reimbursement|Kompensasi');
        $content_list = array('','Thank you for participate in our Facebook event, here is your rewards:
        						|Terima kasih telah berpartisipasi dalam Even Facebook Joys. Berikut adalah hadiah buat Anda :',
        						'Here is your rewards for joining our event. Thank you for your participation.
        						|Selamat, Anda telah mendapatkan hadiah Karena telah berparitisipasi dalam Event kami. Terima Kasih&Selamat Bermain!',
        						'Thank you for your support, here is a little gift from the house.
        						|Terima kasih atas dukungan Anda, segera ambil hadiah Anda sekarang juga.',
        						'Here is reimbursement for you. Thank you for playing.
        						|Berikut adalah kompensasi buat Anda. Terima Kasih & Selamat Bermain.');
        $title = $diytitle != ''? $diytitle :$title_list[$title];
		$content = $diycontent != ''? $diycontent : $content_list[$content];
		$items_string = $title.'+'.$gold.'+'.$chips.'+'.$items.'+'.$content;

 	  	$game_id = Session::get('game_id');
 	  	$game = Game::find($game_id);
 	  	$operator = Auth::user()->username;

 	  	$slave_api = SlaveApi::connect($game->eb_api_url, $game->eb_api_key, $game->eb_api_secret_key);

 	  	$result = $slave_api->pokercreatelibao($game_id, $operator, time(), $player_array, $items_string);

 	  	if('200' == $result->http_code){
 	  		return Response::json(array('msg' => '创建成功'), 200); 
 	  	}else{
 	  		return Response::json(array('error' => '创建异常'), 401); 
 	  	}
	}
}