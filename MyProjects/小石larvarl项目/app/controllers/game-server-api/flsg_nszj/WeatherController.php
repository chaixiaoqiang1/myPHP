<?php

class WeatherController extends \BaseController
{

    public function index()
    {
        //$servers = Server::currentGameServers()->get();
        $servers = $this->getUnionServers();
        if (empty($servers)) {
            App::abort(404);
            exit();
        }
        $data = array(
            'content' => View::make('serverapi.flsg_nszj.weather.index', array(
                'servers' => $servers
            ))
        );
        return View::make('main', $data);
    }

    public function setWeather()
    {
        $msg = array(
            'code' => Config::get('errorcode.unknow'),
            'error' => Lang::get('error.basic_input_error')
        );
        $rules = array(
            'weather_type' => 'required|min:1'
        );
        $validator = Validator::make(Input::all(), $rules);
        if ($validator->fails()) {
            return Response::json($msg, 403);
        }
        //天气类型 3 下雪 2 落叶 1 下雨
        $weather_type = (int) Input::get('weather_type');
        $server_ids = Input::get('server_id');
        foreach ($server_ids as $server_id) {
            $server = Server::find($server_id);
            if (! $server) {
                $msg['error'] = Lang::get('error.basic_not_found');
                return Response::json($msg, 404);
            }
            $api = GameServerApi::connect($server->api_server_ip, $server->api_server_port, $server->api_dir_id);
            
            $response = $api->setWeather($weather_type);
        }
        return $api->sendResponse();
    }

    private function initTable()
    {
        $game = Game::find(Session::get('game_id'));
        $table = Table::init(public_path() . '/table/' . 'flsg' . '/server.txt');
        return $table;
    }

}