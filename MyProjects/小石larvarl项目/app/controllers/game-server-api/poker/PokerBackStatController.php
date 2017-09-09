<?php

class PokerBackStatController extends \BaseController {

    /**
     * Display a listing of the resource.
     *
     * @return Response
     */
    public function backStatIndex()
    {   
        $data = array(
                'content' => View::make('serverapi.poker.users.backStat')
            );
        return View::make('main',$data);
    }

    public function backStatGetOld()
    {   
        $game_id = Session::get('game_id');
    if($game_id==11){
        $start_time = Input::get('start_time');
        $end_time = Input::get('end_time'); 
        $cc = Input::get('cc');
        $c0 = Input::get('c0');
        $c1 = Input::get('c1');
        $c2 = Input::get('c2');
        $c3 = Input::get('c3');
        $c4 = Input::get('c4');
        $c5 = Input::get('c5');
        $c6 = Input::get('c6');
        $c7 = Input::get('c7');
        $c8 = Input::get('c8');
        //$game_id = 8;
        $game = Game::find($game_id);
        $api = SlaveApi::connect($game->eb_api_url, $game->eb_api_key,
                $game->eb_api_secret_key);
        $platform = Platform::find(Session::get('platform_id'));
        $server = Server::find(13);
        //var_dump($cc);var_dump($c0);var_dump($c1);var_dump($c2);die();
        if (! $platform)
        {
            $msg['error'] = 'can not find platform.';
            return Response::json($msg, 404);
        }
        

        //时间精确到日，所以以下时间默认从0点开始
        for($tmp_time=$end_time; strtotime($tmp_time)>=strtotime($start_time);
            $tmp_time=date("Y-m-d",strtotime('-1 day', strtotime($tmp_time))))
        {
            $result_line['date'] = $tmp_time;
            $result_line['timeD1'] = null;
            $result_line['timeD4'] = null;
            $result_line['timeD6'] = null;
            $result_line['timeD8'] = null;
            $result_line['timeD15'] = null;
            $result_line['timeM1'] = null;
            $result_line['timeM2'] = null;
            $result_line['timeM3'] = null;
            $result_line['timeM6'] = null;
            ///////////////////////////////////////////////array[0]
            if($c0==true){
                        $response = $api->getPokerBackStatOld(
                            $platform->platform_id, 
                            $game->game_id, 
                            $server->server_internal_id,
                            strtotime('-3 day', strtotime($tmp_time)),
                            strtotime($tmp_time),
                            strtotime($tmp_time),
                            0
                        );//1-3day
                        //var_dump($response);die();
                        $result_line['timeD1'] = $response->body;}
            ///////////////////////////////////////////////array[1]
            if($c1==true){
                        $response = $api->getPokerBackStatOld(
                            $platform->platform_id, 
                            $game->game_id, 
                            $server->server_internal_id,
                            strtotime('-5 day', strtotime($tmp_time)),
                            strtotime('-3 day', strtotime($tmp_time)),
                            strtotime($tmp_time),
                            3*24*3600
                        );//4-5day
                        $result_line['timeD4'] = $response->body;}
            // ///////////////////////////////////////////////array[2]
            if($c2==true){
                        $response = $api->getPokerBackStatOld(
                            $platform->platform_id, 
                            $game->game_id, 
                            $server->server_internal_id,
                            strtotime('-7 day', strtotime($tmp_time)),
                            strtotime('-5 day', strtotime($tmp_time)),
                            strtotime($tmp_time),
                            5*24*3600
                        );//6-7day
                        $result_line['timeD6'] = $response->body;}
            // ///////////////////////////////////////////////array[3]
            if($c3==true){
                        $response = $api->getPokerBackStatOld(
                            $platform->platform_id, 
                            $game->game_id, 
                            $server->server_internal_id,
                            strtotime('-14 day', strtotime($tmp_time)),
                            strtotime('-7 day', strtotime($tmp_time)),
                            strtotime($tmp_time),
                            7*24*3600
                        );//8-14day
                        $result_line['timeD8'] = $response->body;}
            // ///////////////////////////////////////////////array[4]
            if($c4==true){
                        $response = $api->getPokerBackStatOld(
                            $platform->platform_id, 
                            $game->game_id, 
                            $server->server_internal_id,
                            strtotime('-30 day', strtotime($tmp_time)),
                            strtotime('-14 day', strtotime($tmp_time)),
                            strtotime($tmp_time),
                            14*24*3600
                        );//15-30day
                        $result_line['timeD15'] = $response->body;}
            ///////////////////////////////////////////////array[5]
            if($c5==true){
                        $response = $api->getPokerBackStatOld(
                            $platform->platform_id, 
                            $game->game_id, 
                            $server->server_internal_id,
                            strtotime('-2 month', strtotime($tmp_time)),
                            strtotime('-1 month, +1 day', strtotime($tmp_time)),
                            strtotime($tmp_time),
                            $tmp_time-strtotime('-1 month, +1 day', strtotime($tmp_time))
                        );//1-2month
                        $result_line['timeM1'] = $response->body;}
            ///////////////////////////////////////////////array[6]
            if($c6==true){
                        $response = $api->getPokerBackStatOld(
                            $platform->platform_id, 
                            $game->game_id, 
                            $server->server_internal_id,
                            strtotime('-3 month', strtotime($tmp_time)),
                            strtotime('-2 month, +1 day', strtotime($tmp_time)),
                            strtotime($tmp_time),
                            $tmp_time-strtotime('-2 month, +1 day', strtotime($tmp_time))
                        );//3-6month
                        $result_line['timeM2'] = $response->body;}
            ///////////////////////////////////////////////array[7]
            if($c7==true){
                        $response = $api->getPokerBackStatOld(
                            $platform->platform_id, 
                            $game->game_id, 
                            $server->server_internal_id,
                            strtotime('-6 month', strtotime($tmp_time)),
                            strtotime('-3 month, +1 day', strtotime($tmp_time)),
                            strtotime($tmp_time),
                            $tmp_time-strtotime('-3 month, +1 day', strtotime($tmp_time))
                        );//3-6month
                        $result_line['timeM3'] = $response->body;}
            ///////////////////////////////////////////////array[8]
            if($c8==true){
                        $response = $api->getPokerBackStatOld(
                            $platform->platform_id, 
                            $game->game_id, 
                            $server->server_internal_id,
                            0,
                            strtotime('-6 month, +1 day', strtotime($tmp_time)),
                            strtotime($tmp_time),
                            $tmp_time-strtotime('-6 month, +1 day', strtotime($tmp_time))
                        );//6month to long long ago
                        $result_line['timeM6'] = $response->body;}

        ///////////////////////////////////////////////**********/array[][9]
          $result_table[] = $result_line;
            unset($result_line);
        }
        // foreach ($result_table as $key => $value) {
        //      var_dump($value);
        //  }
        // die();
        if(!empty($result_table))
        {
            return $result_table;
        }
        else
        {
            return Response::json('result_table is empty.');
        }
    }else{
        $end_time = strtotime(Input::get('end_time'));
        $start_time = $end_time - 7 * 24 * 3600 + 1;
        $platform_id = Session::get('platform_id');
        $platform = Platform::find($platform_id);
        $game = Game::find(Session::get('game_id'));
        $api = SlaveApi::connect($game->eb_api_url, $game->eb_api_key, 
                $game->eb_api_secret_key);
        $user_counts = array();
        $server_start_time = $start_time - 15 * 60 * 60;
        $server_end_time = $end_time;  
        Log::info(var_export("end_time:" . $end_time,true));  
        $response = $api->getWeeklyStat($platform_id, $game->game_id, 
                $server_start_time, $server_end_time, $start_time, $end_time);
        $body = $response->body;
        Log::info(var_export("end:",true));
    }
          
    }

    public function refluxFirstPayAndAnonyIndex()
    {   
        $data = array(
                'content' => View::make('serverapi.poker.users.refluxFirstPayAndAnony')
            );
        return View::make('main',$data);
    }

    public function refluxFirstPayAndAnony()
    {
        $start_time = Input::get('start_time');
        $end_time = Input::get('end_time');

        $game_id = Session::get('game_id');
        $game = Game::find($game_id);
        $platform = Platform::find(Session::get('platform_id'));
        $server = Server::find(13);//poker server_id
        if (! $platform)
        {
            $msg['error'] = 'can not find platform.';
            return Response::json($msg, 404);
        }

        $api = SlaveApi::connect($game->eb_api_url, $game->eb_api_key,
                $game->eb_api_secret_key);

        $twoDay = 2*24*3600;
        for($tmp_time=$end_time; strtotime($tmp_time)>=strtotime($start_time);
            $tmp_time=date("Y-m-d",strtotime('-1 day', strtotime($tmp_time))))
        {
            $result_line['date'] = $tmp_time;

            ///////////////////////////////////////////////array[0]
            $responseP = $api->getPokerBackStat(
                $platform->platform_id, 
                $game->game_id, 
                $server->server_internal_id,
                0,
                strtotime('-2 day', strtotime($tmp_time)),
                strtotime($tmp_time),
                $twoDay
            );
            if(is_numeric($responseP->body)){
                $result_line['reflux'] = $responseP->body;
            }
            else{
                $result_line['reflux'] = 0;
            }

            $responseF = $api->getFirstPayPlayer(
                $platform->platform_id, 
                $game->game_id,
                $server->server_internal_id,
                strtotime($tmp_time),
                strtotime('+1 day', strtotime($tmp_time))
            );
            if(is_numeric($responseF->body)){
                $result_line['firstPay'] = $responseF->body;
            }
            else{
                $result_line['firstPay'] = 0;
            }

            $responseA = $api->getAnonyPlayer(
                $platform->platform_id,
                $game->game_id,
                $server->server_internal_id,
                strtotime($tmp_time),
                strtotime('+1 day', strtotime($tmp_time))
            );
            if(is_numeric($responseA->body)){
                $result_line['anonyPlayer'] = $responseA->body;
            }
            else{
                $result_line['anonyPlayer'] = 0;
            }

            $result_table[] = $result_line;
            unset($result_line);
        }
        if(!empty($result_table))
        {
            return $result_table;
        }
        else
        {
            return Response::json('result_table is empty.');
        }

    }

}