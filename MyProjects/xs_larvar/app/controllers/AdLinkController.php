<?php

class AdLinkController extends \BaseController {

	/**
	 * Display a listing of the resource.
	 *
	 * @return Response
	 */
	public function index()
	{
		$data = array(
		    'content' => View::make('links.index')
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
		$data = array(
		    'content' => View::make('links.create')
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
		    'code'  => Config::get('errorcode.link_add'),
		    'error' => Lang::get('error.link_add')
		);
		$rules = array(
		    'lp'          => 'required',
		    'campaign'    => 'required',
		    'source'      => 'required',
		    'term'        => 'required',
		    'game'     => 'required',
		);
		$validator = Validator::make(Input::all(),$rules);
		if ($validator->fails()) {
			return Response::json($msg, 403);
		}
		$link= new AdLink;
		$link->source_id    = trim(Input::get('source'));
		$link->campaign_id  = trim(Input::get('campaign'));
		$link->term_id      = trim(Input::get('term'));
		$link->game_id   = trim(Input::get('game'));
		$link->lp_id = trim(Input::get('lp'));
		if ($link->save()) {
			return Response::json(array('msg' => Lang::get('basic.edit_success')));
		} else {
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
		//
	}

	/**
	 * Show the form for editing the specified resource.
	 *
	 * @param  int  $id
	 * @return Response
	 */
	public function edit($id)
	{
		$campaign = AdCampaign::find($id);   
		$lp_id = AdLink::where('campaign_id', $id)->pluck('lp_id');
		$lp = AdLp::where('game_id', Session::get('game_id'))->get();
		if (!$lp || !$campaign || !$lp_id) {
			App::abort(404);
			exit;
		}
		
		$data = array(
		    'content' => View::make('links.edit', array('id' => $id, 'lp' => $lp, 'campaign' => $campaign, 'lp_id' => $lp_id))
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
		$link = AdLink::where('campaign_id', $id)->first();
		$msg = array(
		    'code'  => Config::get('errorcode.adlink_edit'),
		    'error' => Lang::get('error.adlink_edit')
 		);
		if (!$link) {
			return Response::json($msg,404);
			exit;
		}
		return $this->editLink($link, $msg);
	}
	
	public  function editLink($link, $msg)
	{
		$rules = array(
		    'lp'          => 'required',
 		);
		$validator = Validator::make(Input::all(), $rules);
		if ($validator->fails()) {
			return Response::json($msg,403);
		} else {
			$link->lp_id = Input::get("lp"); 
			if ($link->save()) {
			    return Response::json(array('msg' => Lang::get('basic.edit_success')));
			} else {
				return Response::json($msg, 500);
			}
		}
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
	   } elseif ($type == "source1") {
		   return $this->getSource();
	   } elseif ($type == "campaign1") {
		   return $this->getCampaign();
	   }elseif ($type == "term"){
	   	    return $this->getTerm();
	   }elseif ($type == "lp") {
		  return $this->getLp();
	   }
   }
   public function getSource()
   {
		$game_id = Input::get('game');
	    $source = AdSource::where('game_id', '=',  $game_id)->get();
		//$campaign = array('campaign_id' => $campaign->campaign_id, 'campaign_name' => $campaign->campaign_name)
		$lp = AdLp::where('game_id', '=', $game_id)->get();
		$source = json_decode($source, true);
		$lp = json_decode($lp, True);
		$arr = array(
		    'source' => $source,
			'lp'     => $lp
		);
		return Response::json($arr);
	}
	
   public function getCampaign()
   {
		$source_id = Input::get('source');
	    $campaign = AdCampaign::where('source_id', '=', $source_id)->get();
		$lp = AdLp::where('source', '=', $source_id)->get();
		$campaign = json_decode($campaign,true);
		$lp = json_decode($lp,true);
		$arr = array(
		    'campaign' => $campaign,
		    'lp'       => $lp,
		);
		return Response::json($arr);
	}
   
   public function getTerm()
   {
		$campaign_id = Input::get('campaign');
	    $term = AdTerm::where('campaign_id', '=', $campaign_id)->get();
		$lp = AdLp::where('campaign', "=", $campaign_id)->get();
		$term = json_decode($term,true);
		$lp = json_decode($lp,true);
		$arr = array(
		    'term' => $term,
		    'lp'   => $lp,
		);
		return Response::json($arr);
	}

   
   
}