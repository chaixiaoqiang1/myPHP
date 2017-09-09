<?php

use Illuminate\Console\Command;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Input\InputArgument;

class MasterMonitor extends Command {   //用来监控一些数据异常，计划每隔一段时间执行一次

    protected $warning_range = 20000;    //报警的比例值，计算方法是当日运营平台修改元宝，邮件发放元宝以及补储元宝的总元宝数除以当日充值的美元
    protected $internal_warning_range = 70000;
    protected $poker_warning_range = 0;
    protected $poker_internal_warning_range = 0;
    protected $max_yuanbao_range = 300000;

    protected $recharge_day = 14;    //元宝变化的玩家向前找几天内的充值

    protected $monitor_days = 7;    //监控几天内的玩家元宝变化

    protected $to_email = array(
        'zgliu@xinyoudi.com',
        'dlyu@xinyoudi.com',
        'hlcai@xinyoudi.com',
        'sswang@xinyoudiglobal.com',
        );

    protected $warning_num = 0;     //有多少条数据需要监控

    protected $data = array();  //存储所有玩家元宝变化以及充值美金等信息

    protected $internal_uids = array();

    protected $yuanbao_type = array('gold', 'crystal'); //在发放物品邮件时本质为元宝的字段

    protected $name = 'master:monitor';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Master monitor';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function fire()  //脚本调用开始，获取参数等
    {
        $start = $this->argument('start');
        if('start' == $start){
            $this->start_monitor();
        }elseif('test' == $start){
            $this->info('Test master monitor!');
        }else{
            $this->info('Unknown argument!');
        }
    }

    private function start_monitor(){   //正确输入下开始脚本
        $this->info('start_monitor!');
        //监控元宝变化和当日充值信息
        $this->CheckYuanBaoChange();
        //监控其他
    }

    private function CheckYuanBaoChange(){  //监控元宝变化和当日充值信息
        //初始化内玩的信息
        $table = Table::init(public_path() . '/table/neiwan.txt');
        $neiwans = $table->getData();
        foreach ($neiwans as $k => $v) {
            $this->internal_uids[$v->game_id][] = $v->uid;
        }
        
        $time = strtotime(date("Y-m-d 00:00:00", time()));
        $start_time = $time - 86400*$this->monitor_days;
        $change_keys = array('changeYuanbao', 'mobilechangeYuanbao', 
        //'restore',    //不查看补储信息
        );    //所有涉及到元宝变化的记录

        $record_yuanbao = EastBlueLog::whereIn('log_key', $change_keys)
                    ->whereBetween('created_at', array($start_time, $time))
                    ->selectRaw('`desc` as data, `log_key`, `game_id`')
                    ->orderBy('game_id', 'asc')
                    ->get();

        if(count($record_yuanbao)){ //整理数据
            foreach ($record_yuanbao as $value) {
                $this->getYuanbaoIncrease($value);
            }
            unset($record_yuanbao);
            unset($value);
        }
        //此时data已经是精确到玩家的今日的log表中记录的元宝增量和扣除量
        $this->info('Check EastBlueLog Done!');
        $operations = Operation::whereBetween('giftbag_id', array(1, 99)) //礼包id较低的是特殊的经济值操作
                        ->whereBetween('operate_time', array($start_time, $time))
                        ->where('operation_type', 'mail_gift')
                        ->selectRaw("game_id, server_name, giftbag_id, player_id, extra_msg")
                        ->get();
        if(count($operations)){
            foreach ($operations as $value) {
                $game = Game::find($value->game_id);
                $table = Table::init(public_path() . '/table/' . $game->game_code . '/award.txt');
                $table = $table->getData();
                $yuanbao_id = 0;
                foreach ($table as $single_line) {
                    if(in_array($single_line->ename, $this->yuanbao_type)){
                        $yuanbao_id = $single_line->id;
                        break;
                    }
                    unset($single_line);
                }
                unset($table);
                if($yuanbao_id && $yuanbao_id == $value->giftbag_id){
                    $single_operation = explode('|', $value->extra_msg);
                    if(3 == count($single_operation)){  //3个是正确长度
                        if('success' == $single_operation[0]){  //如果发放成功
                            $num = substr($single_operation[2], strlen('数量:'));
                            if(isset($this->data[$value->game_id][$value->player_id])){
                                if(isset($this->data[$value->game_id][$value->player_id]['server_internal_id']) 
                                    && $this->data[$value->game_id][$value->player_id]['server_internal_id']){
                                    $this->data[$value->game_id][$value->player_id]['increase'] += $num;
                                }else{
                                    $server = Server::where('game_id', $value->game_id)->where('server_name', $value->server_name)->first();
                                    $server_internal_id = isset($server->server_internal_id) ? $server->server_internal_id : 0;
                                    unset($server);
                                    $this->data[$value->game_id][$value->player_id]['server_internal_id'] += $server_internal_id;
                                    $this->data[$value->game_id][$value->player_id]['increase'] += $num;
                                }
                            }else{
                                $server = Server::where('game_id', $value->game_id)->where('server_name', $value->server_name)->first();
                                $server_internal_id = isset($server->server_internal_id) ? $server->server_internal_id : 0;
                                unset($server);
                                $this->data[$value->game_id][$value->player_id] = array(
                                    'increase'  =>  $num,
                                    'decrease'  =>  0,
                                    'server_internal_id' => $server_internal_id,
                                );
                            }
                        }
                    }
                }
                unset($single_operation);
                unset($num);
                unset($value);
            }
        }
        //删除增加量不足报警值的数据
        foreach ($this->data as $k1 => $v1) {
            foreach ($v1 as $k2 => $v2) {
                if($v2['increase'] < $this->warning_range){
                    unset($this->data[$k1][$k2]);
                }
            }
        }
        $this->info('Check Operation Done!');
        //此时data已经是每个游戏玩家的元宝增量和扣除量，从各个游戏的slave端获取每个玩家七日内的充值
        foreach ($this->data as $game_id => $single_game_info) {
            $game = Game::find($game_id);
            if(!$game){
                continue;
            }
            if('poker' == $game->game_code){    //德扑的筹码和金币还有区别，这个地方暂时无法妥善处理，单从补储记录里无法区分
                unset($this->data[$game_id]);
                continue;
                $mail_range = $this->poker_warning_range;
                $mail_internal_range = $this->poker_internal_warning_range;
            }else{
                $mail_range = $this->warning_range;
                $mail_internal_range = $this->internal_warning_range;
            }
            $slave_api = SlaveApi::connect($game->eb_api_url, $game->eb_api_key, $game->eb_api_secret_key);
            //循环单个游戏下的所有玩家信息
            foreach ($single_game_info as $player_id => $player_info) {
                if(!$player_id){    //如果没有playerid那么直接跳过
                    unset($this->data[$game_id][$player_id]);
                    continue;
                }
                //后台操作值超过报警值才有可能需要报警，否则直接跳过
                if($this->data[$game_id][$player_id]['increase'] >= $this->warning_range){
                    $this->data[$game_id][$player_id]['platform_id'] = $game->platform_id;
                    //监控截止当前七天内的储值信息
                    $pay_end_time = strtotime(date("Y-m-d 00:00:00", time()));
                    $pay_start_time = $pay_end_time - $this->recharge_day * 86400;
                    //获取玩家的server_internal_id,uid,充值美金和充值元宝以及玩家名等值,这里是从官网的create_player表查询的，如果有server_internal_id则也限制这个条件
                    $about_servers = $this->getServersAbout($game_id, $this->data[$game_id][$player_id]['server_internal_id']); //相关的几个服务器，可能为array(0)
                    $player_dollar = $slave_api->getSinglePlayerPayDollar($pay_start_time, $pay_end_time, $game_id, $player_id, $about_servers, $game->platform_id);    //获取每个玩家的uid和充值美金及元宝
                    unset($about_servers);
                    if(200 == $player_dollar->http_code){   //如果正常结果，则赋值
                        $this->data[$game_id][$player_id]['dollar'] = isset($player_dollar->body->all_dollar) ? $player_dollar->body->all_dollar : 0;
                        $this->data[$game_id][$player_id]['uid'] = isset($player_dollar->body->uid) ? $player_dollar->body->uid : 0;
                        $this->data[$game_id][$player_id]['recharge_yuanbao'] = isset($player_dollar->body->all_yuanbao) ? $player_dollar->body->all_yuanbao : 0;
                        if(isset($player_dollar->body->server_internal_id) && $player_dollar->body->server_internal_id){
                            $this->data[$game_id][$player_id]['server_internal_id'] = $player_dollar->body->server_internal_id;
                        }
                        $this->data[$game_id][$player_id]['player_name'] = isset($player_dollar->body->player_name) ? $player_dollar->body->player_name : '-';
                    }else{  //如果无法正常返回，则把所有的数据都认定为没有，但是这些数据会发送邮件
                        $this->data[$game_id][$player_id]['dollar'] = 0;
                        $this->data[$game_id][$player_id]['uid'] = 0;
                        $this->data[$game_id][$player_id]['recharge_yuanbao'] = 0;
                        $this->data[$game_id][$player_id]['server_internal_id'] = 0;
                        $this->data[$game_id][$player_id]['player_name'] = '-';
                    }
                }else{
                    unset($this->data[$game_id][$player_id]);
                    continue;
                }
                //如果我们没有玩家的uid但是有玩家的server_internal_id信息,那么可以通过查询日志库尝试得到玩家的uid等信息
                if($this->data[$game_id][$player_id]['server_internal_id'] && !$this->data[$game_id][$player_id]['uid']){   //如果有玩家的服务器信息但是没有玩家的uid信息
                    $about_servers = $this->getServersAbout($game_id, $this->data[$game_id][$player_id]['server_internal_id']);
                    $player_uid = array();
                    foreach ($about_servers as $single_server_internal_id) {    //到所有相关的日志库里都查看一下是否有此玩家的数据
                        $try = $slave_api->getplayerinfolikeserver($type=1, $player_id, $this->data[$game_id][$player_id]['platform_id'], $game_id, $single_server_internal_id);
                        if(200 == $try->http_code){
                            $player_uid = $try;
                            break;
                        }
                    }
                    unset($try);
                    unset($about_servers);
                    if(isset($player_uid->http_code) && 200 == $player_uid->http_code){  //根据玩家id来从日志库查询玩家信息
                        $player_uid = $player_uid->body;
                        foreach ($player_uid as $player_uid_value) {    //找到玩家的uid以及玩家名等信息
                            if(isset($player_uid_value->uid)){
                                $tmp_uid = $player_uid_value->uid;
                                $this->data[$game_id][$player_id]['player_name'] = $player_uid_value->player_name;
                            }else{  //如果结构异常，赋值为空并打印返回值
                                $tmp_uid = 0;
                                $this->data[$game_id][$player_id]['player_name'] = '-';
                                Log::info('MasterMonitor--Bad_Structure--'.var_export($player_uid_value, true));
                            }
                            break;  //第一条后跳出
                        }
                        $player_uid = $tmp_uid;
                        unset($tmp_uid);
                        if(!$player_uid){
                            unset($this->data[$game_id][$player_id]);
                            continue;
                        }
                        $player_dollar = $slave_api->getSinglePlayerPayDollar($pay_start_time, $pay_end_time, $game_id, $player_id, $this->data[$game_id][$player_id]['server_internal_id'], $game->platform_id, $player_uid);    //获取每个玩家的uid和充值美金及元宝
                        if(200 == $player_dollar->http_code){   //如果正常结果，则赋值
                            $this->data[$game_id][$player_id]['dollar'] = isset($player_dollar->body->all_dollar) ? $player_dollar->body->all_dollar : 0;
                            $this->data[$game_id][$player_id]['uid'] = isset($player_dollar->body->uid) ? $player_dollar->body->uid : $player_uid;
                            $this->data[$game_id][$player_id]['recharge_yuanbao'] = isset($player_dollar->body->all_yuanbao) ? $player_dollar->body->all_yuanbao : 0;
                            if(isset($player_dollar->body->server_internal_id) && $player_dollar->body->server_internal_id){
                                $this->data[$game_id][$player_id]['server_internal_id'] = $player_dollar->body->server_internal_id;
                            }
                        }else{  //如果无法正常返回，则把所有的数据都认定为没有，但是这些数据会发送邮件
                            $this->data[$game_id][$player_id]['uid'] = $player_uid;
                        }
                    }else{  //如果也无法通过日志库得到结果，那么删除本条数据
                        unset($this->data[$game_id][$player_id]);
                        continue;
                    }
                }
                if(!$this->data[$game_id][$player_id]['uid']){  //若此时依然没有玩家的uid信息，那么删除本条数据
                    unset($this->data[$game_id][$player_id]);
                    continue;
                }
                $this->checkYuanbaoPayDollarRate($game_id, $this->data[$game_id][$player_id], $mail_range, $mail_internal_range);
                Log::info('MasterMonitor----Game:'.$game->game_name.'--PlayerID:'.$player_id.'--'.var_export($this->data[$game_id][$player_id], true));
            }
        }
        $this->info('Get PayDollar And GameYuanbaoIncrease Done!');
        $this->YuanbaoChangeSendMail();
        $this->info('Send Email Done!');
    }

    private function checkYuanbaoPayDollarRate($game_id, &$single_player_info, $mail_range, $mail_internal_range){    //此功能用来检测增加的元宝值和七天内的充值额的比例
        $single_player_info['is_internal_uid'] = 0;
        if($single_player_info['uid']){ //能找到玩家的uid
            $white_uids = isset($this->internal_uids[$game_id]) ? $this->internal_uids[$game_id] : array();
            if(in_array($single_player_info['uid'], $white_uids)){  //如果是内玩，那么跳过
                $single_player_info['is_internal_uid'] = 1;
                if($single_player_info['increase'] >= $mail_internal_range){   //超过内玩限额
                    $this->warning_num++;
                    $send_mail = 1;
                }else{  //未超过内玩限额
                    $send_mail = 0;
                }
            }else{  //非内玩
                if($single_player_info['increase']){   //可以查询到玩家的元宝增量的
                    if($single_player_info['increase'] - $single_player_info['recharge_yuanbao'] >= $mail_range){   //如果游戏内获得的元宝超过充值元宝的部分达到了报警界
                        $this->warning_num++;
                        $send_mail = 1;
                    }else{  //未超过报警界
                        $send_mail = 0;
                    }
                }else{  //未能查询到玩家的元宝增量的，发送邮件
                    $this->warning_num++;
                    $send_mail = 1;
                }
            }
        }else{  //如果找不到这个玩家的uid，那么发送邮件
            $send_mail = 1;
            $this->warning_num++;
        }
        $single_player_info['send_mail'] = $send_mail;
    }

    private function getYuanbaoIncrease($single_record){  //根据不同的log_key得到元宝变动的值
        switch ($single_record->log_key) {
            case 'changeYuanbao':
                $tmp_data = explode('|', $single_record->data);
                $player_id = $tmp_data[0];
                if(5 == count($tmp_data)){  //5个说明是标准格式
                    $yuanbao_num = 0;
                    if('元宝' == $tmp_data[4]){
                        $yuanbao_num = $tmp_data[3];
                    }else{
                        break;
                    }

                    if($yuanbao_num){
                        if(isset($this->data[$single_record->game_id][$player_id])){
                            $change_key = $tmp_data[2] == '增加' ? 'increase' : 'decrease';
                            $yuanbao_num = $change_key == 'increase' ? $yuanbao_num : -$yuanbao_num;
                            $this->data[$single_record->game_id][$player_id][$change_key] += $yuanbao_num;
                        }else{
                            $server = Server::where('game_id', $single_record->game_id)->where('server_name', $tmp_data[1])->first();
                            $server_internal_id = isset($server->server_internal_id) ? $server->server_internal_id : 0;
                            unset($server);
                            $change_key = $tmp_data[2] == '增加' ? 'increase' : 'decrease';
                            $yuanbao_num = $change_key == 'increase' ? $yuanbao_num : -$yuanbao_num;
                            $zero_key = $tmp_data[2] == '增加' ? 'decrease' : 'increase';
                            $this->data[$single_record->game_id][$player_id] = array(
                                    $change_key => $yuanbao_num,
                                    $zero_key => 0,
                                    'server_internal_id' => $server_internal_id,
                                );
                        }
                    }
                }
                break;
            
            case 'mobilechangeYuanbao':
                $tmp_data = explode('|', $single_record->data);
                $player_id = $tmp_data[0];
                if(3 == count($tmp_data)){  //3个说明是标准格式
                    $tmp_data = explode(' ', $tmp_data[2]);
                    $yuanbao_num = 0;
                    foreach ($tmp_data as $value) { //至多只会有一条修改钻石的记录
                        if(0 === strpos($value, '修改钻石') || strpos($value, '修改钻石')){
                            $yuanbao_num = (int) (substr($value, (strpos($value, '修改钻石') + strlen('修改钻石'))));
                            break;
                        }
                        unset($value);
                    }
                    if($yuanbao_num){
                        if(isset($this->data[$single_record->game_id][$player_id])){
                            $change_key = $yuanbao_num > 0 ? 'increase' : 'decrease';
                            $this->data[$single_record->game_id][$player_id][$change_key] += $yuanbao_num;
                        }else{
                            $change_key = $yuanbao_num > 0 ? 'increase' : 'decrease';
                            $zero_key = $yuanbao_num > 0 ? 'decrease' : 'increase';
                            $this->data[$single_record->game_id][$player_id] = array(
                                    $change_key => $yuanbao_num,
                                    $zero_key => 0,
                                    'server_internal_id' => 0,
                                );
                        }
                    }
                }
                break;

            case 'restore':
                $tmp_data = explode('|', $single_record->data);
                if(7 == count($tmp_data)){  //7个说明是标准格式
                    $yuanbao_num = 0;
                    $yuanbao_num = (int)($tmp_data[2]); //必然大于等于0 
                    $player_id = $tmp_data[6];

                    if($yuanbao_num){
                        if(isset($this->data[$single_record->game_id][$player_id])){
                            $change_key = $yuanbao_num > 0 ? 'increase' : 'decrease';
                            $this->data[$single_record->game_id][$player_id][$change_key] += $yuanbao_num;
                        }else{
                            $server = Server::where('game_id', $single_record->game_id)->where('server_name', $tmp_data[4])->first();
                            $server_internal_id = isset($server->server_internal_id) ? $server->server_internal_id : 0;
                            unset($server);
                            $change_key = $yuanbao_num > 0 ? 'increase' : 'decrease';
                            $zero_key = $yuanbao_num > 0 ? 'decrease' : 'increase';
                            $this->data[$single_record->game_id][$player_id] = array(
                                    $change_key => $yuanbao_num,
                                    $zero_key => 0,
                                    'server_internal_id' => $server_internal_id,
                                );
                        }
                    }
                }
                break;
            default:
                break;
        }
    }

    private function YuanbaoChangeSendMail(){
        if($this->warning_num){
            $mail_data = array(
                'data' => $this->data,
                'num' => $this->warning_num,
                );
            $to_email = $this->to_email;
            Mail::send('YuanbaoChangeWarning', $mail_data, function($message) use ($to_email)
            {
                $message->subject('YuanbaoChangeWarning');
                $message->from('cs@game168.com.tw', 'Eastblue');
                $message->to($to_email);
            });
        }
    }

    private function getMainServer($game_id, $server_internal_id){    //根据game_id和server_internal_id返回此服务器的所属的主服
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

    private function getServersAbout($game_id, $server_internal_id){    //根据game_id和server_internal_id确定相关的几个服务器
        $server_info = $this->getUnionGame();   //获取合服文件内容
        foreach ($server_info as $value) {
            if($game_id == $value->gameid){
                if(in_array($server_internal_id, explode(',', $value->serverid2))){ //如果属于本条数据的从服，返回这些从服和主服
                    $about_servers = explode(',', $value->serverid2);
                    $about_servers[] = $value->serverid1;
                    return $about_servers;
                }
                if($server_internal_id == $value->serverid1){   //如果属于本条数据的主服，返回这些从服和主服
                    $about_servers = explode(',', $value->serverid2);
                    $about_servers[] = $value->serverid1;
                    return $about_servers;
                }
            }
        }
        return array($server_internal_id);
    }

    private function getUnionGame(){
        $server = Table::init(public_path() . '/table/' . 'flsg' . '/server.txt');
        $server = $server->getData();
        $server = (array)$server;
        return $server;
    }

    /**
     * Get the console command arguments.
     *
     * @return array
     */
    protected function getArguments()
    {
        return array(
            array('start', InputArgument::REQUIRED, 'start'),
        );
    }

    /**
     * Get the console command options.
     *
     * @return array
     */
    protected function getOptions()
    {
        return array(
            array('example', null, InputOption::VALUE_OPTIONAL, 'An example option.', null),
        );
    }

}
