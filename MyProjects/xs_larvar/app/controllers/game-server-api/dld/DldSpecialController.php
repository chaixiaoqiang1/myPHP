<?php

class DldSpecialController extends BaseController
{
    public function index()
    {
        $servers = $this->getUnionServers();
        $data = array(
            'content' => View::make('serverapi.dld.dldSpecial', array(
                'servers' => $servers
            ))
        );
        return View::make('main', $data);
    }

    //重置仙境演武场
    public function resetXJYWC()
    {
        $servers_id = Input::get('servers_id');
        foreach ($servers_id as $server_id) {
            $server = Server::find($server_id);
            $api = DldGameServerApi::connect($server->api_server_ip, $server->api_server_port, $server->api_dir_id);
            $response = $api->resetXJYWC();
            if (isset($response->result) && $response->result == 'OK') {
                $result[] = array(
                    'msg' => ' ( ' . $server->server_name . ' ) : ' . $response->result . "\n",
                    'status' => 'ok'
                );
            } else {
                $result[] = array(
                    'msg' => ' ( ' . $server->server_name . ' ) : ' . 'error' . "\n",
                    'status' => 'error'
                );
            }
        }
        $msg = array(
            'result' => $result
        );
        return Response::json($msg);
    }

    public function budokaiIndex()
    {
        $servers = $this->getUnionServers();
        $data = array(
            'content' => View::make('serverapi.dld.dldBudokai', array(
                'servers' => $servers
            ))
        );
        return View::make('main', $data);
    }

    public function budokaiOpen()
    {
        $servers_id = Input::get('servers_id');
        foreach ($servers_id as $server_id) {
            $server = Server::find($server_id);
            $api = DldGameServerApi::connect($server->api_server_ip, $server->api_server_port, $server->api_dir_id);
            $response = $api->setBudokai(true);
            //Log::info("galaxy budokai response:".var_export($response, true));
            if (isset($response->result) && $response->result == 'OK') {
                $result[] = array(
                    'msg' => ' ( ' . $server->server_name . ' ) : open ' . $response->result . "\n",
                    'status' => 'ok'
                );
            } else {
                $result[] = array(
                    'msg' => ' ( ' . $server->server_name . ' ) : open ' . 'error' . "\n",
                    'status' => 'error'
                );
            }
        }
        $msg = array(
            'result' => $result
        );
        return Response::json($msg);
    }

    public function budokaiClose()
    {
        //Log::info("budokaiClose()");
        $servers_id = Input::get('servers_id');
        //Log::info("servers_id:".var_export($servers_id, true));
        foreach ($servers_id as $server_id) {
            $server = Server::find($server_id);
            //Log::info("ip:".$server->api_server_ip."--port:".$server->api_server_port."--dir id:".$server->api_dir_id);
            $api = DldGameServerApi::connect($server->api_server_ip, $server->api_server_port, $server->api_dir_id);
            $response = $api->setBudokai(false);
            //Log::info("galaxy budokai response:".var_export($response, true));
            if (isset($response->result) && $response->result == 'OK') {
                $result[] = array(
                    'msg' => ' ( ' . $server->server_name . ' ) : close ' . $response->result . "\n",
                    'status' => 'ok'
                );
            } else {
                $result[] = array(
                    'msg' => ' ( ' . $server->server_name . ' ) : close ' . 'error' . "\n",
                    'status' => 'error'
                );
            }
        }
        $msg = array(
            'result' => $result
        );
        return Response::json($msg);
    }

    public function lvTalkIndex()
    {
        $servers = $this->getUnionServers();
        $data = array(
            'content' => View::make('serverapi.dld.lvTalkIndex', array(
                'servers' => $servers
            ))
        );
        return View::make('main', $data);
    }

    public function lvTalkSwitch()
    {
        $servers_id = Input::get('servers_id');

        $level = (int)Input::get('talk_level');

        $active = 1;
        if (0 >= $level) {   //等级填0或不填都是0，对应关闭。填正数则是开启。
            $active = 0;
        }
        foreach ($servers_id as $server_id) {
            $server = Server::find($server_id);
            $api = DldGameServerApi::connect($server->api_server_ip, $server->api_server_port, $server->api_dir_id);
            $response = $api->setLvTalk($active, $level);
            if (isset($response->result) && $response->result == 'OK') {
                $result[] = array(
                    'msg' => ' ( ' . $server->server_name . ' ) : set ' . $response->result . "\n",
                    'status' => 'ok'
                );
            } else {
                $result[] = array(
                    'msg' => ' ( ' . $server->server_name . ' ) : set ' . 'error' . "\n",
                    'status' => 'error'
                );
            }
        }
        $msg = array(
            'result' => $result
        );
        return Response::json($msg);
    }

    public function lvTalkLookup()
    {
        //Log::info("budokaiClose()");
        $servers_id = Input::get('servers_id');
        //Log::info("servers_id:".var_export($servers_id, true));
        foreach ($servers_id as $server_id) {
            $server = Server::find($server_id);
            //Log::info("ip:".$server->api_server_ip."--port:".$server->api_server_port."--dir id:".$server->api_dir_id);
            $api = DldGameServerApi::connect($server->api_server_ip, $server->api_server_port, $server->api_dir_id);
            $response = $api->lvTalkLookup();
            //Log::info("galaxy budokai response:".var_export($response, true));
            if (isset($response->active) && isset($response->level)) {
                if(0 == $response->active)
                    $active = Lang::get('serverapi.talk_on');
                else $active = Lang::get('serverapi.talk_off');
                $result[] = array(
                    'msg' => ' ( ' . $server->server_name . ' ) ：active state :  ' . $active . '  level : '.$response->level."\n",
                    'status' => 'ok'
                );
            } else {
                $result[] = array(
                    'msg' => ' ( ' . $server->server_name . ' ) : lookup ' . 'error' . "\n",
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