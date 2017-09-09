<?php

class ChattingController extends \BaseController {

    public function index()
    {
        $servers = Server::currentGameServers()->get();
        $data = array(
                'content' => View::make('serverapi.flsg_nszj.chatting.index', 
                        array(
                                'servers' => $servers
                        ))
        );
        return View::make('main', $data);
    }

    public function getData()
    {
        
        $type = (int) Input::get('type');
        $player_name = Input::get('player_name');
        $to_player_name = Input::get('to_player_name');
        $start_time = strtotime(trim(Input::get('start_time')));
        $end_time = strtotime(trim(Input::get('end_time')));
        $server_id = Input::get('server_id');
        $server = Server::find($server_id);
        if (! $server)
        {
            $msg['error'] = Lang::get('error.basic_not_found');
            return Response::json($msg, 404);
        }
        $api = GameServerApi::connect($server->api_server_ip, 
                $server->api_server_port, $server->api_dir_id);
       
        $response = $api->getChattingRecords(67895382, time()-23*60*60, time(), 67895402);
        $player = $api->getPlayerInfoByName($player_name);
        //Log::info(var_export($player, true));
        if (! isset($player->player_id))
        {
            $msg['error'] = Lang::get('serverapi.player_not_found');
            return Response::json($msg, 404);
        }
        $player_id = $player->player_id;
        if ($type == 1)
        { // 查询聊天好友
            $response = $api->getChattingFriends(67895382, $start_time, $end_time);
            //Log::info(var_export($response, true));
        } else
        { // 查询聊天记录
            $to_player = $api->getPlayerInfoByName($to_player_name);
            if (! isset($to_player->player_id))
            {
                $msg['error'] = Lang::get('serverapi.player_not_found');
                return Response::json($msg, 404);
            }
            $to_player_id = $to_player->player_id;
            $response = $api->getChattingRecords(67895382, time()-23*60*60, time(), 67895402);
            //Log::info(var_export($response, true));
        }
    }
}