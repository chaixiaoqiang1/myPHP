<?php

class AdTermController extends \BaseController {

	/**
	 * Display a listing of the resource.
	 *
	 * @return Response
	 */
	public function index()
	{
		$source = AdSource::all();
		$data = array(
		    'content' => View::make('terms.index', array('source' => $source))
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
		$game = Game::all();
		$source = AdSource::all();
		$data = array(
		    'content' => View::make('terms.create', array('game' => $game, 'source' => $source))
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
		    'campaign'    => 'required',
		    'source'      => 'required',
		    'term'        => 'required',
		);
		$validator = Validator::make(Input::all(),$rules);
		if ($validator->fails()) {
			return Response::json($msg, 403);
		} else {
			$terms = trim(Input::get('term'));
			$terms = explode("\n", $terms);
			$len = count($terms);
			$j = 0;
			for ($i=0; $i <$len ; $i++) {
				$term= new AdTerm;
			    $link = new AdLink; 
				$term->campaign_id  = trim(Input::get('campaign'));
				$termss = explode(",", $terms[$i]);
				$term->term_name    = trim($termss[0]);
				$term -> term_value = trim($termss[1]); 
			    if ($term->save()) {
			    	$link->game_id      = trim(Input::get("game"));
					$link->campaign_id  = trim(Input::get('campaign'));
			    	$link->source_id    = trim(Input::get('source'));
					$link->term_id = $term->term_id;
					$link->lp_id = trim($termss[2]);
					if ($link->save()) {
						$j++;
					}
				} 
			}
		    if ($j == $len) {
			    return Response::json(array('msg' => Lang::get('basic.create_success')));
		    } else {
			    return Response::json($msg, 500);
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
		$term = AdTerm::find($id);
		$lp = AdLink::where('term_id', $term->term_id)->pluck("lp_id");
		if (!$term) {
			App::abort(404);
			exit;
		}
		$data = array(
		    'content' => View::make('terms.edit', array('term' => $term, 'lp' => $lp))
		);
		return View::make('main', $data);
	}

	/**
	 * Update the specified resource in storage.
	 *
	 * @param  int  $id
	 * @return Response
	 */
	public function update($id)
	{
		$link = AdLink::where('term_id', $id)->first();
		$msg = array(
		    'code'  => Config::get('errorcode.term_edit'),
		    'error' => Lang::get('error.term_edit')
		);
		if (!$link) {
			return Response::json($msg,404);
			exit;
		}
		return $this->editTerm($link,$msg);
	}

    private function editTerm($link, $msg)
	{
		 $rules = array(
		    'lp_id' => 'required'
		);
		$validator = Validator::make(Input::all(), $rules);
		if ($validator->fails()) {
			return Response::json($msg, 403);
		}
		$link->lp_id = Input::get('lp_id');
		//return Response::json($link->lp_id);
		if ($link->save()) {
			return Response::json(array('msg' => Lang::get('basic.edit_success')));
		} else {
			return Response::json($msg, 500);
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
	
	public function getCampaign()
    {
		$source_id = Input::get('source');
	    $campaign = AdCampaign::where('source_id', $source_id)->get();
		return Response::json($campaign);
	}

}