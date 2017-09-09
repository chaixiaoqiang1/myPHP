<?php

class AdLpController extends \BaseController {

	/**
	 * Display a listing of the resource.
	 *
	 * @return Response
	 */
	public function index()
	{
	    $lp = AdLp::currentGameLps()->paginate(20);
		$data = array(
		    'content' => View::make('lps.index', array('lp' => $lp))
		);
		return View::make('main',$data);
	}

	/**
	 * Show the form for creating a new resource.
	 *
	 * @return Response
	 */
	public function create()
	{
		$lp = AdLp::orderBy('lp_id', 'desc')->first();
		if (!$lp) {
			$lp_id = 1;
		} else {
			$lp_id = $lp->lp_id + 1;
		}
		
		$game = Game::all();
		$data = array(
		    'content' => View::make('lps.create', array('lp_id' => $lp_id, 'game' => $game))
		);
		return View::make('main',$data);
	}

	/**
	 * Store a newly created resource in storage.
	 *
	 * @return Response
	 */
	public function store()
	{
		$msg = array(
		    'code'  => Config::get('errorcode.lp_add'),
		    'error' => Lang::get('error.lp_add')
		);
		$rules = array(
		    'lp_name'   => 'required',
		    'game'      => 'required',
		);
		$validator = Validator::make(Input::all(),$rules);
		if ($validator->fails()) {
			return Response::json($msg, 403);
		}
		$lp = new AdLp;
		$lp->lp_name = trim(Input::get('lp_name'));
		$lp->game_id = trim(Input::Get('game'));
		if ($lp->save() ) {
			 $lp_id = $lp->lp_id+1;
			 return Response::json(array('msg' => Lang::get('basic.create_success'), 'lp_id' => $lp_id));
		} else{
			return Response::json($msg, 500);
		}
		
	}
    

	
	/**
	 * Display the specified resource.
	 *
	 * @param  int  $id
	 * @return Response
	 */
	public function show($id)
	{
		$lp_id = Input::get('lp_id');
		$lp = AdLp::find($id);
		$data = array(
		    'content' => View::make('lps.show', array('lp' => $lp))
		);
		return View::make('main', $data);
	}

	/**
	 * Show the form for editing the specified resource.
	 *
	 * @param  int  $id
	 * @return Response
	 */
	public function edit($id)
	{
		$lp = AdLp::find($id);
		if (!$lp) {
			App::abort(404);
			exit;
		}
		
		$data = array(
		 'content' => View::make('lps.edit', array('lp' => $lp))
		);
		return View::make('main',$data);
	}

	/**
	 * Update the specified resource in storage.
	 *
	 * @param  int  $id
	 * @return Response
	 */
	public function update($id)
	{
		
	}
	
	

	/**
	 * Remove the specified resource from storage.
	 *
	 * @param  int  $id
	 * @return Response
	 */
	public function destroy($id)
	{
		
	}
   
   
   public function upload()
   {
   	    
		$destinationPath = '';
    	$filename        = '';
        $file            = Input::file('uploadedFile');
        $destinationPath = 'upload/';
        $filename        = $file->getClientOriginalName();
        $uploadSuccess   = $file->move($destinationPath, $filename);
    
	
	    if ($uploadSuccess) {
	        return Response::json("succeed");
	    }
   }

   public function getAction()
   {
   	   $type = Input::get('type');
	   if ($type == "game") {
	   	   return $this->getGame();
	   } elseif ($type == "source") {
		   return $this->getSource();
	   } elseif ($type == "campaign") {
		   return $this->getCampaign();
	   } elseif ($type == "lp") {
		   return $this->getLp();
	   }
   }

   public function getGame()
   {
       $platform_id = Input::get("platform");
	   $game = Game::orderBy('game_id', 'asc')->where('platform_id', $platform_id)->get();
	   return Response::json($game);
   }
   
   public function getSource()
   {
   	   $game_id = Input::get("game");
	   $source  = AdSource::where('game_id', $game_id)->get();
	   return Response::json($source);
   }
   
   public function getCampaign()
   {
       $source_id = Input::get("source");
	   $campaign = AdCampaign::where('source_id', $source_id)->get();
	   return Response::json($campaign);
   }
   
   public function getLp()
   {
	   $arr4 = array();
	   $game_id = Input::get('game');
	   if ($game_id ) {
		   $result = AdLink::where("game_id", $game_id)->get();
		   foreach ($result as $key => $v) {
			   array_push($arr4, $v->lp_id);
		   }
		   $lp = AdLp::whereIn('lp_id', $arr4)->get();		   
	   } 
	   return Response::json($lp);	
   }
    
}
?>