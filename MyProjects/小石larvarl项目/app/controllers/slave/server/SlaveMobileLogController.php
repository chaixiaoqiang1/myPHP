<?php 

class SlaveMobileLogController extends \SlaveServerBaseController {

	public function getFormData(){
		$game_id = Input::get('game_id');
		$id = Input::get('id');
        $created_at = Input::get('created_at');
        $table_name = array('device_list','login_device','third_party','create_player','played_server_list','registration_list','channel_list','device_list,user');
        if($id == 'third_party'){
            $tmp_app_id = DB::connection($this->db_qiqiwu)->table('tp_applications')
                ->where('game_id','=',$game_id)
                ->where('tp_code','=','fb')
                ->select('app_id')
                ->get();
            $app_id = $tmp_app_id[0]->app_id;
        	$result=DB::connection($this->db_qiqiwu)->table('third_party')
                    ->where('app_id','=',$app_id)
        			->selectRaw('count(1) as num');
        	$num_not_null = $result->where('token_for_business','!=','')->get();
        	$result = array(
        		'num_not_null' => $num_not_null[0]->num,
        		);
        }elseif($id == 'device_list,users') {
            $tmp1 = DB::connection($this->db_qiqiwu)->table('device_list' .' as tar')
                ->where('game_id', $game_id)
                ->where('time', '>', strtotime($created_at['date']))
                ->groupBy('source')
                ->selectRaw('source, count(1) as num')
                ->get();
            $tmp2 = DB::connection($this->db_qiqiwu)->table('users' .' as tar')
                ->where('game_source', $game_id)
                ->where('created_time', '>', $created_at['date'])
                ->groupBy('source')
                ->selectRaw('source, count(1) as num')
                ->get();
            $result = array(
                'device_list' => $tmp1,
                'users' => $tmp2
                );
        }else{
            $result = DB::connection($this->db_qiqiwu)->table($id .' as tar')
                ->where('game_id','=',$game_id);
        	$result = $result->selectRaw('count(1) as num')->get();
        	$result = array(
        		'num' => $result[0]->num,
        		);
        }
        return Response::json($result);
	}

    public function getInformaitionData(){
        $game_id = Input::get('game_id');
        $tmp_tp_app = DB::connection($this->db_qiqiwu)->table('tp_applications')->where('game_id',$game_id)->get();
        $tmp_app = DB::connection($this->db_qiqiwu)->table('applications')->where('game_id',$game_id)->get();
        $tmp_game_list_qiqiwu = DB::connection($this->db_qiqiwu)->table('game_list')->where('game_id',$game_id)->get();
        $tmp_game_list_payment = DB::connection($this->db_payment)->table('game_list')->where('game_id',$game_id)->get();
        $tmp_goods_list = DB::connection($this->db_payment)->table('goods_list')->where('game_id',$game_id)->get();

        $result = array(
            'tp_applications' =>$tmp_tp_app,
            'applications' =>$tmp_app,
            'game_list_qiqiwu' =>$tmp_game_list_qiqiwu,
            'game_list_payment' =>$tmp_game_list_payment,
            'tmp_goods_list' =>$tmp_goods_list,
            );

        return Response::json($result);
    }

    public function getFormationData(){
        $search_type= Input::get('search_type');
        $start_time = Input::get('start_time');
        $end_time = Input::get('end_time');
        $vip = Input::get('vip');
        $player_lev = Input::get('player_lev');
        $hero_id = Input::get('hero_id');
        $formation = Input::get('formation');
        $hero_type = Input::get('hero_type');
        $formation_type = Input::get('formation_type');

        $result = DB::connection($this->db_name)->table('log_formation')
            ->whereBetween('action_time',array($start_time,$end_time));

        if(!empty($vip)){
            $result->whereIn('vip',$vip);
        }
        if(!empty($player_lev)){
            $result->whereIn('player_lev',$player_lev);
        }
        if(!empty($hero_type)){
            $result->whereIn('hero_type',$hero_type);
        }
        if($formation_type){
            $result->where('formation_type',$formation_type);
        }
        if(1 == $search_type){
           $total = $result->count()/5;
           $count = $result->where('formation',$formation)->count()/5;
        }elseif(2 == $search_type){
            $result->where('formation',$formation);
            $total = $result->count()/5;
            $count = $result->where('is_win',1)->count()/5;
        }elseif(3 == $search_type){
            $total = $result->count()/5;
            $count = $result->whereIn('hero_id',$hero_id)->count();
        }elseif(4 == $search_type){
            $result->whereIn('hero_id',$hero_id);
            $total = $result->count();
            $count = $result->where('is_win',1)->count();
        }elseif(5 == $search_type){//查询英雄的登场率和胜率
            $total = $result->count();//所有英雄的总上场数，要除以5
            $hero_appear = $result->whereIn('hero_id',$hero_id)->groupBy('hero_id')
                ->selectRaw('hero_id,COUNT(1) as hero_appear')->get();//每个英雄的上场数，即参加场数

            $hero_win = $result->where('is_win',1)
                ->selectRaw('hero_id,COUNT(1) as hero_win')->get();//每个英雄的胜利场数
            $count = array(
                'hero_appear' => $hero_appear,
                'hero_win' => $hero_win,
                );
        }

        $data = array(
            'total' => $total,
            'count'=> $count,
        );
        return Response::json($data);
        
    }

    public function getPartnerDel(){
        $start_time = Input::get('start_time');
        $end_time = Input::get('end_time');
        $player_id = Input::get('player_id');
        $mid = Input::get('mid');
        $result = DB::connection($this->db_name)->table('log_partner_del as pd')
            ->leftJoin('log_create_partner as cp',function($join){
                $join->on('cp.player_id','=','pd.player_id')
                ->on('cp.partner_id','=','pd.partner_id');
            })
            ->selectRaw('distinct pd.player_id,pd.mid,cp.table_id,pd.partner_id,time')
            ->whereBetween('pd.time',array($start_time,$end_time));
        if($player_id){
            $result->where('pd.player_id',$player_id);
        }
        if($mid){
            $result->where('pd.mid',$mid);
        }
        $result = $result->get();
        return Response::json($result);
    }

}