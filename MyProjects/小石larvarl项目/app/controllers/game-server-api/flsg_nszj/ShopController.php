<?php 

class ShopController extends \BaseController 
{
	//商城添加商品只需要在该数组中添加
	private $shopInfo = array(
							'price_0'		=>  'God',
							'price_8' 		=>	'5萬銀幣',
							'price_16' 		=>	'10萬銀幣',
							'price_148' 	=>	'100萬銀幣',
							'price_388' 	=>	'500萬銀幣',
							'price_688' 	=>	'1000萬銀幣',
							'price_298' 	=>	'完美寶石碎片*1禮包',
							'price_50' 		=>	'星座碎片*1',
							'price_48' 		=>	'500聲望',
							'price_88' 		=>	'100寶石碎片',
							'price_2888'	=>	'天神之翼禮包',
							'price_666' 	=>	'精灵之翼禮包',
							'price_99' 		=>	'隨機變身卡禮包',
							'price_2000'	=>	'20級星座寶盒',
							'price_2000'	=>	'60級星座寶盒',
							'price_2' 		=>	'初級戰鬥加速藥劑',
							'price_3' 		=>	'中級戰鬥加速藥劑',
							'price_5' 		=>	'高級戰鬥加速藥劑',
							'price_200' 	=>	'女神卡',
							'price_888' 	=>	'炫彩之翼禮包',
							'price_8888'	=>	'墮天使之翼禮包',
							'price_166'		=>  '限時墮天使之翼禮包',
						);

	public function index()
	{
		//$servers = Server::currentGameServers()->get();
		$servers = $this->getUnionServers();
		$table = $this->initTableShop();
		$items = $table->getData();
		$data = array (
			'content' => View::make('serverapi.flsg_nszj.shop.index', array (
				'servers' => $servers,
				'items' => $items,
			)), 
		);
		return View::make('main', $data);
	}

	public function shopAction()
	{
		$msg = array(
			'error' => Lang::get('error.basic_input_error'),
		);
		$type = Input::get('type');
		$server_ids = Input::get('server_id');
		if('open_limit' == $type){
			$open_time_from = strtotime(Input::get('open_time_from'));
			$open_time_to = strtotime(Input::get('open_time_to'));
			$duration = (int)Input::get('duration');
			if (!$open_time_from || !$open_time_to || $open_time_from < time() || $open_time_from >= $open_time_to) {
				return Response::json(array('error'=>'时间输入有误!'), 403);
			}
		}
		
		if(!$server_ids){
			return;
		}
		foreach($server_ids as $server_id){
			//$server = $this->getServersInternal($server_id);
			$server = Server::find($server_id);
			if (!$server) {
				return Response::json($msg, 403);
			}
			$api = GameServerApi::connect($server->api_server_ip, $server->api_server_port, $server->api_dir_id);
			switch ($type) {
				case 'open':
					$this->openShop($api);
					break;
				case 'close':
					$this->closeShop($api);
					break;
				case 'open_limit':
					$this->openShopTimeLimit($api);
					break;
				case 'close_limit':
					$this->closeShopTimeLimit($api);
					break;
				case 'on_item':
					$this->onShopItem($api);
					break;
				case 'off_item':
					$this->offShopItem($api);
					break;
				case 'status':
					return $this->loadShopStatus($api);
					break;
			}
		}
		return $api->sendResponse();
	}

	private function openShop($api)
	{
		$api->openShop();
// 		return $api->sendResponse();
	}

	private function closeShop($api)
	{
		$api->closeShop();
// 		return $api->sendResponse();	
	}

	private function loadShopStatus($api)
	{
		$response = $api->loadShopStatus();
		if (isset($reponse->error)) {
			return Response::json($body, 500);
		}
		$response->bonus_shop_open_time_date = date('Y-m-d H:i:s', $response->bonus_shop_open_time);
		$response->bonus_shop_close_time_date = date('Y-m-d H:i:s', $response->bonus_shop_close_time);
		$table = $this->initTableShop();
		$items = $table->getData();
		if (isset($response->items)) {
			foreach ($response->items as $v) {
				foreach ($items as $vv) {
					if ($v->id == $vv->id) {
						$v->item_id = $vv->itemid;
						$v->item_name = $vv->help;
					}
				}
			}	
		}
		return Response::json($response);	
	}

	private function openShopTimeLimit($api)
	{
		$open_time_from = strtotime(Input::get('open_time_from'));
		$open_time_to = strtotime(Input::get('open_time_to'));
		$duration = (int)Input::get('duration');
		if (!$open_time_from || !$open_time_to || $open_time_from < time() || $open_time_from >= $open_time_to) {
			//return Response::json(array('error'=>'please check the current platform and servers!'), 403);
			$msg['error'] = Lang::get('error.basic_input_error');
			return Response::json($msg, 403);
		}
		$duration = intval($open_time_to)-intval($open_time_from);
		$response = $api->openShopTimeLimit($open_time_from, $duration);
// 		return $api->sendResponse();
	}

	private function closeShopTimeLimit($api)
	{
		$api->closeShopTimeLimit();
// 		return $api->sendResponse();
	}

	private function onShopItem($api)
	{
		$shop_id = (int)Input::get('shop_id');
		$api->onShopItem($shop_id);
// 		return $api->sendResponse();
	}

	private function offShopItem($api)
	{
		$shop_id = (int)Input::get('shop_id');
		$api->offShopItem($shop_id);
// 		return $api->sendResponse();
	}


     public function getServers()
    {
    	$ser = $this->getUnionGame();
		$game_id = Session::get('game_id');
		$len = count($ser);
		for ($i=0; $i < $len; $i++) { 
			$game_arr[$i] =  $ser[$i]->gameid;	
		}
		$ga = array_unique($game_arr);
		$se = "";
		$servers = array();
		if (in_array($game_id, $ga)) {
			for ($i=0; $i < $len; $i++) { 
				if ($ser[$i]->gameid == $game_id) { //判断是联运
					/*$servers[$i]['server_id'] = $ser[$i]->serverid1;
					$ss = Server::where("game_id", "=", $game_id)->get();
	                for ($k=0; $k < count($ss); $k++) { 
	                    if ($ss[$k]->server_internal_id == $ser[$i]->serverid1) {
	                        $servers[$i]['server_name'] = $ss[$k]->server_name;
	                    }
	                }
	                $servers[$i] = (object)$servers[$i];*/
	                $se .= $ser[$i]->serverid2 . ' , '; 
				}
			}
			$se_arr = explode(',' , $se);
			unset($se_arr[count($se_arr)]);
			$server = Server::whereNotIn('server_internal_id', $se_arr)->get();
			for ($i=0; $i < count($server); $i++) { 
				if ($server[$i]->game_id == $game_id) {
					$servers[] = $server[$i];
				}
			}

		} else {
			$servers = Server::currentGameServers()->get();
		}
		return $servers;
    }

    private function getGameId()
    {
    	$ser = $this->getUnionGame();
		$len = count($ser);
		for ($i=0; $i < $len; $i++) { 
			$game_arr[$i] =  $ser[$i]->gameid;	
		}
		$ga = array_unique($game_arr);
		return $ga;
    }

    private function getServersInternal($server_id)
    {
    	$game_arr = $this->getGameId();
    	$game_id = Session::get('game_id');
    	if (in_array($game_id, $game_arr)) {
			$ser = Server::where("game_id", "=", $game_id)->get();
			for ($i=0; $i < count($ser); $i++) { 
				if ($ser[$i]->server_internal_id == $server_id) {
					$server = $ser[$i];
					break;
				}
			}
		} else {
			$server = Server::find($server_id);
		}
    	return $server; 
    }

    public function soldStaticsIndex()
    {
    	$servers = $this->getUnionServers();
    	$data = array(
    			'content' => View::make('serverapi.flsg_nszj.shop.soldStatics', array (
												'servers' => $servers)
    									)
    		);
    	return View::make('main', $data);
    }

    public function getSoldStatics()
    {
    	$start_time = strtotime(Input::get('start_time'));
    	$end_time = strtotime(Input::get('end_time'));
    	$server_ids = Input::get('server_id');
    	$game = Game::find(Session::get('game_id'));
    	$platform = Platform::find(Session::get('platform_id'));

        if ($server_ids==-1) {
        	$msg = array('error'=>'Did you pick server(s)?');
            return Response::json($msg, 403);
        }
        $allServerTotal = array();
        /////////////////////////////////选择全部服务器/////////////////////////////////////////
        if ($server_ids[0]==-2) 
        {
        	$servers = $this->getUnionServers();
        	$data = array();
        	foreach ($servers as $server) 
        	{	
        		//单服统计初始化
    			$serverTotalYuanb = 0;
				$serverTotalPers = 0;
				$serverTotal = 0;
				//
        		$server_id = $server->server_id;
        		$server = Server::find($server_id);
    			$server_internal_id = $server->server_internal_id;
    			//$server_internal_id = 2;//本地测试
	    		$api = SlaveApi::connect($game->eb_api_url, $game->eb_api_key, $game->eb_api_secret_key);
	    		$response = $api->getSoldStatics($game->game_id, $platform->platform_id, $server_internal_id, $start_time, $end_time);
        		$body = $response->body;
        		if($body==0 || is_null($body))
        		{
        			//return Response::json(array('error'=>'No data from database'), 403);
        			continue;
        		}
        		foreach ($body as $value) {
        			$v = (array) $value;
        			$yuanbToName = 'price_'. $v['yuanbao'];
        			if(isset($this->shopInfo[$yuanbToName]))
        			{
        				$yuanbToName = $this->shopInfo[$yuanbToName];
        				if(isset($allServerTotal[$yuanbToName]))
        				{	
        					$allServerTotal[$yuanbToName][0] += $v['yuanb_num'];
        					$allServerTotal[$yuanbToName][1] += $v['pers_num'];
        					$allServerTotal[$yuanbToName][2] += $v['yuanbao'] * $v['yuanb_num'];
        				}else{
        					$allServerTotal[$yuanbToName][0] = $v['yuanb_num'];
        					$allServerTotal[$yuanbToName][1] = $v['pers_num'];
        					$allServerTotal[$yuanbToName][2] = $v['yuanbao'] * $v['yuanb_num'];
        				}
        				$total = $v['yuanbao'] * $v['yuanb_num'];
        				$data[] = array($server->server_name, $yuanbToName, $v['yuanb_num'], $v['pers_num'], $total);
        				$serverTotalYuanb += $v['yuanb_num'];
        				$serverTotalPers += $v['pers_num'];
        				$serverTotal += $total;
        			}

        		}
        		$data[] = array('==========', 'Total', $serverTotalYuanb, $serverTotalPers, $serverTotal);
        	}
        	array_unshift($data, array('//// NEXT ////————————', '//// IS ////————————', 
        		'//// Each ////————————', '//// SERVER ////————————', '//// DATA ////————————'));
        	foreach ($allServerTotal as $key => $value) {
        		array_unshift($data, array('All Servers\'(selected) Commodities', $key, $value[0], $value[1], $value[2]));
        	}
        	return $data;
        } 
        /////////////////////////////////////////选择单服或者某几个服务器////////////////////////////////////////
        $data = array();
    	foreach ($server_ids as $server_id) 
    	{
    		//单服统计初始化
    		$serverTotalYuanb = 0;
			$serverTotalPers = 0;
			$serverTotal = 0;
			//
    		$server = Server::find($server_id);
    		$server_internal_id = $server->server_internal_id;
    		//$server_internal_id = 2;//本地测试
	    	$api = SlaveApi::connect($game->eb_api_url, $game->eb_api_key, $game->eb_api_secret_key);
	    	$response = $api->getSoldStatics($game->game_id, $platform->platform_id, $server_internal_id, $start_time, $end_time);
    		$body = $response->body;
    		if($body==0 || is_null($body))
        	{
        		//return Response::json(array('error'=>'No data from database'), 403);
        		continue;
        	}

        	foreach ($body as $value) {
        		$v = (array) $value;
        		$yuanbToName = 'price_'. $v['yuanbao'];
        		if(isset($this->shopInfo[$yuanbToName]))
        		{
        			$yuanbToName = $this->shopInfo[$yuanbToName];
        			if(isset($allServerTotal[$yuanbToName]))
        			{	
        				$allServerTotal[$yuanbToName][0] += $v['yuanb_num'];
        				$allServerTotal[$yuanbToName][1] += $v['pers_num'];
        				$allServerTotal[$yuanbToName][2] += $v['yuanbao'] * $v['yuanb_num'];
        			}else{
        				$allServerTotal[$yuanbToName][0] = $v['yuanb_num'];
        				$allServerTotal[$yuanbToName][1] = $v['pers_num'];
        				$allServerTotal[$yuanbToName][2] = $v['yuanbao'] * $v['yuanb_num'];
        			}
        			$total = $v['yuanbao'] * $v['yuanb_num'];
        			$data[] = array($server->server_name, $yuanbToName, $v['yuanb_num'], $v['pers_num'], $total);
        			$serverTotalYuanb += $v['yuanb_num'];
        			$serverTotalPers += $v['pers_num'];
        			$serverTotal += $total;
        		}
        	}
    		$data[] = array('==========', 'Total', $serverTotalYuanb, $serverTotalPers, $serverTotal);
    	}
        array_unshift($data, array('//// NEXT ////————————', '//// IS ////————————', 
        		'//// Each ////————————', '//// SERVER ////————————', '//// DATA ////————————'));
    	foreach ($allServerTotal as $key => $value) {
        	array_unshift($data, array('All Servers\'(selected) Commodities', $key, $value[0], $value[1], $value[2]));
        }
    	return $data;
    }

	private function initTable()
    {
        $game = Game::find(Session::get('game_id'));
        $table = Table::init(public_path() . '/table/' . 'flsg' . '/server.txt');
        return $table;
    }

    public function initTableShop()
    {
    	$game_id = Session::get('game_id');
        $game = Game::find($game_id);
        if(in_array($game_id, $this->area_shop_id)){
            $table = Table::init(public_path() . '/table/' . $game->game_code . '/shop'.$game_id.'.txt');
        }else{
            $table = Table::init(public_path() . '/table/' . $game->game_code . '/shop.txt');
        }
    	return $table;
    }

	public function getUnionGame()
	{
		$server = $this->initTable();
        $server = $server->getData();
        $server = (array)$server;
        return  $server;
	}

    public function oreFightIndex(){
    	$servers = $this->getUnionServers($no_skip=1);
    	if(empty($servers)){
    		App::abort(404);
    		exit();
    	}
    	$data = array(
    		'content' => View::make('serverapi.flsg_nszj.shop.ore_fight',
    			array(
    				'servers' => $servers
    			))
    	);
    	return View::make('main',$data);
    }

    public function oreFightOpenOrClose(){
    	$msg = array(
    	        'code' => Config::get('errorcode.unknow'),
    	        'error' => Lang::get('error.basic_input_error')
    	);
    	$server_ids = Input::get('server_id');
    	if(0 == $server_ids){
    		return Response::json(array('error'=>'请选择服务器！'), 403);
    	}
    	$game_id = Session::get('game_id');
    	$is_open = (int)Input::get('is_open');
    	foreach ($server_ids as $server_id) {
    		$server = Server::find($server_id);
    		if(!$server){
    			$msg['error'] = Lang::get('error.basic_not_found');
	            return Response::json($msg, 404);
    		}
    		$api = GameServerApi::connect($server->api_server_ip, $server->api_server_port, $server->api_dir_id);
    		$response = $api->oreFightOpenOrClose($is_open);
    		if(isset($response->active) && $response->active == 'true' && $is_open == 1){
    			$result[] = array(
    				'msg' => '(' . $server->server_name . ')' . '开启OK' .  "\n",
    				'status' => 'ok'
    			);
    		}elseif(isset($response->active) && $response->active == 'false' && $is_open == 0){
    			$result[] = array(
    				'msg' => '(' . $server->server_name . ')' . '关闭OK' .  "\n",
    				'status' => 'ok'
    			);
    		}else{
    			Log::info('0xbc84:' . var_export($response,true));
    			$result[] = array(
    			        'msg' => '(' . $server->server_name . ')' . 'error' . "\n",
    			        'status' => 'error'
    			);
    		}
    		
    	}
    	$msg = array(
    	        'result' => $result
    	);
    	return Response::json($msg);
    }

    public function oreFightLook(){
    	$msg = array(
    	        'code' => Config::get('errorcode.unknow'),
    	        'error' => Lang::get('error.basic_input_error')
    	);
    	$server_ids = Input::get('server_id');
    	if(0 == $server_ids){
    		return Response::json(array('error'=>'请选择服务器！'), 403);
    	}
    	$game_id = Session::get('game_id');
    	foreach ($server_ids as $server_id) {
    		$server = Server::find($server_id);
    		if(!$server){
    			$msg['error'] = Lang::get('error.basic_not_found');
	            return Response::json($msg, 404);
    		}
    		$api = GameServerApi::connect($server->api_server_ip, $server->api_server_port, $server->api_dir_id);
    		$response = $api->oreFightLook();
    		if(isset($response->active) && $response->active == 'true'){
    			$result[] = array(
    				'msg' => '(' . $server->server_name . ')' . '开启OK' .  "\n",
    				'status' => 'ok'
    			);
    		}else{
    			Log::info('0xbc84:' . var_export($response,true));
    			$result[] = array(
    			        'msg' => '(' . $server->server_name . ')' . 'error' . "\n",
    			        'status' => 'error'
    			);
    		}
    		
    	}
    	$msg = array(
    	        'result' => $result
    	);
    	return Response::json($msg);
    }
}