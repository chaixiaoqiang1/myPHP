<?php 

class SlaveApiSqlController extends \BaseController
{    

    public function inputSqlIndex(){    //手动sql查询
        $data = array(
                'content' => View::make('slaveapi.user.inputsql', array())
        );
        return View::make('main', $data); 
    }

    public function inputSqlDeal($sql = '', $not_show = '', $database = ''){
        if('1' == Input::get('dealsqls')){
            return $this->getsqls();
        }elseif('2' == Input::get('dealsqls')){
            return $this->addsqls($sql = Input::get('sql'));
        }else{
            if(!$sql){
                $sql = Input::get('sql');
            }
            if('' == $sql){
                return Response::json(array('error'=>'请输入sql语句'), 403);
            }
            //接下来的判断用来限制sql语句中只有select操作并且没有分号
            if(is_numeric(strpos(strtolower($sql), 'insert')) || is_numeric(strpos(strtolower($sql), 'delete')) || is_numeric(strpos(strtolower($sql), ';')) || is_numeric(strpos(strtolower($sql), 'alter')) || is_numeric(strpos(strtolower($sql), 'update')) || is_numeric(strpos(strtolower($sql), 'drop'))){
                return Response::json(array('error'=>'只可做查询操作或单次操作'), 403);
            }
            if(0 !== strpos(strtolower($sql), 'select') && 0 !== strpos(strtolower($sql), 'explain') && 0 !== strpos(strtolower($sql), 'desc')){
                return Response::json(array('error'=>'只可做查询操作'), 403);
            }
            $game_id = Session::get('game_id');
            $platform_id = Session::get('platform_id');
            $game = Game::find($game_id);

            $api = SlaveApi::connect($game->eb_api_url, $game->eb_api_key, $game->eb_api_secret_key);
            $time_start_s = time();
            $time_start_ms = microtime();
            if(!$database){
                $database = Input::get('database');
            }
            if('eastblue' == $database){    //查询eastblue本地数据库
                $msg = array(
                    'code' => Config::get('error.unknow'),
                    'error' => Lang::get('error.basic_not_found'),
                );
                try {
                    $tmp_result = DB::select($sql);
                } catch (Exception $e) {
                    $tmp_result = array();
                    if(is_numeric(stripos($e,'in /var/www/eastblue/'))){
                        $msg['error'] = substr($e,0,stripos($e,'in /var/www/eastblue/'));
                    }else{
                        $msg['error'] = $e;
                    }
                }
                if($tmp_result){    //其实访问的是本地的数据库，但是懒得改后面的判断了，因此把数据格式模拟成http返回值类型
                    $result = new stdClass();
                    $result->http_code = 200;
                    $result->body = $tmp_result;
                }else{
                    $result = new stdClass();
                    $result->http_code = 403;
                    $returnmsg = new stdClass();
                    $returnmsg->error = $msg['error'];
                    $result->body = $returnmsg;
                }
            }else{  //查询slave端的数据库
                $result = $api->getsqlresult($game_id, $platform_id, $sql, $database, Input::get('ifdownload'));
            }
            $time_end_s = time();
            $time_end_ms = microtime();
            $time_use = ($time_end_s - $time_start_s) + ($time_end_ms - $time_start_ms);
            if('200' == $result->http_code){
                $standresult = $this->standresult($result);
                $keywords = $standresult['keywords'];
                $response = $standresult['sqlresult'];
                $data = array(
                    'info'  =>  '花费了'.$time_use.'秒，返回了'.count($response).'条记录',
                    'keywords'  =>  $keywords,
                    'sqlresult' => (array)$response,
                );
                if($not_show){  //如果显式说明不需要显式，会返回一个完整的准备返回给页面的数据，即上面看到的$data
                    return $data;
                }
                if(Input::get('ifdownload')){
                    $now = $this->inputSqlDownload($data['keywords'], $data['sqlresult']);
                    unset($data);
                    $data = array(
                        'info'  =>  '花费了'.$time_use.'秒，返回了'.count($response).'条记录',
                        'keywords'  =>  array(),
                        'sqlresult' => array(),
                        );
                    $data['now'] = $now;
                }
                return Response::json($data);
            }elseif('500' == $result->http_code){
                return Response::json(array('error'=>'异常的返回，有报错或请求的数据量过大'), $result->http_code);
            }elseif('403' == $result->http_code){
                return Response::json(array('error'=> $result->body->error), 403);
            }else{
                return $api->sendResponse();
            }
        }
    }

    public function standresult($result){   //标准化查询结果，这里的标准化指的是方便页面显示
        $keywords = array();    //用来存储所有的字段名
        $body = $result->body;
        foreach ($body as $value) {
            $count = 0;
            foreach ($value as $k => $v) {
                $keywords['key'.$count] = $k;
                $count ++;
            }
            break;
        }
        $response = array();
        foreach ($body as $value) {
            $count = 0;
            $tmp = array();
            foreach ($value as $k => &$v) {
                if(null == $v){
                    $v = ' ';
                }
                $tmp['key'.$count] = $v;
                $count ++;
            }
            $response[] = $tmp;
            unset($tmp);
        }
        return array(
            'keywords' => array($keywords),
            'sqlresult' => $response,
            );
    }

    public function getsqls(){  //获取sql语句
        $result = EastBlueLog::where('log_key', 'sql')->get();
        $response = array();
        foreach ($result as $value) {
            $response[] = array(
                'name'  =>  $value->desc,
                'value' =>  $value->new_value,
                );
        }

        $data = array(
            'sqls' => $response,
            );
        return Response::json($data);
    }

    public function addsqls($sql){  //新增sql语句
        if('' == $sql){
            return Response::json(array('error'=>'请输入sql语句'), 403);
        }
        if(!is_numeric(strpos(strtolower($sql), '|'))){
            return Response::json(array('error'=>'不合法的格式'), 403);
        }
        $descandsql = explode('|', $sql);
        $data = array();
        $data['log_key'] = 'sql';
        $data['desc'] = $descandsql[0];;
        $data['new_value'] = $descandsql[1];
        $data['game_id'] = Session::get('game_id');
        $data['created_at'] = time();
        $operation = EastBlueLog::insert($data);
        if($operation){
            return Response::json(array('msg'=>'新增成功'));
        }else{
            return Response::json(array('error'=>'插入数据失败'), 403);
        }
    }

    public function inputSqlDownload($tobedownloadkey='', $tobedownloadvalue=''){
        $msg = array(
            'code' => Lang::get('errorcode.unknown'),
            'msg' => Lang::get('errorcode.server_not_found')
        );
        $internal = 0;
        if($tobedownloadkey && $tobedownloadvalue){ //如果不是前端调用，返回值会有相应不同
            $internal = 1;
        }else{
            $tobedownloadkey = Input::get('tobedownloadkey');
            $tobedownloadvalue = Input::get('tobedownloadvalue');
        }
        if (empty($tobedownloadvalue)){ //下载数据若不存在
            return Response::json(array('error'=>'没有数据需要下载!'), 403);
        }
        $keys = array();
        $count = 0;
        foreach ($tobedownloadkey[0] as $key => $value) {
            if(is_numeric(strpos($key, 'key'))){
                $keys[$count] = $value;
            }
            $count++;
        }
        $count = 0;

        $now = time();
        $file = storage_path() . "/cache/" . $now . ".csv";

        $csv = CSV::init($file, $keys);
        foreach ($tobedownloadvalue as $key1 => $value1) {
            $result = array();
            foreach ($tobedownloadkey[0] as $key2 => $title) {
                if(is_numeric(strpos($key2, 'key'))){
                    $result[$title] = isset($value1[$key2]) ? $value1[$key2] : '';
                }
            }
            $res = $csv->writeData($result);
            unset($result);
        }
        $res = $csv->closeFile();
        if($internal){  //后端内部调用返回文件名的时间
            return $now;
        }
        if ($res){  //否则返回给页面
            $data = array('now' => $now);
            return Response::json($data);
        } else{
            return Response::json($msg, 403);
        }
        
    }

    public function inputSqlDownloadIndex(){
        $now = Input::get('now');
        $file = storage_path() . "/cache/" . $now . ".csv";
        $data = array(
                'content' => View::make('download', 
                        array(
                                'file' => $file
                        ))
        );
        return View::make('main', $data);
    }

    public function replaceData(&$data, $keyname, $id2name, $newname = ''){  //参数分别是需要替换的数据(注，这个数据格式，是前面在82行左右对应的格式返回的数据)，需要替换的键名，替换对应的数组，可选的新的键名
        $keyIndex = 0;
        if(in_array($keyname, $data['keywords'][0])){    //判断并找到操作ID对应的键名
            $keyIndex = array_search($keyname, $data['keywords'][0]);
            if($newname){
                $data['keywords'][0][$keyIndex] = $newname;
            }
        }

        foreach ($data['sqlresult'] as &$value) { //如果存在则替换操作以及物品名
            if($keyIndex){
                $value[$keyIndex] = isset($id2name[$value[$keyIndex]]) ? $id2name[$value[$keyIndex]] : $value[$keyIndex];
            }
        }
    }

    public function replaceKeys(&$data, $keysname2name){
        foreach ($keysname2name as $key => $value) {
            $keyIndex = 0;
            if(in_array($key, $data['keywords'][0])){    //判断并找到操作ID对应的键名
                $keyIndex = array_search($key, $data['keywords'][0]);
                if($value){
                    $data['keywords'][0][$keyIndex] = $value;
                }
            }
            unset($key);
            unset($value);
        }
    }

    public function gameserverindex(){
        if($filename = Input::get('filename')){
            $download_filename = "/home/game/findsql/".$filename;
            $data = array(
                    'content' => View::make('download', 
                            array(
                                    'file' => $download_filename
                            ))
            );
            return View::make('main', $data);
        }
        $servers = $this->getUnionServers();
        $data = array(
            'content' => View::make('serverapi.gamesql', array(
                'servers' => $servers,
                ))
        );
        return View::make('main', $data); 
    }

    public function gameserversql(){
        $game_id = Session::get('game_id');
        $type = Input::get('type');
        $sql_path = "/home/game/findsql";
        if(is_dir($sql_path)){
        }else{
        }
        $flag = '/home/game/findsql/find_sql_running';
        if(file_exists($flag)){ //如果脚本仍在执行，那么不下载也不提交
            return Response::json(array('error'=>'Executing'), 401); 
        }

        if('download' == $type){
            $download_filename = "find_sql_result";
            if(file_exists('/home/game/findsql/find_sql_result')){
                return Response::json(array('filename'=> $download_filename));
            }else{
                return Response::json(array('error' => 'No Result.'), 401);
            }
        }

        if('submit' == $type){
            $server_ids = Input::get('server_ids');
            if(is_array($server_ids)){
                $sql_file_path = "/home/game/findsql/find_sql_list";
                $first = 1; 
                foreach ($server_ids as $server_id) {
                    $server = Server::find($server_id);
                    if($server){
                        $towrite = $server->server_ip."\t".$server->api_dir_id."\n";
                        if($first){
                            file_put_contents($sql_file_path, $towrite);
                            $first = 0;
                        }else{
                            file_put_contents($sql_file_path, $towrite, FILE_APPEND);
                        }
                    }else{
                        continue;
                    }
                }
            }else{
                return Response::json(array('error'=>'Bad servers.'), 401); 
            }

            $sql = Input::get('sql');
            if($sql){
                $sql_file_path = "/home/game/findsql/find_sql.sql";
                $sql = $sql.';';
                file_put_contents($sql_file_path, $sql);
            }else{
                return Response::json(array('error'=>'Bad sql.'), 401); 
            }

            system("bash /home/game/findsql/find_sql.sh", $output);

            if(0 == $output){
                $download_filename = "find_sql_result";
                if(file_exists('/home/game/findsql/find_sql_result')){
                    return Response::json(array('filename'=> $download_filename));
                }else{
                    return Response::json(array('error' => 'No Result.'), 401);
                }
            }else{
                return Response::json(array('error'=> 'Script Execute Error'), 403);
            }
        }

        return Response::json(array('error'=>'Not a support type.'), 401); 
    }
}

