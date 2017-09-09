<?php

class MobileGameController extends \BaseController{

	public function MobileGamesProcedureIndex(){
		$game_id = Session::get('game_id'); 
        $whatsdone = MobileGameProcess::where('game_id', $game_id)->get();
        $data = array(
            'game_id' => Session::get('game_id'),
            'updated_at' => time()
            );
        if(count($whatsdone)){
            $done = explode(',', $whatsdone[0]->done);
        }else{
            $result = MobileGameProcess::where('game_id','=',$game_id)->insert($data);
            $done = array();
        }

        
		$data = array(
                'content' => View::make('MobilegamesProcedure', array(
                    'done' => $done,
                    )));
        return View::make('main', $data);
	}

    public function MobileGamesProcedureData(){
        $game_id = Session::get('game_id');
        $platform_id = Session::get('platform_id');
        $game = Game::find($game_id);
        $id = Input::get('id');
        $type = Input::get('type');

        if($id && !$type){
            if($id == 'submit'){
                $this->UpdateData();//更新打钩选项
            }
            elseif($id == 'mobile_modification_sdk'){
                $result = DB::table('mobile_modification')
                    ->where('game_id',$game_id)
                    ->where('type','=',1)
                    ->get();
                if($result){
                    return Response::json($result[0]);
                }
            }
            elseif($id == 'mobile_modification_officalweb'){
                $result = DB::table('mobile_modification')
                    ->where('game_id',$game_id)
                    ->where('type','=',2)
                    ->get();
                if($result){
                    return Response::json($result[0]);
                }
            }
            else{
                $api = SlaveApi::connect($game->eb_api_url, $game->eb_api_key, $game->eb_api_secret_key);
                $result = $api->getFormData($game_id, $platform_id, $id, $game->created_at);
                if(200 == $result->http_code){
                    $result = $result->body;
                    if($result){
                        return Response::json($result);
                    }
                    else
                    {
                        return Response::json(array('error' => '没有查到数据'), 404);
                    }
                }else{
                    return $api->sendResponse();
                }
            }
        }
        elseif (!$id && $type) {
            
            $this->modifyData();//sdk、官网修改数据
            
        }
    }

    public function UpdateData(){
        $game_id = Session::get('game_id');
        $game = Game::find($game_id);
        $app = Input::get('app');
        $apps = '';
        foreach ($app as $value) {
            if($value){
                $apps .= $value.',';
            }
        }
        if($apps){
            $apps = substr($apps, 0, strlen($apps)-1);
        }
        $data = array(
            'game_id' => Session::get('game_id'),
            'done' => $apps
            );
        $tmp = MobileGameProcess::where('game_id','=',$game_id)->selectRaw('count(1)')->get();
        if($tmp){
            $result = MobileGameProcess::where('game_id','=',$game_id)->update($data);
        }else{
            $result = MobileGameProcess::where('game_id','=',$game_id)->insert($data);
        }
        
       
            if($result){
                return Response::json(array('msg' => '提交成功'), 200);
            }
            else
            {
                return Response::json(array('msg' => '没有查到数据'), 404);
            }
        
    }

	public function uploadDocIndex(){
        $file = Input::get('file');
        if($file){
            $data = array(
                'content' => View::make('download', array(
                    'file' => $file
                    )));
            return View::make('main', $data);
        }
		$game_id = Session::get('game_id'); 
		$data = array(
                'content' => View::make('uploaddoc', array()));
        return View::make('main', $data);
	}

	public function uploadDocData(){
        $type = Input::get('type');
        if($type){
            return $this->downloadDocIndex();
        }
        $game_id = Session::get('game_id');
        $game = Game::find($game_id);
        $platform_id = Session::get('platform_id');
        if ($_FILES["docfile"]["error"] > 0){
            return Response::json(array('error'=>'上传文件出错!'), 200);
        }else{
            move_uploaded_file($_FILES["docfile"]["tmp_name"], "table/cache/".$game_id.".doc");
            return Response::json(array('error'=>'上传文件成功!'), 200);
        }
    }

    public function downloadDocIndex()
    {
    	$game_id = Session::get('game_id');
        $game = Game::find($game_id);
        $file ="table/cache/" . $game_id . ".doc";
        if(file_exists(public_path().'/table/cache/' . $game_id . '.doc')){
            $data = array(
                'file' => $file
            );
            return Response::json($data);
        }
        else{
            return Response::json(array('error'=>'没有文件!'), 403);
        }

    }

    public function modifyData(){
        $game_id = Session::get('game_id');
        $game = Game::find($game_id);
        $data = Input::get('data');
        $type = Input::get('type');
        if($type == 'sdkmodify'){
            $type = 1;
        }
        else{
            $type = 2;
        }

        $tmp = DB::table('mobile_modification')
             ->where('type','=', $type)
             ->where('game_id','=', $game_id)
             ->selectRaw('count(1) as num')->get();
        if($tmp[0]->num == 0){
            $data = array(
                'game_id' => $game_id,
                'type' => $type,
                'data' => $data,
                'last_modified_time' => time()
            );
            $result = DB::table('mobile_modification')
                ->insert($data);
        }
        else{
            $modify = array(
                'data' => $data
            );
            $result = DB::table('mobile_modification')
                ->where('type','=', $type)
                ->where('game_id','=', $game_id)
                ->update($modify);
            }
        if($result){
            return Response::json(array('msg' => '提交成功'), 200);
        } 
    }

}