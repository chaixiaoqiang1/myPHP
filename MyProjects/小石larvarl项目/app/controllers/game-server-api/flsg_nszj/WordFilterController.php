<?php 

class WordFilterController extends \BaseController {
	
	public function index()
	{

		$server = Server::currentGameServers()->first();
		$game_id = Session::get("game_id");

		$words = KeywordBlacklist::where("game_id", "=", $game_id)->where("is_deleted", "=", 0)->get();
		if (!$words) {
		    App::abort(404);
		}
		$data = array(
			'content' => View::make('serverapi.flsg_nszj.wordfilter.index', array(
				'words' => $words
			)),
		);
		return View::make('main', $data);
	}

	public function add()
	{
		$msg = array(
			'code' => Config::get('errorcode.unknow'),
			'error' => Lang::get('error.basic_input_error'),
		);
		$rules = array(
			'words' => 'required',
		);
		$validator = Validator::make(Input::all(), $rules);
		if ($validator->fails()) {
			return Response::json($msg, 403);
		}

		$words = array_map(function($v){
			$word = trim($v);
			if ($word) {
				return $word;
			}
		}, explode("\n", Input::get('words')));
		$is_delete = Input::get('is_delete') == 'true' ? true : false;
		
        $game_id = Session::get("game_id");
        
        // 将新添的屏蔽词列表存放到数据库里面，并送到视图
        foreach ( $words as $word ) {
            if(!$is_delete){
                $count = (int)KeywordBlacklist::where("game_id", "=", $game_id)->where("word", "=", $word)->count();
                if ($count == 0) {//如果屏蔽词不存在，则insert
                    $blacklist = new KeywordBlacklist;
                    $blacklist->word = $word;
                    $blacklist->game_id = $game_id;
                    $blacklist->save();
                }else if($count == 1){//如果屏蔽词存在，则update
                	KeywordBlacklist::where("game_id", "=", $game_id)->where('word', '=', $word)->update(array('is_deleted' => 0));
                }
            } else {//“删除”屏蔽词
                $query = (int)KeywordBlacklist::where("game_id", "=", $game_id)->where("word", "=" , $word)->count();
                if ( $query != 0 ){
                    KeywordBlacklist::where("game_id", "=", $game_id)->where('word', '=', $word)->update(array('is_deleted' => 1));
                }
            }
        }
        
        $servers = Server::currentGameServers()->get();
        
        foreach($servers as $server){
        	$api = GameServerApi::connect($server->api_server_ip, $server->api_server_port, $server->api_dir_id);
        	$response = $api->addWordFilter($words, $is_delete);
        }
        $word_lists = array();
 		$game_id = Session::get("game_id");
 		
 		$words = KeywordBlacklist::where("game_id", "=", $game_id)->where("is_deleted", "=", 0)->get();
//  		if (!$words) {
//  			App::abort(404);
//  		}
 		foreach ($words as $item){
 			$word_lists[] = array(
 				'word' => $item->word,
 				'is_deleted' => $item->is_deleted ? 'Yes' : 'No',
 			);
 		}
//  		$s = var_export($response,true);
//  		Log::info($s);
		return Response::json(array('words' => $word_lists));
	}
}