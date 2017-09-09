<?php

class ShengyuController extends \BaseController {

    /**
     * 圣域争霸--入口
     */
    public function shengyuIndex()
    {
        $servers = $this->getUnionServers();

        if (empty ( $servers ))
        {
            App::abort ( 404 );
            exit ();
        }
        $data = array (
            'content' => View::make ( 'serverapi.flsg_nszj.tournament.shengyu', array (
                'servers' => $servers
            ) )
        );
        return View::make ( 'main', $data );
    }
    /**
     * 全服圣域争霸--入口
     */
    public function allShengyuIndex()
    {
        $servers = Server::currentGameServers($no_skip=1)->get();

        if (empty ( $servers ))
        {
            App::abort ( 404 );
            exit ();
        }
        $data = array (
            'content' => View::make ( 'serverapi.flsg_nszj.tournament.all_shengyu', array (
                'servers' => $servers
            ) )
        );
        return View::make ( 'main', $data );
    }
    /**
     * 圣域争霸--开启
     */
    public function shengyuData()
    {
        $msg = array (
            'code' => Config::get ( 'errorcode.unknow' ),
            'error' => Lang::get ( 'error.basic_input_error' )
        );

        $server_ids = Input::get('server_id');
        $server_id2 = (int)Input::get('server_id2');

        $main_server = Server::find($server_id2);

        if (! $main_server) {
            $msg['error'] = Lang::get ( 'error.basic_not_found' );
            return Response::json ( $msg, 404 );
        }
        //var_dump($main_server);die();
        $host = $main_server->api_server_ip;
        $port = $main_server->match_port;
        $is_all = (int)Input::get('is_all');
        $result = array();
        foreach ($server_ids as $server_id) {
            $server = Server::find($server_id);
            if (! $server) {
                $msg['error'] = Lang::get ( 'error.basic_not_found' );
                return Response::json ( $msg, 404 );
            }
            $api = GameServerApi::connect($server->api_server_ip, $server->api_server_port, $server->api_dir_id);

            // 建立连接
            $response = $api->updateShengyu( $host, $port, $is_all, true);
            //$s = var_export($response,true);
            //Log::info("kaiqi shengyu zhengba response:==>".var_export($response, true));
            if (isset($response->result) && $response->result == 'OK') {
                $result[] = array (
                    'msg' => ' ( ' . $server->server_name . ' ) ' . Lang::get('serverapi.tournament_shengyu_connect') . '['.$host . ':' . $port . ']' . ' : ' . $response->result . "\n",
                    'status' => 'ok'
                );
            } else if (isset($response->error_code) && isset($response->error)) {
                $result[] = array (
                    'msg' => ' ( ' . $server->server_name . ' ) ' . Lang::get('serverapi.tournament_shengyu_connect') . '['.$host . ':' . $port . ']' . ' : ' .  $response->error . "\n",
                    'status' => 'error'
                );
            }
        }
        $msg = array (
            'result' => $result
        );
        return Response::json($msg);
    }
    /**
     * 圣域争霸--查询
     */
    public function shengyuLookup()
    {
        $msg = array (
            'code' => Config::get ( 'errorcode.unknow' ),
            'error' => Lang::get ( 'error.basic_input_error' )
        );
        $server_ids = Input::get('server_id');
        $is_all = (int)Input::get('is_all');
        $result = array();
        foreach ($server_ids as $server_id) {
            $server = Server::find($server_id);
            if (! $server) {
                $msg['error'] = Lang::get ( 'error.basic_not_found' );
                return Response::json ( $msg, 404 );
            }
            $api = GameServerApi::connect($server->api_server_ip, $server->api_server_port, $server->api_dir_id);
            $response = $api ->searchShengyu($is_all);
            //Log::info("chakan shengyu zhengba response:==>".var_export($response, true));
            if (isset($response->error)) {
                $result[] = array (
                    'msg' => ' ( ' . $server->server_name . ' ) : ' . $response->error ."\n" ,
                    'status' => 'error'
                );
            }else if(isset($response->status) && $response->status == 'Disconnected') {
                $result[] = array (
                    'msg' => ' ( ' . $server->server_name . ' ) : ' . Lang::get('serverapi.tournament_shengyu_disconnect') . "===".$response->active. "\n",
                    'status' => 'disconnected'
                );
            }else if(isset($response->status) && $response->status == 'Connected') {
                $main_server_name = ' ';
                $query = Server::where("api_server_ip", $response->host)->where("match_port", $response->port)->where("game_id", Session::get('game_id'))->first();
                if($query){
                    $main_server_name = $query ->server_name;
                }
                $result[] = array (
                    'msg' => ' ( ' . $server->server_name . ' ) : ' . Lang::get('serverapi.tournament_shengyu_connect') .' ( ' . $main_server_name . ' ) : ' .$response->host .' : '.$response->port. "===".$response->active . "\n",
                    'status' => 'connected'
                );
            }else if(isset($response->status) && $response->status == 'Connecting') {
                $main_server_name = ' ';
                $query = Server::where("api_server_ip", $response->host)->where("match_port", $response->port)->where("game_id", Session::get('game_id'))->first();
                if($query){
                    $main_server_name = $query ->server_name;
                }
                $result[] = array (
                    'msg' => ' ( ' . $server->server_name . ' ) : ' . Lang::get('serverapi.tournament_shengyu_connecting') .' ( ' . $main_server_name . ' ) : ' .$response->host .' : '.$response->port. "===".$response->active. "\n",
                    'status' => 'connecting'
                );
            }else{
                $result[] = array (
                    'msg' => ' ( ' . $server->server_name . ' ) : ' . $response->error . "\n",
                    'status' => 'error'
                );
            }

        }
        $msg = array (
            'result' => $result
        );
        return Response::json($msg);
    }

    public function shengyuClose()
    {
        $server_ids = Input::get('server_id');
        $is_all = (int)Input::get('is_all');
        $result = array();
        foreach ($server_ids as $key => $server_id) {
            $server = Server::find($server_id);
            if (!$server) {
                $msg['error'] = Lang::get('error.basic_not_found');
                return Reposne::json($msg, 403);
            }
            $host = $server->api_server_ip;
            $port = $server->match_port;
            $api = GameServerApi::connect($server->api_server_ip, $server->api_server_port, $server->api_dir_id);
            $response = $api->shengyuClose($host, $port, $is_all, false);

            //Log::info("guanbi shengyu zhengba response:==>".var_export($response, true));

            if (isset($response->result) && $response->result == 'OK') {
                $result[] = array (
                    'msg' => ' ( ' . $server->server_name . ' ) ' . Lang::get('serverapi.tournament_shengyu_close_success') . '['.$host . ':' . $port . ']' . ' : ' . $response->result . "\n",
                    'status' => 'ok'
                );
            } else if (isset($response->error_code) && isset($response->error)) {
                $result[] = array (
                    'msg' => ' ( ' . $server->server_name . ' ) ' . Lang::get('serverapi.tournament_shengyu_close_error') . '['.$host . ':' . $port . ']' . ' : ' .  $response->error . "\n",
                    'status' => 'error'
                );
            }
        }
        $msg = array (
            'result' => $result
        );
        return Response::json($msg);

    }
    
}