<?php

class ServerController extends \BaseController {

    const SPEC_FIX = 'XYD9061';

    private $versions = array(
            'ares',
            'bres',
            'cres',
            'dres'
    );

    private $type_s_types = array(
            0 => 'PHP', 
            1 => 'Flash', 
            2 => 'C++',
            );

    /**
     * Display a listing of the resource.
     *
     * @return Response
     */
    public function index()
    {
        $server = Server::currentGameServers($no_skip=1)->paginate(20);
        foreach ($server as $k => &$v)
        {
            if ($v->open_server_time)
            {
                $v->open_server_time = date('Y-m-d H:i:s', $v->open_server_time);
            }
        }
        unset($v);
        
        $data = array(
                'content' => View::make('servers.index', 
                        array(
                                'server' => $server
                        ))
        );
        return View::make('main', $data);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return Response
     */
    public function create()
    {
        $server = Server::currentGameServers()->first();
        if (! $server)
        {
            $server = (object) array(
                    'server_track_name' => '',
                    'server_name' => '',
                    'version' => $this->versions[2],
                    'game_path' => 'bin-release/Game.swf',
                    'resource_path' => 'resource',
                    'xloader_path' => 'bin-release/XLoader.swf',
                    'battle_report_path' => 'bin-release/BattleReport.swf',
                    'server_ip' => '',
                    'server_port' => '',
                    'server_internal_id' => 0,
                    'use_for_month_card' => 0,
                    'api_server_ip' => '',
                    'api_server_port' => 3328,
                    'api_dir_id' => 0,
                    'tp_server_id' => 0,
                    'qq_zone_id' => 0
            );
            $max_server = (object) array(
                    'api_dir_id' => 0
            );
        } else
        {
            // 开在同一个机器上不同的游戏，需要根据api_server_ip取
            $max_server = Server::where('api_server_ip', $server->api_server_ip)->orderBy(
                    'api_dir_id', 'DESC')->first();
        }
        
        if ($server->api_server_ip == 'm1.qiqiwu.com')
        {
            $server->api_server_ip = 'yy.qiqiwu.com';
            $server->server_ip = 'yy.qiqiwu.com';
            $max_server = Server::where('api_server_ip', $server->api_server_ip)->orderBy(
                    'api_dir_id', 'DESC')->first();
        }
        $game_array = array(
            '7',
            '9',
            '10',
            '12',
            '13',
            '14',
            '15',
            '16',
            '17',
            '18',
            '19',
            '20',
            '21',
            '22',
            '23',
            '24',
            '25',
            '26',
            '27',
            '28',
            '29',
            '31',
            '32',
            '33',
            '34',
            '35',
            '37',
            '39',
            '40',
            '42'
        ); 
        $game_id = Session::get('game_id');
        if (in_array($game_id, $game_array) ) {
            $server->server_port = (10000 + ($max_server->api_dir_id + 1) * 10);
            $server->match_port = (10000 + ($max_server->api_dir_id + 1) * 10 + 1);
            // $server->server_port = (63300+($max_server->api_dir_id-532) * 10);
            // $server->match_port = (63300+($max_server->api_dir_id-532) * 10 + 1);
        }else{
            $server->server_port = (10000 + ($max_server->api_dir_id + 1) * 100);
            $server->match_port = (10000 + ($max_server->api_dir_id + 1) * 100 + 1);
        }
        //var_dump($server);die();
        $server->server_internal_id = 1 + $server->server_internal_id;
        $server->api_dir_id = 1 + $max_server->api_dir_id; 

        if($server->api_dir_id >= 250){
            $game_server = Server::where('api_server_ip', $server->api_server_ip)->selectRaw(
                    'distinct api_dir_id as api_dir_id')->get();
            $all_dir_ids = array();
            foreach ($game_server as $key => $value) {
                $all_dir_ids[] = $value->api_dir_id;
            }
            $dir_init = 4;
            for ($dir_init = 4; $dir_init < 250; $dir_init++) { 
                if(!in_array($dir_init, $all_dir_ids)){
                    break;
                }
            }
            if($dir_init != 250){
                $server->api_dir_id = $dir_init;
                $server->server_port = (10000 + ($dir_init) * 100);
                $server->match_port = (10000 + ($dir_init) * 100 + 1);
            }else{
                return $this->show_message('', '没有剩余的api_dir_id,请联系技术！');
            }
        }

        $server->tp_server_id = 1 + $server->tp_server_id;
        $game_id = Session::get('game_id');
        if($game_id == 5){
            $server->qq_zone_id = $server->qq_zone_id + 1;
        }
        
        $game = Game::find(Session::get('game_id'));
        
        $sunday = date('N');
        $server->version = $this->versions[2];
        // if ($sunday == 7)
        // {
        //     if ($game->game_id == 1 || $game->game_id == 8)
        //     {
        //         $server->version = $this->versions[3];
        //     }
        // }
        if(in_array($game_id, $this->world_edition_list)) {     //世界服开服时五个地区新加的服务器需要连接在同一个api_dir_id,server_port和match_port
            $dir_id = $max_server->api_dir_id;
            $count = Server::where('api_server_ip', $server->api_server_ip)->where('api_dir_id', $dir_id)->count();
            if ($count < 5) {   // 连接到新服的地区不满5个时，新建的服连入该新服
                $server->server_port = $max_server->server_port;
                $server->api_dir_id = $max_server->api_dir_id;
                $server->match_port = $max_server->match_port;
            }
        }
        if($game_id == 38) { // 神仙道开服有点不一样
        	$server_ip_arr = explode(".", $server->server_ip);
        	$num = (int)substr($server_ip_arr[0],1);
        	$sxd_domain = "s" . ($num + 1) . ".idgameland.com";
        	$server->server_ip = $sxd_domain;
        	$server->api_server_ip = $sxd_domain;
        }
        if ($game_id == 46) { //黑暗之光开服
            $server_ip_arr = explode('/', $server->server_ip);
            $server_arr = explode('.', $server_ip_arr[1]);
            $hazg_domain = "admin.hg.yeapgame.com/". ($server_arr[0] +1).".html";
            $server->server_ip = $hazg_domain;
            $server->api_server_ip = $hazg_domain; 
            $server->server_internal_id = $server_arr[0] +1 ;
        }
        
        $data = array(
                'content' => View::make('servers.create', 
                        array(
                                'server' => $server,
                                'game' => $game
                        ))
        );
        return View::make('main', $data);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @return Response
     */
    public function store()
    {
        $msg = array(
                'code' => Config::get('errorcode.server_add'),
                'error' => Lang::get('error.server_add')
        );
        $rules = array(
                'server_track_name' => 'required',
                'server_name' => 'required',
                'version' => 'required',
                'game_path' => 'required',
                'resource_path' => 'required',
                'xloader_path' => 'required',
                'battle_report_path' => 'required',
                'server_ip' => 'required',
                'server_port' => 'required',
                'server_internal_id' => 'required',
                'api_server_ip' => 'required',
                'api_server_port' => 'required',
                'api_dir_id' => 'required'
        );
        
        $game_id = Session::get('game_id');
        $game = Game::find($game_id);
        
        if (! $game)
        {
            $msg['error'] = Lang::get('error.game_not_found');
            return Response::json($msg, 404);
        }
        
        $validator = Validator::make(Input::all(), $rules);
        if ($validator->fails())
        {
            return Response::json($msg, 403);
        } else
        {
            $server = new Server();
            $server->open_server_time = strtotime(
                    Input::get('open_server_time'));
            $server->server_track_name = trim(Input::get('server_track_name')) .
                     self::SPEC_FIX;
            $server->server_name = trim(Input::get('server_name'));
            $server->game_path = trim(Input::get('game_path'));
            $server->version = trim(Input::get('version'));
            $server->resource_path = trim(Input::get('resource_path'));
            $server->xloader_path = trim(Input::get('xloader_path'));
            $server->battle_report_path = trim(Input::get('battle_report_path'));
            $server->server_ip = trim(Input::get('server_ip'));
            $server->server_port = trim(Input::get('server_port'));
            $server->server_internal_id = (int) Input::get('server_internal_id');
            $server->api_server_ip = trim(Input::get('api_server_ip'));
            $server->api_server_port = trim(Input::get('api_server_port'));
            $server->api_dir_id = (int) Input::get('api_dir_id');
            $server->game_id = $game_id;
            $server->match_port = (int) Input::get('match_port');
            
            $tp_server_id = (int) Input::get('tp_server_id');
            if ($tp_server_id)
            {
                $server->tp_server_id = $tp_server_id;
            }
            $qq_zone_id = (int) Input::get('qq_zone_id');
            if ($qq_zone_id)
            {
                $server->qq_zone_id = $qq_zone_id;
            }
            $use_for_month_card = (int) Input::get('use_for_month_card');
            if ($use_for_month_card)
            {
                $server->use_for_month_card = $use_for_month_card;
            }
            $monday = date('N', $server->open_server_time);
            if ($monday == 1)
            {
                if ($game->game_id == 1 || $game->game_id == 8)
                {
                    $server->version = $this->versions[3];
                }
            }
            
            $platform = Game::find($server->game_id)->platform;
            $platform_id = $platform->platform_id;
            $region_code = Platform::find($platform_id)->region->region_code;
            $platform_id = str_pad($platform_id, 3, '0', STR_PAD_LEFT);
            $game_id = str_pad($server->game_id, 3, '0', STR_PAD_LEFT);
            $server_id = str_pad($server->server_internal_id, 4, '0', 
                    STR_PAD_LEFT);
            if(strlen($server_id) > 4)
                $server_id = substr($server_id, -4);
            $server_uid = $region_code . $platform_id . $game_id . $server_id;
            $server->server_uid = $server_uid;
//            Log::info('ready');

            $response = $this->addPlatformServer($platform, $server);
//            Log::info('new Server');
//            Log::info(var_export($response, true));
            if ($response->http_code == 200 && $response->payment_server->http_code == 200)
            {
//                Log::info('platform and payment server success.');
                $api = SlaveApi::connect($game->eb_api_url, $game->eb_api_key,
                        $game->eb_api_secret_key);
                $api->runOpenServerScript($server->game_id,
                        $server->server_internal_id, $server->server_ip,
                        $server->api_dir_id, $server->open_server_time);
//                Log::info('addNewPlatformServer'.$server->game_id.'--'. $server->server_internal_id.'--'. $server->server_ip.'--'. $server->api_dir_id. '--'.$server->open_server_time);
                Log::info(Auth::user()->username.' CREATE SERVER '.$server->server_track_name.' AT TIME '.date("Y-m-d H:i:s", time()));
                return Response::json($response->body, $response->http_code);
            } else
            {
                $msg['error'] = Lang::get('error.server_create_failed');
                return Response::json($msg, 500);
            }
        }
    }

    private function addPlatformServer($platform, $server)
    {
        $api = PlatformApi::connect($platform->platform_api_url, 
                $platform->api_key, $platform->api_secret_key);
        $params = array();
        $replace = array(
                'resource_path' => 'source_path',
                'xloader_path' => 'xload_path',
                'api_dir_id' => 'dir_id',
                'qq_zone_id' => 'zoneid'
        );
        $server_array = $server->toArray();
        unset($server_array['created_at']);
        unset($server_array['updated_at']);
        foreach ($server_array as $k => $v)
        {
            if (isset($replace[$k]))
            {
                $params[$replace[$k]] = $v;
            } else
            {
                $params[$k] = $v;
            }
        }
        $params['is_server_on'] = isset($server_array['is_server_on']) ? $server_array['is_server_on'] : 0;
//        Log::info(var_export($params, true));
//        Log::info('addNewPlatformServer');
        $response = $api->addNewPlatformServer($params);
//        Log::info(var_export($response, true));
        if ($response->http_code == 200 && isset($response->body->server_id))
        {
            $params['server_id'] = $response->body->server_id;
            
            $server->platform_server_id = $params['server_id'];
//            Log::info('before add platform_server_id. id:'.$params['server_id']);
            $server->save();
            $response->payment_server = $this->addPaymentServer($platform, $params);
        }
        return $response;
    }

    private function addPaymentServer($platform, $server)
    {
        $api = PlatformApi::connect($platform->payment_api_url, 
                $platform->api_key, $platform->api_secret_key);
        $params = array(
                'server_id' => $server['server_id'],
                'server_name' => $server['server_name'],
                'game_id' => $server['game_id'],
                'dir_id' => $server['dir_id'],
                'on_recharge' => isset($server['on_recharge']) ? $server['on_recharge'] : 0,
                'use_for_month_card' => isset($server['use_for_month_card']) ? $server['use_for_month_card'] : 0,
                'server_internal_id' => isset($server['server_internal_id']) ? $server['server_internal_id'] : 0,
        );
        if ($server['game_id'] == 46) {
            $params['server_port'] = $server['server_internal_id'];
            $params['server_ip'] =  "s" . $server['server_internal_id'] . "-hg.yeapgame.com/pay";
        }else{
            $params['server_port'] = $server['api_server_port'];
            $params['server_ip'] = $server['api_server_ip'];
        }
        $response = $api->addNewPaymentServer($params);
//        Log::info('addPaymentServer');
//        Log::info(var_export($response, true));
        return $response;
    }

    /**
     * Display the specified resource.
     *
     * @param int $id            
     * @return Response
     */
    public function show($id)
    {
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param int $id            
     * @return Response
     */
    public function edit($id)
    {
        $server = Server::find($id);
        if (! $server)
        {
            App::abort(403);
            exit();
        }
        
        $game = Game::find($server->game_id);
        
        $platform = Platform::find($game->platform_id);
        
        if (! $platform)
        {
            App::abort(403);
            exit();
        }
        
        $response = $this->getPlatformServer($platform, 
                $server->platform_server_id);
        if ($response->http_code == 200)
        {
            $platform_server = $response->body;
            $platform_server->game_name = Game::find($platform_server->game_id)->game_name;
            if (! isset($platform_server->tp_server_id))
            {
                $platform_server->tp_server_id = 0;
            }
            if (! isset($platform_server->zoneid))
            {
                $platform_server->zoneid = 0;
            }
            if (! isset($platform_server->open_server_time))
            {
                $platform_server->open_server_time = '';
            } else
            {
                if ($platform_server->open_server_time > 0)
                {
                    $platform_server->open_server_time = date('Y-m-d H:i:s', 
                            $platform_server->open_server_time);
                }
            }
        } else
        {
            $platform_server = (object) array(
                    'server_track_name' => '',
                    'server_name' => '',
                    'version' => '',
                    'game_path' => '',
                    'source_path' => '',
                    'xload_path' => '',
                    'battle_report_path' => '',
                    'server_ip' => '',
                    'server_port' => '',
                    'server_internal_id' => '',
                    'api_server_ip' => '',
                    'api_server_port' => '',
                    'dir_id' => '',
                    'server_uid' => '',
                    'is_server_on' => '',
                    'game_name' => '',
                    'server_id' => 0,
                    'tp_server_id' => 0,
                    'zoneid' => 0,
                    'open_server_time' => ''
            );
        }
        
        $response = $this->getPaymentServer($platform, 
                $server->platform_server_id);
        if ($response->http_code == 200)
        {
            $payment_server = $response->body;
            $game = Game::find($payment_server->game_id);
            if($game){
                $payment_server->game_name = $game->game_name;
            } else {
                $payment_server->game_name = "";
            }
            //$payment_server->game_name = Game::find($payment_server->game_id)->game_name;
        } else
        {
            $payment_server = (object) array(
                    'server_id' => 0,
                    'server_name' => '',
                    'server_ip' => '',
                    'server_port' => '',
                    'game_id' => '',
                    'dir_id' => '',
                    'on_recharge' => '',
                    'use_for_month_card' => '',
                    'game_name' => ''
            );
        }
        
        $data = array(
                'content' => View::make('servers.edit', 
                        array(
                                'server' => $server,
                                'platform' => $platform,
                                'platform_server' => $platform_server,
                                'payment_server' => $payment_server
                        ))
        );
        return View::make('main', $data);
    }

    public function syncServer($server_id)
    {
        $type = Input::get('type');
        $server = Server::find($server_id);
        $msg = array(
                'code' => Config::get('errorcode.server_edit'),
                'error' => Lang::get('error.server_edit')
        );
        if (! $server)
        {
            return Response::json($msg, 404);
        }
        
        $platform = Game::find($server->game_id)->platform;
        $payment_server = $this->getPaymentServer($platform, 
                $server->platform_server_id);
        $platform_server = $this->getPlatformServer($platform, 
                $server->platform_server_id);
        if ($type == 'platform')
        {
            if (! isset($platform_server->body->server_id))
            {
                $response = $this->addPlatformServer($platform, $server);
                return Response::json($response->body, $response->http_code);
            }
        } else if ($type == 'payment')
        {
            if (isset($platform_server->body->server_id) &&
                     ! isset($payment_server->body->server_id))
            {
                $server = $server->toArray();
                $server['server_id'] = $platform_server->body->server_id;
                $server['dir_id'] = $server['api_dir_id'];
                $response = $this->addPaymentServer($platform, $server);
                return Response::json($response->body, $response->http_code);
            } else
            {
                return Response::json($msg, 403);
            }
        }
    }

    private function getPlatformServer($platform, $platform_server_id)
    {
        $api = PlatformApi::connect($platform->platform_api_url, 
                $platform->api_key, $platform->api_secret_key);
        $response = $api->getPlatformServer($platform_server_id);
        return $response;
    }

    private function getPaymentServer($platform, $platform_server_id)
    {
        $api = PlatformApi::connect($platform->payment_api_url, 
                $platform->api_key, $platform->api_secret_key);
        $response = $api->getPaymentServer($platform_server_id);
        return $response;
    }

    /**
     * Update the specified resource in storage.
     *
     * @param int $id            
     * @return Response
     */
    public function update($id)
    {
        $server = Server::find($id);
        $msg = array(
                'code' => Config::get('errorcode.server_edit'),
                'error' => Lang::get('error.server_edit')
        );
        
        if (! $server)
        {
            return Response::json($msg, 404);
        }
        return $this->editServer($server, $msg);
    }

    private function editServer($server, $msg)
    {
        $rules = array(
                'server_track_name' => 'required',
                'server_name' => 'required',
                'version' => 'required',
                'game_path' => 'required',
                'resource_path' => 'required',
                'xloader_path' => 'required',
                'battle_report_path' => 'required',
                'server_ip' => 'required',
                'server_port' => 'required',
                'is_server_on' => 'required',
                'on_recharge' => 'required',
                'use_for_month_card' => 'required',
                'server_internal_id' => 'required',
                'api_server_ip' => 'required',
                'api_server_port' => 'required',
                'api_dir_id' => 'required'
        );
        
        $game = Game::find($server->game_id);
        
        if (! $game)
        {
            return Response::json($msg, 404);
        }
        
        $validator = Validator::make(Input::all(), $rules);
        
        if ($validator->fails())
        {
            return Response::json($msg, 403);
        } else
        {
            $is_server_on = $server->is_server_on;
            $server->server_track_name = trim(Input::get('server_track_name'));
            $server->server_name = trim(Input::get('server_name'));
            $server->version = trim(Input::get('version'));
            $server->game_path = trim(Input::get('game_path'));
            $server->resource_path = trim(Input::get('resource_path'));
            $server->xloader_path = trim(Input::get('xloader_path'));
            $server->battle_report_path = trim(Input::get('battle_report_path'));
            $server->server_ip = trim(Input::get('server_ip'));
            $server->server_port = trim(Input::get('server_port'));
            $server->is_server_on = (int) Input::get('is_server_on');
            $server->open_server_time = strtotime(
                    Input::get('open_server_time'));
            $server->on_recharge = (int) Input::get('on_recharge');
            $server->use_for_month_card = (int) Input::get('use_for_month_card');
            $server->server_internal_id = trim(Input::get('server_internal_id'));
            $server->api_server_ip = trim(Input::get('api_server_ip'));
            $server->api_server_port = trim(Input::get('api_server_port'));
            $server->api_dir_id = trim(Input::get('api_dir_id'));
            $server->tp_server_id = (int) Input::get('tp_server_id');
            $server->qq_zone_id = (int) Input::get('qq_zone_id');
            $server->match_port = (int) Input::get('match_port');
            
            if ($server->save())
            {
                $platform = $this->updatePlatformServer($server->toArray());
                $payment = $this->updatePaymentServer($server->toArray());
                Log::info(Auth::user()->username.' MODIFY SERVER '.$server->server_track_name.' AT TIME '.date("Y-m-d H:i:s", time()));
                // return Response::json(array('msg' =>
            // Lang::get('basic.edit_success')));
            } else
            {
                return Response::json($msg, 500);
            }
        }
    }

    private function updatePlatformServer($server)
    {
        $platform = Game::find($server['game_id'])->platform;
        $api = PlatformApi::connect($platform->platform_api_url, 
                $platform->api_key, $platform->api_secret_key);
        
        $params = array();
        $replace = array(
                'resource_path' => 'source_path',
                'xloader_path' => 'xload_path',
                'api_dir_id' => 'dir_id',
                'qq_zone_id' => 'zoneid'
        );
        
        foreach ($server as $k => $v)
        {
            if (isset($replace[$k]))
            {
                $params[$replace[$k]] = $v;
            } else
            {
                $params[$k] = $v;
            }
        }
        $params['server_id'] = $server['platform_server_id'];
        $response = $api->updatePlatformServer($params);
//        Log::info('updatePlatformServer');
//        Log::info(var_export($response, true));
        return $response;
    }

    private function updatePaymentServer($server)
    {
        $params = array(
                'server_name' => $server['server_name'],
                //'server_ip' => $server['api_server_ip'],
                //'server_port' => $server['api_server_port'],
                'dir_id' => $server['api_dir_id'],
                'game_id' => Session::get('game_id'),
                'on_recharge' => $server['on_recharge'],
                'use_for_month_card' => $server['use_for_month_card'],
                'server_id' => $server['platform_server_id'],
                'server_internal_id' => isset($server['server_internal_id']) ? $server['server_internal_id'] : '', 
        );

        if ($server['game_id'] == 46) {
            $params['server_port'] = $server['server_internal_id'];
            $params['server_ip'] =  "s" . $server['server_internal_id'] . "-hg.yeapgame.com/pay";
        }else{
            $params['server_port'] = $server['api_server_port'];
            $params['server_ip'] = $server['api_server_ip'];
        }

        $platform = Game::find($server['game_id'])->platform;
        $api = PlatformApi::connect($platform->payment_api_url, 
                $platform->api_key, $platform->api_secret_key);
        $response = $api->updatePaymentServer($params);
//        Log::info('updatePaymentServer');
//        Log::info('updatePaymentServer params:'.var_export($params, true).'<br>AND response:'.var_export($response, true));
        return $response;
    }

    public function openServer($server_id)
    {
        $msg = array(
                'code' => Config::get('errorcode.server_add'),
                'error' => Lang::get('error.server_add')
        );
        
        $server = Server::find($server_id);
        
        if (! $server)
        {
            return Response::json($msg, 404);
        }
        
        $server->server_track_name = str_replace(self::SPEC_FIX, '', 
                $server->server_track_name);
        $server->is_server_on = 1;
        $server->on_recharge = 1;
        $server->use_for_month_card = 0;
        if ($server->save())
        {
            $platform = Game::find($server->game_id)->platform;
            $api = PlatformApi::connect($platform->platform_api_url, 
                    $platform->api_key, $platform->api_secret_key);
            $params = array(
                    'server_id' => $server->platform_server_id,
                    'is_server_on' => 1,
                    'server_track_name' => $server->server_track_name
            );
            $response = $api->updatePlatformServer($params);
            if ($response->http_code != 200)
            {
                return $api->sendResponse();
            }
            $api = PlatformApi::connect($platform->payment_api_url, 
                    $platform->api_key, $platform->api_secret_key);
            $params = array(
                    'server_id' => $server->platform_server_id,
                    'on_recharge' => 1,
                    'use_for_month_card' => 0
            );
            
            $response = $api->updatePaymentServer($params);
            if ($response->http_code != 200)
            {
                return $api->sendResponse();
            }
            Log::info(Auth::user()->username.' OPEN SERVER '.$server->server_track_name.' AT TIME '.date("Y-m-d H:i:s", time()));
            return Response::json($server);
        } else
        {
            $msg['error'] = Lang::get('error.open_server_error');
            return Response::json($msg, 500);
        }
    }

    public function closeServer($server_id)
    {
        $msg = array(
                'code' => Config::get('errorcode.server_add'),
                'error' => Lang::get('error.server_add')
        );
        
        $server = Server::find($server_id);
        
        if (! $server)
        {
            return Response::json($msg, 404);
        }
        
        $server->is_server_on = 0;
        $server->on_recharge = 0;
        $server->use_for_month_card = 0;
        
        if ($server->save())
        {
            $platform = Game::find($server->game_id)->platform;
            $api = PlatformApi::connect($platform->platform_api_url, 
                    $platform->api_key, $platform->api_secret_key);
            $params = array(
                    'server_id' => $server->platform_server_id,
                    'is_server_on' => 0
            );
            $response = $api->updatePlatformServer($params);
            if ($response->http_code != 200)
            {
                return $api->sendResponse();
            }
            $api = PlatformApi::connect($platform->payment_api_url, 
                    $platform->api_key, $platform->api_secret_key);
            $params = array(
                    'server_id' => $server->platform_server_id,
                    'on_recharge' => 0,
                    'use_for_month_card' => 0
            );
            $response = $api->updatePaymentServer($params);
            if ($response->http_code != 200)
            {
                return $api->sendResponse();
            }
            Log::info(Auth::user()->username.' CLOSE SERVER '.$server->server_track_name.' AT TIME '.date("Y-m-d H:i:s", time()));
            return Response::json($server);
        } else
        {
            return Response::json($msg, 500);
        }
    }

    public function initGameServer($server_id)
    {
        $msg = array(
                'code' => Config::get('errorcode.server_add'),
                'error' => Lang::get('error.server_add')
        );
        
        $server = Server::find($server_id);
        
        if (! $server)
        {
            return Response::json($msg, 404);
        }
        $api = GameServerApi::connect($server->api_server_ip, 
                $server->api_server_port, $server->api_dir_id);
        $response = $api->initGameServer();
        if (isset($response->error))
        {
            $response->error = Lang::get('error.init_server_error');
            return Response::json($response, 500);
        }
        Log::info(Auth::user()->username.' RESET SERVER '.$server->server_track_name.' AT TIME '.date("Y-m-d H:i:s", time()));
        return $api->sendResponse();
    }

    public function openServerByScript($server_id)
    {
        $server = Server::find($server_id);
        
        if ($server)
        {
            $game = Game::find($server->game_id);
            $api = SlaveApi::connect($game->eb_api_url, $game->eb_api_key, 
                    $game->eb_api_secret_key);
            $api->runOpenServerScript($server->game_id, 
                    $server->server_internal_id, $server->server_ip, 
                    $server->api_dir_id, $server->open_server_time);
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param int $id            
     * @return Response
     */
    public function destroy($id)
    {
    }
    public function testStore()
    {
        
        $game_id = Session::get('game_id');
        $game = Game::find($game_id);

        $server = new Server();
        $server->open_server_time = strtotime(time());
        $server->server_track_name = 'T4tsdfasd';
        $server->server_name = 'T4fasda';
        $server->game_path = '';
        $server->version = 'cres';
        $server->resource_path = '';
        $server->xloader_path = '';
        $server->battle_report_path = '';
        $server->server_ip = '127.0.0.1';
        $server->server_port = '809';
        $server->server_internal_id = '1';
        $server->api_server_ip = '127.0.0.1';
        $server->api_server_port = '80';
        $server->api_dir_id = '3';
        $server->game_id = $game_id;
        $server->match_port = '30';


        $platform = Game::find($server->game_id)->platform;
        $platform_id = $platform->platform_id;
        $region_code = Platform::find($platform_id)->region->region_code;
        $platform_id = str_pad($platform_id, 3, '0', STR_PAD_LEFT);
        $game_id = str_pad($server->game_id, 3, '0', STR_PAD_LEFT);
        $server_id = str_pad($server->server_internal_id, 4, '0', 
                STR_PAD_LEFT);
        $server_uid = $region_code . $platform_id . $game_id . $server_id;
        $server->server_uid = $server_uid;
        
        $response = $this->addPlatformServer($platform, $server);
        var_dump($response);
        /*$api = PlatformApi::connect($platform->platform_api_url, 
                $platform->api_key, $platform->api_secret_key);
        $method = '/home_api/new_server';
        $params = array();
        
        $res = $api->test_home_api($method,$params);
        var_dump($res);*/
    }

    public function UpdateServersIndex(){
        $servers = $this->getUnionServers($no_skip=1);
        if(!in_array(Session::get('game_id'), $this->world_edition_list)){
            foreach ($servers as $key => $value) {
                if('5016' == $value->server_internal_id){
                    unset($servers[$key]);
                }
            }
        }
        $res = Server::where('game_id', Session::get('game_id'))->selectRaw("distinct version")->get();
        $show_type_s = 0;
        $game_id = Session::get('game_id');
        $game = Game::find($game_id);
        if('poker' == $game->game_code){
            $show_type_s = 1;
        }

        $data = array(
            'content' => View::make('servers.updateservers', array(
                'servers' => $servers,
                'res' => $res,
                'show_type_s' => $show_type_s,
                'type_s_types' => $this->type_s_types,
                )
            )
        );
        return View::make('main', $data);
    }

    public function UpdateServers(){
        $update_type = Input::get('update_type');
        if(!$update_type){
            return Response::json(array('error'=> Lang::get('serverapi.select_update_type')), 403);
        }

        $submittype = Input::get('submittype');

        $dir = public_path().'/updateservers/';

        if('1' == $update_type){
            $startdir = $dir.'front_start/';
            $dir .= 'front/';
        }elseif('2' == $update_type){
            $startdir = $dir.'backend_start/';
            $dir .= 'backend/';
        }elseif('3' == $update_type){
            $startdir = $dir.'upload_start/';
            $dir .= 'upload/';
        }

        $game_id = Session::get('game_id');
        $game = Game::find($game_id);
        $dir .= $game->game_code.$game_id;

        if('check' == $submittype){
            return $this->checkUpdateProcess($update_type, $dir);
        }else{
            $result = $this->checkUpdateProcess($update_type, $dir, 1);
            $result[-3] = array('msg' => "\t");
            $result[-2] = array('msg' => '上次执行结果为（上次更服成功本次提交方有效）：');
            $result[-1] = array('msg' => '设置成功，请等待定时程序寻入执行。');
        }

        if(!in_array($game->game_code, array('flsg','nszj','poker'))){
            return Response::json(array('error'=> 'Not a supported game!'), 403);
        }

        if(is_dir($dir)){
        }else{
            mkdir($dir, 0777, 1);
        }

        if(is_dir($startdir)){
        }else{
            mkdir($startdir, 0777, 1);
        }

        $startdir .= $game->game_code.$game_id;

        $server_ids = Input::get('server_ids');
        $reses = Input::get('reses');
        $language = Input::get('language');

        if('1' == $update_type){
            $towrite = $game->game_code."\t".$game_id."\n";
            if(count($reses)){
                foreach ($reses as $res) {
                    $towrite .= $res."\t";
                }
            }else{
                return Response::json(array('error'=> Lang::get('serverapi.select_reses')), 403);
            }
            $towrite .= "\n".time();
            file_put_contents($startdir, $towrite);
        }elseif('2' == $update_type){
            if(empty($server_ids)){
                return Response::json(array('error'=> Lang::get('serverapi.select_game_server')), 403);
            }
            if(in_array(0, $server_ids)){
                $towrite = $game->game_code."\t".$game_id."\n".'1'."\n".time()."\n";
                file_put_contents($startdir, $towrite);
            }else{
                $towrite = $game->game_code."\t".$game_id."\n".'2'."\n".time()."\n"."data\n";
                file_put_contents($startdir, $towrite);
                foreach ($server_ids as $server_id) {
                    $server = Server::find($server_id);
                    $towrite = $server->server_ip."\t".$server->server_port."\n";
                    file_put_contents($startdir, $towrite, FILE_APPEND);
                }
            }  
        }elseif('3' == $update_type){
            if(empty($language)){
                return Response::json(array('error'=> Lang::get('serverapi.select_language')), 403);
            }else{
                $towrite = $game->game_code."\t".$game_id."\n".time()."\n"."data\n";
                file_put_contents($startdir, $towrite);
                foreach ($language as $single_language) {
                    $towrite = $this->type_s_types[$single_language]."\t";
                    file_put_contents($startdir, $towrite, FILE_APPEND);
                }
            }  
        }

        //文件上传成功
        if(file_exists($startdir)){
            return Response::json($result);
        }else{
            return Response::json(array('error'=> '设置失败'), 403);
        }
    }

    private function checkUpdateProcess($update_type, $dir, $update=0){    //查询更新进度，调用脚本到游戏服务器去获取日志
        if('1' == $update_type){
            $dirname = 'front';
        }elseif('2' == $update_type){
            $dirname = 'backend';
        }elseif('3' == $update_type){
            $dirname = 'upload';
        }

        $game_id = Session::get('game_id');
        $game = Game::find($game_id);
        if('3' == $update_type){
            system("bash -x ".public_path().'/updateservers/'."postCheck.sh {$game->game_code}"."{$game_id}", $output);
        }else{
            system("bash -x ".public_path().'/updateservers/'."checkResult.sh {$dirname} {$game->game_code}"."{$game_id}", $output);
        }

        if(0 == $output){
            if(file_exists($dir.'/logmsg.txt')){
                $result = array();
                if(file_exists($dir.'/tip')){
                    $result[] = array('msg' => file_get_contents($dir.'/tip'));
                    $result[] = array('msg' => '');
                }
                if(!$update){
                    $handle = fopen($dir.'/logmsg.txt', 'r');
                    while($line = fgets($handle)) {
                        $result[] = array('msg' => $line);
                    }
                }
                if($update){
                    return $result;
                }
                return Response::json($result);
            }else{
                if($update){
                    return array();
                }
                return Response::json(array('error'=> 'File Not Exists'), 403);
            }
        }else{
            if($update){
                return array();
            }
            return Response::json(array('error'=> 'Check Script Exec Error'), 403);
        }
    }
}
