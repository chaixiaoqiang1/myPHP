<?php 

class AnnounceController extends \BaseController {

    const  UPDATE_TYPE = 27;
    public function index()
    {
        
        //$servers = Server::currentGameServers()->get();
        $servers = $this->getUnionServers();
        $game_id = Session::get('game_id');
        $position = array(
            '1' => Lang::get('serverapi.announce_pos_center'),
            '2' => Lang::get('serverapi.announce_pos_chat'),
            '3' => Lang::get('serverapi.announce_pos_all')
        );
        $data = array(
            'content' => View::make('serverapi.flsg_nszj.announce.index', array(
                'servers' => $servers,
                'pos' => $position,
                'game_id' => $game_id
            )),
        );
        return View::make('main', $data);
    }

    public function send()
    {
        $msg = array(
            'code' => Config::get('errorcode.unknow'),
            'error' => Lang::get('error.basic_input_error'),
        );
        $rules = array(
            'pos' => 'required',
            'content' => 'required',
        );
        $validator = Validator::make(Input::all(), $rules);
        if ($validator->fails()) {
            return Response::json($msg, 403);
        }

        $server_ids = Input::get('server_id');
        if('0' == count($server_ids)){
            return Response::json(array('error'=>'请选择服务器'), 403);
        }
        $result = array();
        
        $game_id = Session::get('game_id');
        $game_arr = $this->getGameId();
        $area_id = (int)Input::get('area_id');//针对台湾和英文世界服分国旗
        foreach ($server_ids as $server_id) {
            $server = Server::find($server_id);
            if (!$server) {
                $msg['error'] = Lang::get('error.basic_not_found');
                return Response::json($msg, 404);
            }
            $days = 0;
            $pos = (int)Input::get('pos');
            $interval_type = 2;
            $content = trim(Input::get('content'));             
            
            $interval = 1;
            //Log::info(var_export(Input::all(), true));
            if($game_id == 11) {
                $api = PokerGameServerApi::connect($server->api_server_ip, $server->api_server_port);
                $response = $api->announceSend($content, 'id_id');
            } elseif($game_id == 59 || $game_id == 60 || $game_id == 61 || $game_id == 62 || $game_id == 63 || $game_id == 64)
            {
                if(($game_id == 59 || $game_id == 63) && $area_id != 0){//针对台湾和英文分国旗
                        $game_id = $area_id;
                }
                $api = GameServerApi::connect($server->api_server_ip, $server->api_server_port, $server->api_dir_id);       
                $response = $api->announce($days, $interval_type, $pos, $interval, $content, $game_id);
            }else
            {
                $api = GameServerApi::connect($server->api_server_ip, $server->api_server_port, $server->api_dir_id);
                $response = $api->announce($days, $interval_type, $pos, $interval, $content);
            }
            if(isset($response->result) && $response->result == 'OK')
            {
                // Cache::add('promotion-close-time', $end_time, 100000);
                $result[] = array(
                        'msg' => ' ( ' . $server->server_name . ' ) : ' . $response->result . "\n",
                        'status' => 'ok'
                );
            } else
            {
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


    //静默玩家发言
    //flsg
    public function stopAnnounceIndex()
    {
        $servers = $this->getUnionServers();
        //$servers = Server::currentGameServers()->get();
        $data = array(
            'content' => View::make('serverapi.flsg_nszj.announce.stop', array('servers'=>$servers))
        );
        return View::make('main', $data);
    }

    public function stopAnnounceData()
    {
        $msg = array(
            'code' => Config::get('errorcode.unknow'),
            //'error' => Lang::get('error.basic_input_error')
        );
        $rules = array(
            'level' => 'required',
        );
        $validator = Validator::make(Input::all(), $rules);
        if ($validator->fails()) {
            $msg['error'] = Lang::get('error.basic_input_error');
            return Response::json($msg, 403);
        }
        $server_ids = Input::get('server_id');
        $level = trim(Input::get('level'));
        $type = Input::get('action');
        $game_id = Session::get('game_id');
       // $game_id = 4;//本地测试
        $game = Game::find($game_id);
        //var_dump($type);die();
        foreach ($server_ids as $server_id) {
            $server = Server::find($server_id);
            if (!$server) {
                $msg['error'] = Lang::get('error.server_not_found');
                return Response::json($msg, 403);
            }
            if($game_id!=$server->game_id){
                return Response::json(array('error'=>'please check the current platform and servers!'), 403);
            }
            $api = GameServerApi::connect($server->api_server_ip, $server->api_server_port, $server->api_dir_id);
            $response = $api->stopAnnounce($type,$level,$game->game_code);
            Log::info(var_export($response,true));
            if(isset($response->result) && $response->result == 'OK'){
                $result[] = array(
                    'result' => '(' . $server->server_name . ')' . $response->result . ($type == "true" ? '-----开启禁言' : '----关闭禁言').'----Level :---'.$level. "\n",
                    'status' => 'ok'
                    );
            }else{
                $result[] = array(
                    'result' => '(' . $server->server_name . ')' . $response->result . "\n",
                    'status' => 'error'
                    );
            }

            
        }
        $msg = array(
                'result' => $result
            );
        return Response::json($msg);
    }

        public function stopAnnounceLookup(){
        $msg = array(
            'code' => Config::get('errorcode.unknow'),
            );

        $server_ids = Input::get('server_id');
        $game_id = Session::get('game_id');
        //$game_id = 4;//本地测试
        $game = Game::find($game_id);

        foreach($server_ids as $server_id){
            $server = Server::find($server_id);
            if(!$server){
                $msg['error'] = Lang::get('error.server_not_found');
                return Response::json($msg,403);
            }
            $api = GameServerApi::connect($server->api_server_ip,$server->api_server_port,$server->api_dir_id);
            $response = $api->lookupAnnounce($game->game_code);
           Log::info(var_export($response,true));
            if(isset($response->active) ){
                //$body = $response->body;
                $result[] = array(
                    'result' => '(' . $server->server_name . ')' . (($response->active == 1)? '----开启':'----解除'). '----禁言  Level--'.$response->level .  "\n",
                    'status' => 'ok'
                    );
            }else{
                $result[] = array(
                    'result' => '(' . $server->server_name . ')' . 'error'. "\n",
                    'status' => 'error'
                    );
            }
        } 
        
        return Response::json($result);    
    }

    //nszi
    public function stopAnnounceIndexNV(){
        $servers = $this->getUnionServers();
        $data = array(
            'content' => view::make('serverapi.flsg_nszj.announce.nvstop', array('servers' => $servers))
            );
        return View::make('main', $data);
    }
    public function stopAnnounceDataNV(){
        $server_ids = Input::get('server_id');
        $game_id = Session::get('game_id');
        //$game_id = 4;//本地测试
        $game = Game::find($game_id);
        if(is_null(Input::get('level'))){
            return Response::json(array('error'=>'Please input level.'), 403);
        }
            $level = trim(Input::get('level'));
        $type = Input::get('action');
        foreach($server_ids as $server_id){
            $server = Server::find($server_id);
            if(!$server){
                $msg['error'] = Lang::get('error.server_not_found');
                return Response::json($msg,403);
            }
            $api = GameServerApi::connect($server->api_server_ip,$server->api_server_port,$server->api_dir_id);
            $response = $api->stopAnnounce($type, $level, $game->game_code);
            // var_dump($response);die();
            if(isset($response->result) && $response->result == 'OK'){
                $result[] = array(
                    'result' => '(' . $server->server_name . ')' . $response->result . ($type == "true" ? '-----开启禁言' : '----关闭禁言').'----Level :---'.$level. "\n",
                    'status' => 'ok'
                    );
            }else{
                $result[] = array(
                    'result' => '(' . $server->server_name . ')' . $response->result . "\n",
                    'status' => 'error'
                    );
            }
        }
        $msg = array(
                'result' => $result
            );
        return Response::json($msg);
    }

    public function stopAnnounceLookupNV(){
        $msg = array(
            'code' => Config::get('errorcode.unknow'),
            );

        $server_ids = Input::get('server_id');
        $game = Game::find(Session::get('game_id'));

        foreach($server_ids as $server_id){
            $server = Server::find($server_id);
            if(!$server){
                $msg['error'] = Lang::get('error.server_not_found');
                return Response::json($msg,403);
            }
            $api = GameServerApi::connect($server->api_server_ip,$server->api_server_port,$server->api_dir_id);
            $response = $api->lookupAnnounce($game->game_code);
            // var_dump($response);die();
            if(isset($response->active) ){
                //$body = $response->body;
                $result[] = array(
                    'result' => '(' . $server->server_name . ')' . (($response->active == 1)? '----开启':'----解除'). '----禁言  Level--'.$response->level .  "\n",
                    'status' => 'ok'
                    );
            }else{
                $result[] = array(
                    'result' => '(' . $server->server_name . ')' . $response->error. "\n",
                    'status' => 'error'
                    );
            }
        }
        return Response::json($result);        
    }

    public function noticeIndex(){
            $servers = $this->getUnionServers();
            $game_id = Session::get('game_id');
            $position = array(
                '1' => Lang::get('serverapi.announce_pos_center'),
                '2' => Lang::get('serverapi.announce_pos_chat'),
                '3' => Lang::get('serverapi.announce_pos_all')
            );
            $data = array(
                'content' => View::make('serverapi.flsg_nszj.notice.index',array(
                    'servers' => $servers,
                    'pos' => $position,
                    'game_id' => $game_id
                    ))
                );
            return View::make('main',$data);
    }

    public function stopNoticeIndex(){
        $servers = $this->getUnionServers();
        $data = array(
            'content' => View::make('serverapi.flsg_nszj.notice.stop',array(
                'servers' => $servers
                ))
            );
        return View::make('main',$data);
    }
    public function stopNoticeIndex_nv(){
        $servers = $this->getUnionServers();
        $data = array(
            'content' => View::make('serverapi.flsg_nszj.notice.stop_nv',array(
                'servers' => $servers
                ))
            );
        return View::make('main',$data);
    }

    public function noticeData(){

            $msg = array(
                'code' => Config::get('errorcode.unknow'),
                'error' => Config::get('error.basic_input_error')
                );
            $rules = array(
               // 'days' => 'required',
                'content' => 'required'
                );
            $validate = Validator::make(Input::all(),$rules);
            if($validate->fails()){
                return Response::json($msg,403);
            }

            $server_ids = Input::get('server_id');
            if(!$server_ids){
                return Response::json(array('error'=>'请选择服务器'), 403);
            }
            $result = array();

            $game_id = Session::get('game_id');
            $game_arr = $this->getGameId();
            $interval = trim(Input::get('cycle_time'));
            $area_id = (int)Input::get('area_id');//针对台湾和英文世界服分国旗
            if ($interval < 20) {
                $msg['error'] = Lang::get('error.time_set_wrong');
                return Response::json($msg, 403);
            }
            foreach ($server_ids as $server_id) {
                $server = Server::find($server_id);
                if(!$server){
                    $msg['error'] = Lang::get('error.basic_not_found');
                    return Response::json($msg,404);
                }
                $pos = (int)Input::get('pos');
                $interval_type = (int)5;
                $content = trim(Input::get('content'));
                $start_time = strtotime(Input::get('start_time'));
                $end_time = strtotime(Input::get('end_time'));
                $days = floatval($end_time-$start_time)/(3600*24);
                $interval = trim(Input::get('cycle_time'));
                $interval = intval($interval)*60;
                // var_dump($interval);die();
                $api = GameServerApi::connect($server->api_server_ip,$server->api_server_port,$server->api_dir_id);
                if(in_array($game_id, $this->world_edition_list)) {
                    if(($game_id == 59 || $game_id == 63) && $area_id != 0){//针对台湾和英文分国旗
                        $response = $api->notice($days, $interval_type, $pos, $interval, $start_time, $content, $area_id);
                    }else{
                        $response = $api->notice($days, $interval_type, $pos, $interval, $start_time, $content, $game_id);
                    }
                    
                }else {
                    $response = $api->notice($days, $interval_type, $pos, $interval, $start_time, $content);
                }
                if(isset($response->result) && $response->result == 'OK'){
                    $result[] = array(
                        'msg' => '(' . $server->server_name . '):' . $response->result . "\n",
                        'status' => 'ok'
                        );
                
                }else{
                    $result[] = array(
                        'msg' => '(' . $server->server_name . '):' . 'error' . "\n",
                        'status' => 'error'
                        );
                }
            }

            return Response::json($result);

    }

    public function lookupNotice(){
        $server_id = Input::get('server_id');
        // foreach ($server_ids as $server_id) {
            $server = Server::find($server_id);
            if(!$server){
                $msg = array(
                    'error' => Lang::get('error.server_not_found')
                    );
                return Response::json($msg,403);
            }
            $api = GameServerApi::connect($server->api_server_ip,$server->api_server_port,$server->api_dir_id);
            $response = $api->loadNotice();
              // var_dump($response);die();
            if(isset($response->bulletins)){
                $bulletins = $response->bulletins;   
            }else{
                $msg = array(
                    'result' => '该服务器未查询到公告'
                    );
                return Response::json($msg,404);
            }
              // var_dump($bulletins);die();
        // }
            $data = array();
            foreach ($bulletins as $key => $value) {
                $data[] = array(
                    'BulletinID' => isset($value->BulletinID) ? $value->BulletinID : 0,
                    'content_txt' => isset($value->content_txt) ? $value->content_txt : '',
                    'interval' => isset($value->interval) ? ($value->interval/60) : 0,
                    'StartTime' => isset($value->start_time) ? date("Y-m-d H:i:s",$value->CreateTime):'',
                    'ExpirationTime' => isset($value->ExpirationTime)? date("Y-m-d H:i:s",$value->ExpirationTime):''
                    );
            }
            // var_dump($data);die();
            // // $data = json_encode($data);
            // var_dump($data);die();
            return Response::json($data);
    }

    public function stopNotice(){
        $msg = array(
            'code' => Config::get('errorcode.unknow'),
            );
        $rules = array('tid' => 'required');
        $validator = Validator::make(Input::all(),$rules);
        if($validator->fails()){
            $msg['error'] = Lang::get('error.basic_input_error');
            return Response::json($msg,403);
        }
        $bulletin_id = intval(Input::get('tid'));
        $server_id = Input::get('server_id');
            $server = Server::find($server_id);
            if(!$server){
                $msg = array(
                    'error' => Lang::get('error.server_not_found')
                    );
                return Response::json($msg,403);
            }
            $api = GameServerApi::connect($server->api_server_ip,$server->api_server_port,$server->api_dir_id);
            $game_id = Session::get('game_id');
            $game = Game::find($game_id);
            $response = $api->stopNotice($game->game_code, $bulletin_id);
            if($response->result=="OK"){
                $result = array(
                    'msg' => Lang::get('amount.delete_success'),
                    'res' => 'OK'
                    );
            }else{
                $result = array(
                    'msg' => Lang::get('amount.delete_failed'),
                    'res' => 'error'
                    );
            }
            return Response::json($result);
            // var_dump($response);die();
    }
    public function stopNotice_nv(){
        $msg = array(
            'code' => Config::get('errorcode.unknow'),
            );
        $rules = array('tid' => 'required');
        $validator = Validator::make(Input::all(),$rules);
        if($validator->fails()){
            $msg['error'] = Lang::get('error.basic_input_error');
            return Response::json($msg,403);
        }
        $bulletin_id = intval(Input::get('tid'));
        $server_id = Input::get('server_id');
            $server = Server::find($server_id);
            if(!$server){
                $msg = array(
                    'error' => Lang::get('error.server_not_found')
                    );
                return Response::json($msg,403);
            }
            $api = GameServerApi::connect($server->api_server_ip,$server->api_server_port,$server->api_dir_id);
            $response = $api->stopNotice_nv($bulletin_id);

            //Log::info(var_export($response, true));
            if($response->result=="OK"){
                $result = array(
                    'msg' => Lang::get('amount.delete_success'),
                    'res' => 'OK'
                    );
            }else{
                $result = array(
                    'msg' => Lang::get('amount.delete_failed'),
                    'res' => 'error'
                    );
            }
            return Response::json($result);
            
    }


   public function getServers()
    {
        $game_id = Session::get('game_id');
        $game = Game::find($game_id);
        $api = SlaveApi::connect($game->eb_api_url, $game->eb_api_key, $game->eb_api_secret_key);
        $response = $api->getServers($game_id);
        if (!empty($response->body)) {
            $server = $response->body;
            $servers = array();
            foreach ($server as $key => $value) {
                if ($value->game_id == $game_id) {
                    $servers[] = $value;
                }
            }
        }else{
            App::abort(404);
            return;
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

    public function updateNoticeIndex()
    {
        $server = $this->getUnionServers();
        $data = array(
            'content' => View::make('serverapi.flsg_nszj.announce.update', array('server' => $server))
        );
        return View::make('main', $data);
    }

    public function updateNoticeSend()
    {
        $msg = array(
            'code' => Config::get('errorcode.unknow'),
            'error' => ''
        );
        $rules = array(
            'server_id' => 'required',
            'notice' => 'required',
            'notice_link' => 'required',
            'notice_head' => 'required',
        );
        $validator = Validator::make(Input::all(), $rules);
        if ($validator->fails()) {
            $msg['error'] = Lang::get('error.basic_input_error');
            return Response::json($msg, 403);
        }
        $open_time = intval(strtotime(trim(Input::get('open_time'))));
        $close_time = intval(strtotime(trim(Input::get('close_time'))));
        $notice = Input::get('notice');
        $notice =  addslashes($notice);
        
        $notice_link = Input::get('notice_link');
        $notice_head  = Input::get('notice_head');
        $server_ids = Input::get('server_id');
        $result = array();
        foreach ($server_ids as $key => $server_id) {
            $server = Server::find($server_id);
            if (!$server) {
                $msg['error'] = Lang::get('error.basic_not_found');
                return Response::json($msg, 403);
            }
            $data = array(
                // 'open_time' => $open_time,
                // 'close_time' => $close_time,
                'notice' => $notice,
                'notice_link' => $notice_link,
                'notice_head' => $notice_head,
                //'type' => self::UPDATE_TYPE
            );
            $api = GameServerApi::connect($server->api_server_ip, $server->api_server_port, $server->api_dir_id);
            $response = $api->updateNotice($data);
            //var_dump($response);die();
            if ($response->result == "OK") {
                $result[] = array(
                    'msg' => ($server->server_name) . '--' . $response->result,
                    'status' => 'OK'
                );

            }
            else{
                $result[] = array(
                    'msg' => ($server->server_name) . '--' . $response->error,
                    'status' => 'OK'
                );
            }
            return Response::json($result);
        }
    } 

    public function welfareIndex()
    {
        $servers = $this->getUnionServers();
        $data = array(
            'content' => View::make('serverapi.flsg_nszj.announce.welfare_index', array(
                'servers' => $servers,
            )),
        );
        return View::make('main', $data);
    }

    public function welfareSend()
    {
        $msg = array(
            'code' => Config::get('errorcode.unknow'),
            'error' => Lang::get('error.basic_input_error'),
        );
        $is_look = Input::get('is_look');

        $server_ids = Input::get('server_id');
        if(empty($server_ids)){
            return Response::json(array('error'=>'Did you select a server?'), 403);
        }
        $result = array();
        
        $game_id = Session::get('game_id');
        $game = Game::find($game_id);

        if(0 == $is_look){
            $contents = Input::get('content');
            $urls = Input::get('url');
            $version_contents = Input::get('version_content');
            $version_urls = Input::get('version_url');
            $leadings = Input::get('font_leading');
            $sizes = Input::get('font_size');
            $colors = Input::get('font_color');
            $version_leadings = Input::get('version_font_leading');
            $version_sizes = Input::get('version_font_size');
            $version_colors = Input::get('version_font_color');
            $announce_type = Input::get('announce_type');

            $font_str = '';
            $version_font_str = '';
            for($i=0; $i<count($contents);$i++){
                if(1 == $colors[$i]){
                    $colors[$i] = "#ff0000";
                }elseif(2 == $colors[$i]){
                    $colors[$i] = "#0000ff";
                }
                if($contents[$i] || $urls[$i]){
                    $font_str = $font_str.'<textformat leading='.'"'.$leadings[$i].'"'.'><font';
                    if($sizes[$i]){
                        $font_str = $font_str.' size='.'"'.$sizes[$i].'"';
                    }
                    if($colors[$i]){
                        $font_str = $font_str.' color='.'"'.$colors[$i].'"';
                    }
                    $font_str= $font_str.'>';
                    if($contents[$i]){
                       $font_str = $font_str.$contents[$i]; 
                    }
                    if($urls[$i]){
                        $font_str = $font_str.'<a href='.'"'.$urls[$i].'"'.' target='.'"_blank"'.'>'.$urls[$i].'</a>';
                    }
                    $font_str = $font_str.'</font></textformat>'; 
                }
                
            }
            for($i=0; $i<count($version_contents);$i++){
                if(1 == $version_colors[$i]){
                    $version_colors[$i] = "#ff0000";
                }elseif(2 == $version_colors[$i]){
                    $version_colors[$i] = "#0000ff"; 
                }
                if($version_contents[$i] || $version_urls[$i]){
                    $version_font_str = $version_font_str.'<textformat leading='.'"'.$version_leadings[$i].'"'.'><font';
                    if($version_sizes[$i]){
                        $version_font_str = $version_font_str.' size='.'"'.$version_sizes[$i].'"';
                    }
                    if($version_colors[$i]){
                        $version_font_str = $version_font_str.' color='.'"'.$version_colors[$i].'"';
                    }
                    $version_font_str= $version_font_str.'>';
                    if($version_contents[$i]){
                       $version_font_str = $version_font_str.$version_contents[$i]; 
                    }
                    if($version_urls[$i]){
                        $version_font_str = $version_font_str.'<a href='.'"'.$version_urls[$i].'"'.' target='.'"_blank"'.'>'.$version_urls[$i].'</a>';
                    }
                    $version_font_str = $version_font_str.'</font></textformat>'; 
                }
            }
            
            foreach ($server_ids as $server_id) {
                $server = Server::find($server_id);
                if (!$server) {
                    $result[] = array(
                            'msg' => ' ( ' . $server_id . ' ) : ' . 'server_not_found' . "\n",
                            'status' => 'error'
                    );
                    continue;
                }          
                $api = GameServerApi::connect($server->api_server_ip, $server->api_server_port, $server->api_dir_id);
                if(0 != $announce_type){
                    $look_res = $api->welfareAnnounceLook($game->game_code);
                    if(2== $announce_type){
                        $font_str = isset($look_res->info) ? $look_res->info : '';
                    }elseif(1== $announce_type){
                        $version_font_str = isset($look_res->release) ? $look_res->release : '';
                    }
                } 

                $response = $api->welfareAnnounce($font_str, $version_font_str, $game->game_code);
                if(isset($response->info) && isset($response->release))
                {
                    $result[] = array(
                            'msg' => ' ( ' . $server->server_name . ' ) : ' . 'ok' . "\n",
                            'status' => 'ok'
                    );
                } else
                {
                    Log::info('welfare test:'.var_export($response,true));
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
        }elseif(1 == $is_look){//查看
            foreach ($server_ids as $server_id) {
                $server = Server::find($server_id);
                if (!$server) {
                    $result[] = array(
                            'msg' => ' ( ' . $server_id . ' ) : ' . 'server_not_found' . "\n",
                            'status' => 'error'
                    );
                    continue;
                }           
                $api = GameServerApi::connect($server->api_server_ip, $server->api_server_port, $server->api_dir_id);
                $response = $api->welfareAnnounceLook($game->game_code);
                if(isset($response->info) && isset($response->release))
                {
                    $result[] = array(
                            'msg' => ' ( ' . $server->server_name . ' ) 活动公告：' . strip_tags($response->info) . '----版本公告：' . strip_tags($response->release)."\n",
                            'status' => 'ok'
                    );
                } else
                {
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
    }
}