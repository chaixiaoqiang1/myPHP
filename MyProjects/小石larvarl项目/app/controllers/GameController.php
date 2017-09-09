<?php

class GameController extends \BaseController{

    /**
     * Display a listing of the resource.
     *
     * @return Response
     */
    public function index()
    {
        $data = array(
            'content' => View::make('games.index')
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
        $data = array(
            'content' => View::make('games.create')
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
            'code' => Config::get('errorcode.game_add'),
            'error' => Lang::get('error.game_add')
        );
        
        $platform = Platform::find((int) Input::get('platform_id'));
        if (! $platform) {
            Log::info('platform is not found');
            return Response::json($msg, 403);
        }
        
        $rules = array(
            'game_name' => 'required',
            'platform_id' => 'required',
			'game_code' => 'required',
            'game_type' => 'required',
        );
        $validator = Validator::make(Input::all(), $rules);
        
        if ($validator->fails()) {
            Log::info('Input is wrong.');
            return Response::json($msg, 403);
        } else {
            $game = new Game();
            $game->game_name = trim(Input::get('game_name'));
            $game->platform_id = (int) Input::get('platform_id');
            $game->is_recommend = (int) Input::get('is_recommend');
            $game->eb_api_url = trim(Input::get('eb_api_url'));
            $game->eb_api_key = trim(Input::get('eb_api_key'));
            $game->eb_api_secret_key = trim(Input::get('eb_api_secret_key'));
			$game->game_code = Input::get('game_code');
            $game->game_type = Input::get('game_type');
            if ($game->save()) {
                return Response::json(array(
                    'msg' => Lang::get('basic.create_success')
                ));
            } else {
                Log::info('new game save wrong.');
                Log::info(var_export($game->save()));
                return Response::json($msg, 500);
            }
        }
    }

    /**
     * Display the specified resource.
     *
     * @param int $id            
     * @return Response
     */
    public function show($id)
    {
        $game = Game::find($id);
        
        if (! $game) {
            App::abort(404);
            exit();
        }

       	$platform_id = Session::get('platform_id', $game->platform_id);	
		Session::forget('game_id');
		Session::forget('platform_id');
		Session::put('platform_id', $game->platform_id);
        Session::put('game_id', $game->game_id);
        if(1 == Auth::user()->login_times){ //首次登陆，跳转修改密码页面
            return Redirect::to('/users/'.Auth::user()->user_id.'/edit');
        }else{
            return Redirect::to('/');
        }
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param int $id            
     * @return Response
     */
    public function edit($id)
    {
        $game = Game::find($id);
        
        if (! $game) {
            App::abort(404);
            exit();
        }
        
        $data = array(
            'content' => View::make('games.edit', array(
                'game' => $game
            ))
        );
        return View::make('main', $data);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param int $id            
     * @return Response
     */
    public function update($id)
    {
        $msg = array(
            'code' => Config::get('errorcode.game_add'),
            'error' => Lang::get('error.game_edit')
        );
        
        $game = Game::find($id);
        if (! $game) {
            return Response::json($msg, 404);
            exit();
        }
        
        $platform = Platform::find((int) Input::get('platform_id'));
        
        if (! $platform) {
            return Response::json($msg, 404);
        }
        
        $rules = array(
			'game_name' => 'required',
			'game_code' => 'required:alpha',
            'game_type' => 'required'
        );
        $validator = Validator::make(Input::all(), $rules);
        
        if ($validator->fails()) {
            return Response::json($msg, 403);
        } else {
            $game->game_name = trim(Input::get('game_name'));
            $game->is_recommend = (int) Input::get('is_recommend');
            $game->platform_id = (int) Input::get('platform_id');
            $game->eb_api_url = trim(Input::get('eb_api_url'));
            $game->eb_api_key = trim(Input::get('eb_api_key'));
            $game->eb_api_secret_key = trim(Input::get('eb_api_secret_key'));
			$game->game_code = Input::get('game_code');
            $game->game_type = Input::get('game_type');
            if ($game->save()) {
                return Response::json(array(
                    'msg' => Lang::get('basic.edit_success')
                ));
            } else {
                return Response::json($msg, 500);
            }
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
        //
    }

    public function gameInformationIndex(){
        $game_id = Session::get('game_id'); 
        $platform_id = Session::get('platform_id');
        $game = Game::find($game_id);
        $api = SlaveApi::connect($game->eb_api_url, $game->eb_api_key, $game->eb_api_secret_key);
        $result = $api->getInformaitionData($game_id, $platform_id);
        if('200' != $result->http_code){
            return $this->show_message($result->http_code, json_encode($result->body));
        }
        $result = (array)$result->body;
        $data = array(
                'content' => View::make('gameInformation', array(
                    'result' => $result
                    )));
        return View::make('main', $data);
    }
}