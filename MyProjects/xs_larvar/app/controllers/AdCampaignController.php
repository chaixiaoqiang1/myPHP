<?php

class AdCampaignController extends \BaseController {

	/**
	 * Display a listing of the resource.
	 *
	 * @return Response
	 */
	public function index()
	{
		$source = AdSource::all();
		$data = array(
		    'content' => View::make('campaigns.index', array('source' => $source))
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
		$lp = AdLp::where('game_id', Session::get('game_id'))->get();
		$source = AdSource::all();
		$plats = Platform::get();
		if (!$plats || !$lp) {
			App::abort(404);
		}
		$data = array(
		    'content' => View::make('campaigns.create', array('plats' => $plats, 'lp' => $lp, 'source' => $source))
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
		    'code'  => Config::get('errorcode.campaign_add'),
		    'error' => Lang::get('error.campaign_add'), 
		);
		
		$rules = array(
		    'default_lp'      => 'required',
		);
		$validator = Validator::make(Input::all(), $rules);
		if ($validator->fails()) {
			return Response::json($msg, 403);
		} else {
			$campaign = new AdCampaign;
			$link = new AdLink;
			$link->source_id = trim(Input::get('source'));
			$link->lp_id  = trim(Input::get('default_lp'));
			$link->game_id  = trim(Input::get('game')); 
			$campaign->campaign_name  = trim(Input::get('campaign_name'));
			$campaign->campaign_value = trim(Input::get('campaign_value'));
			$campaign->source_id      = (int)Input::get('source');
			if ($campaign->save()) { 
			    $link->campaign_id = $campaign->campaign_id;
				if ($link->save()) {
				    return Response::json(array('msg' => Lang::get('basic.create_success')));
				}
			} else {
				return Response::json($msg,500);
			}
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
		
	}

	/**
	 * Show the form for editing the specified resource.
	 *
	 * @param  int  $id
	 * @return Response
	 */
	public function edit($id)
	{
		$link = AdLink::where('campaign_id', $id)->get();
		if (!$link) {
			App::abort(404);
			exit;
		}
		$data = array(
		 'content' => View::make('campaigns.edit', array('link' => $link))
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
		//
	}

	/**
	 * Remove the specified resource from storage.
	 *
	 * @param  int  $id
	 * @return Response
	 */
	 public function destroy($id)
	{
		//
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
	    } elseif ($type == "term") {
			return $this->getTerm();
		}
    }
	
    public function getGame()
    {
		$platform_id = Input::get('platform');
	    $game = Game::orderBy('game_id', 'asc')->where('platform_id',  $platform_id)->get();
		return Response::json($game);
	}
    
	public function getSource()
    {
		$game_id = Input::get('game');
	    $source = AdSource::where('game_id', $game_id)->get();
		$lp = AdLp::where('game_id', $game_id)->get();
		
		$source = json_decode($source, true);
		$lp = json_decode($lp, true);
		$arr = array(
		    'source' => $source,
		    'lp'     => $lp,
		);
		return Response::json($arr);
	}
	
	
	public function getTerm()
    {
	    $game_id = Input::get('game');
    	$source_id = Input::get('source');
	    $campaign_id = Input::get('campaign'); 
		if ($game_id && $source_id && $campaign_id) {
			$link = AdLink::whereRaw("game_id = $game_id and source_id = $source_id and campaign_id = $campaign_id")->get();
			foreach ($link as $key => $v) {
				$v->game_name = Game::where('game_id', $v->game_id)->pluck('game_name');
				$v->source_name = AdSource::where('source_id', $v->source_id)->pluck('source_value');
				$v->campaign_name = AdCampaign::where('campaign_id', $v->campaign_id)->pluck('campaign_value');
				$v->term_name = AdTerm::where('term_id', $v->term_id)->pluck('term_name');
				$v->term_value = AdTerm::where('term_id', $v->term_id)->pluck('term_value');
			}
			
		} elseif ($game_id && $source_id && empty($campaign_id)) {
			$link = AdLink::whereRaw(" game_id = $game_id and source_id = $source_id")->get();
			foreach ($link as $key => $v) {
				$v->game_name = Game::where('game_id', $v->game_id)->pluck('game_name');
				$v->source_name = AdSource::where('source_id', $v->source_id)->pluck('source_value');
				$v->campaign_name = AdCampaign::where('campaign_id', $v->campaign_id)->pluck('campaign_value');
				$v->term_name = AdTerm::where('term_id', $v->term_id)->pluck('term_name');
				$v->term_value = AdTerm::where('term_id', $v->term_id)->pluck('term_value');
			}
			
		} elseif ($game_id && empty($source_id) && empty($campaign_id)) {
			$link = AdLink::where("game_id", $game_id)->get();
			foreach ($link as $key => $v) {
				$v->game_name = Game::where('game_id', $v->game_id)->pluck('game_name');
				$v->source_name = AdSource::where('source_id', $v->source_id)->pluck('source_value');
				$v->campaign_name = AdCampaign::where('campaign_id', $v->campaign_id)->pluck('campaign_value');
				$v->term_name = AdTerm::where('term_id', $v->term_id)->pluck('term_name');
				$v->term_value = AdTerm::where('term_id', $v->term_id)->pluck('term_value');
			}
		}
		return Response::json($link);
	}
	
	public function getLp()
    {
    	$game_id = Input::get('game');
    	$source_id = Input::get('source');
	  
		
		if ($game_id && $source_id ) {
			$link = AdLink::whereRaw("game_id = $game_id and source_id = $source_id")->get();
			foreach ($link as $key => $v) {
				$v->game_name = Game::where('game_id', $v->game_id)->pluck('game_name');
				$v->source_name = AdSource::where('source_id', $v->source_id)->pluck('source_name');
				$v->campaign_name = AdCampaign::where('campaign_id', $v->campaign_id)->pluck('campaign_name');
				$v->lp_name = AdLp::where('lp_id', $v->lp_id)->pluck('lp_name');
			}
			
		} elseif ($game_id && empty($source_id)) {
			$link = AdLink::where('game_id',$game_id)->get();
			foreach ($link as $key => $v) {
				$v->game_name = Game::where('game_id', $v->game_id)->pluck('game_name');
				$v->source_name = AdSource::where('source_id', $v->source_id)->pluck('source_name');
				$v->campaign_name = AdCampaign::where('campaign_id', $v->campaign_id)->pluck('campaign_name');
			    $v->lp_name = AdLp::where('lp_id', $v->lp_id)->pluck('lp_name');
			}
			
		}
		return Response::json($link);
	}
	
	

}