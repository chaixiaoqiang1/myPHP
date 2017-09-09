<?php

class BaseController extends Controller {

    protected $world_edition_list = array(59, 60, 61, 62, 63);
    //保存有所有的页游game_id
    protected $webgameids = array(1,2,3,4,5,6,7,8,9,10,11,12,13,14,15,16,17,18,19,20,21,22,23,24,25,26,27,28,29,30,31,32,33,34,35,36,37,38,39,40,41,42,43,44,45,46,47,48,49,50,53,59,60,61,62,63,64,68,70);

    //手游id所在的数组     --- 已弃用
    protected $mobilegames = array(54,66,69,72,73,74,75,76,78,79,80,81,82,83);

    protected $yysggameids = array(54,69,72,73,74,75,76,80,81);	//夜夜三国系列游戏   --- 已弃用
    /**
	*对于item表安地区进行区分，女神目前同版本台湾、越南、巴西、泰国相同，可以共用item表
	*
    */
    protected $area_item_id = array(4, 8, 44, 45);//要单独区分item表的game_id ..因为游戏更版本的时间不一样，所以都区分开
	/**
	 * Setup the layout used by the controller.
	 *
	 * @return void
	 */

	/*
	对于chenghao表安地区进行区分，女神目前只有土耳其和其他地区不一样
	*/
	protected $area_chenghao_id = array(2, 44);

	protected $area_shop_id = array(8, 36, 41, 43, 44, 45, 70);//台湾 越南 泰国 巴西 土耳其 印尼 俄罗斯
    protected $area_mark_id = array(44);//女神星座表

    protected $area_soulcasket_id = array(79, 82, 83, 84, 85);//单独根据地区区分，英文 印尼 越南 泰国 欧美
	
    protected function setupLayout()
	{
		if ( ! is_null($this->layout))
		{
			$this->layout = View::make($this->layout);
		}
	}

	protected function getUnionGame()
    {
        $server = Table::init(public_path() . '/table/' . 'flsg' . '/server.txt');
        $server = $server->getData();
        $server = (array)$server;
        return $server;
    }

    protected function getUnionServers($no_skip=0)
    {
        $game_id = Session::get('game_id');
        $game = Game::find($game_id);
        $ser = $this->getUnionGame();
        $api = SlaveApi::connect($game->eb_api_url, $game->eb_api_key, $game->eb_api_secret_key);
        $response = $api->getUnionServers($game_id, $ser);
        if ($response == "fail") {
            $server = Server::currentGameServers($no_skip)->get();
        } else {
            $server = Server::currentGameServers($no_skip)->whereNotIn('server_internal_id', $response)->get();
        }
        return $server;
    }

    protected function getMainServer($game_id, $server_internal_id){    //根据game_id和server_internal_id返回此服务器的所属的主服
        $server_info = $this->getUnionGame();   //获取合服文件内容
        foreach ($server_info as $value) {
            if($game_id == $value->gameid){
                if(in_array($server_internal_id, explode(',', $value->serverid2))){ //如果属于本条数据的从服，返回其主服
                    return (int)$value->serverid1;
                }
                if($server_internal_id == $value->serverid1){   //如果是本条记录的主服，返回主服
                    return (int)$value->serverid1;
                }
            }
        }
        return $server_internal_id;
    }

    protected function getUnionServersDesc($no_skip=0)
    {
        $game_id = Session::get('game_id');
        $game = Game::find($game_id);
        $ser = $this->getUnionGame();
        $api = SlaveApi::connect($game->eb_api_url, $game->eb_api_key, $game->eb_api_secret_key);
        $response = $api->getUnionServers($game_id, $ser);
        if ($response == "fail") {
            $server = Server::currentGameServers($no_skip)->get(); 
        }else{
            $skip_servers = Config::get('skip_servers.'.$game_id);
            if($no_skip){
                $server = Server::whereNotIn('server_internal_id', $response)->where('game_id', $game_id)->orderBy('server_id', 'desc')->get();
            }else{
                $server = Server::whereNotIn('server_internal_id', $response)->whereNotIn('server_internal_id', $skip_servers)->where('game_id', $game_id)->orderBy('server_id', 'desc')->get();
            }       
        }
        return $server;
    }

    public function show_message($code, $message=''){
        $data = array(
                'content' => View::make('show_message', 
                        array(
                                'code' => $code,
                                'msg' => $message,
                        ))
        );
        return View::make('main', $data);
    }

    public function OpenFile($path)
    {
        $table = Table::init($path);
        return $table->getData();
    }

    public function current_time_nodst($time){ //这个方法用来校正某些时区夏令时冬令时造成的时间选择与实际意义不符的情况
        $localtime_time = localtime($time, true);   //用以获取传入的时间时令
        $localtime_now = localtime(time(), true);   //用以获取当前时令
        if(isset($localtime_time['tm_isdst']) && isset($localtime_now['tm_isdst'])){
            $time = $time + ($localtime_time['tm_isdst'] - $localtime_now['tm_isdst']) * 3600;
        }
        return $time;
    }

}