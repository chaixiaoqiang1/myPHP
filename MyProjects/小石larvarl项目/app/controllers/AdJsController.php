<?php

class AdJsController extends \BaseController {

	/**
	 * Display a listing of the resource.
	 *
	 * @return Response
	 */
	public function index()
	{
		$js = AdJs::all();
		foreach ($js as $key => $v) {
			if ($v->is_open) {
				$v->is_open = "开启";
			} else {
				$v->is_open = "关闭";
			} 
			if ($v->location == 1) {
				$v->location = "注册";
			} elseif ($v->location == 2) {
				$v->location = "创建";
			} elseif ($v->location == 3) {
				$v->location ="充值";
			}
			if ($v->type == 1) {
				$v->type = "GA";
			} elseif ($v->type == 2) {
				$v->type = "remarleting";
			} elseif ($v->type == 3) {
				$v->type = "conversion";
			}
		}
		$data = array(
		    'content' =>View::make('js.index', array('js' => $js))
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
		    'content' => View::make('js.create')
		);
		return View::make("main", $data);
	}

	/**
	 * Store a newly created resource in storage.
	 *
	 * @return Response
	 */
	public function store()
	{
		$msg = array(
		    'code'  => Config::get('errorcode.js_add'),
		    'error' => Lang::get('error.js_add')
		);
		$rules = array(
		    'js_name'     => 'required',
		    'js'     => 'required',
		    'is_open'     => 'required',
		    'location'    => 'required',
		    
 		);
		$validator = Validator::make(Input::all(),$rules);
		if ($validator->fails()) {
			return Response::json($msg, 403);
		} else {
			$valid = Input::get('valid');
			if ($valid == 1) {
				$source = Input::get('source');	
				$kk = 0;
				$arr = explode(",", $source);
			    $len = count($arr);
				for ($i=0; $i <$len ; $i++) { 
					$js = new AdJs;
					$js->js_name     = trim(Input::get('js_name'));
					$js->location    = trim(Input::get('location'));
					$js->source      = $arr[$i];
					$js->content     = trim(Input::get('js'));
					$js->is_open     = trim(Input::get("is_open"));
				    if ($js->save()) {
						$kk ++;
					}
				}
				if ($kk == $len) {
					 return Response::json(array('msg' => Lang::get('basic.create_success')));
				} else {
					return Response::json($msg,500);
				}
			} elseif ($valid == 2) {
			    $campaign = Input::get('campaign');	
				$kk = 0;
				$arr = explode(",", $campaign);
			    $len = count($arr);
				for ($i=0; $i <$len ; $i++) { 
					$js = new AdJs;
					$js->js_name     = trim(Input::get('js_name'));
					$js->location    = trim(Input::get('location'));
					$js->campaign    = $arr[$i];
					$js->content     = trim(Input::get('js'));
					$js->is_open     = trim(Input::get("is_open"));
				    if ($js->save()) {
						$kk ++;
					}
				}
				if ($kk == $len) {
					 return Response::json(array('msg' => Lang::get('basic.create_success')));
				} else {
					return Response::json($msg,500);
				}
			} elseif ($valid == 3) {
				 $source = Input::get('source');
				 $campaign = Input::get('campaign');	
				 $kk = 0;
				 $arr = explode(",", $campaign);
			     $len = count($arr);
				 for ($i=0; $i <$len ; $i++) { 
					 $js = new AdJs;
					 $js->js_name     = trim(Input::get('js_name'));
					 $js->location    = trim(Input::get('location'));
					 $js->campaign    = $arr[$i];
					 $js->source      = $source;  
					 $js->content     = trim(Input::get('js'));
					 $js->is_open     = trim(Input::get("is_open"));
				     if ($js->save()) {
					     $kk ++;
					 }
				 }
				 if ($kk == $len) {
					  return Response::json(array('msg' => Lang::get('basic.create_success')));
				 } else {
					 return Response::json($msg,500);
				 }
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
		$js = AdJs::find($id);
		if (!$js) {
			App:abort(404);
			exit;;
		}
		$js->content = addslashes($js->content); 
		$data = array(
		    'content' => View::make('js.edit', array('js' => $js))
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
		$js = AdJs::find($id);
		$msg = array(
		    'code'  => Config::get('errorcode.js_edit'),
		    'error' => Lang::get('error.js_edit')
 		);
		if (!$js) {
			return Response::json($msg,404);
			exit;
		}
		return $this->editJs($js, $msg);
	}
	 private function editJs($js, $msg)
	 {
	     $rules = array(
		     'js_name'     => 'required', 
		     'is_open'     => 'required',
		     'location'    => 'required',
		     'js'          => 'required',
		 );
		 $validator = Validator::make(Input::all(), $rules);
		 if ($validator->fails()) {
			 return Response::json($msg,403);
		 } else {
			 $js->js_name     = Input::get("js_name");
			 $js->source      = Input::get('source');
			 $js->campaign    = Input::get('campaign');
			 $js->is_open     = Input::get('is_open');
			 $js->content     = Input::get('content');
			 $js->location    = Input::get('location'); 
			 if ($js->save()) {
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
	   } elseif ($type == "source") {
		   return $this->getSource();
	   } elseif ($type == "campaign") {
		   return $this->getCampaign();
	   } elseif ($type == "lp") {
		  return $this->getLp();
	   } elseif ($type== "js") {
		   return $this->getJs();
	   }
   }
	
	public function getJs()
	{
		return Response::json($js);
	}

    public function openClose()
	{
		$js_id = Input::get('js_id');
		return Response::json($js_id);
	} 

}