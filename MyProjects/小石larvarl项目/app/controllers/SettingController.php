<?php

class SettingController extends BaseController {

	
	public function showIp()
	{
		$msg = array(
		    'code'  =>Config::get('errorcode.organ_add'),
		    'error' =>Lang::get('error.organ_add')
		);
		$organ= Organization::find(Auth::user()->organization_id);
		$data = array(
			'content' => View::make('settings/allowed_ips', array(
				'organ' => $organ,
			))
		);
		return View::make('main', $data);
	}
    
	public function editIp($id)
	{
		$organ = Organization::find($id);
		$msg = array(
		    'code'    =>  Config::get('errorcode.organ_edit'),
		    'error'   =>  Lang::get('error.server_edit')
		);
		if (!$organ) {
			return Response::json($msg, 404);
		} 
		return $this->editOrgan($organ, $msg);
	} 
	
	private function editOrgan($organ, $msg)
	{
		$rules = array(
		    'allowed_ips'  => 'required'
		);
		$validator = Validator::make(Input::all(),$rules);
		if ($validator->fails()) {
			return Response::json($msg,404);
		} else {
			$organ->allowed_ips        =  trim(Input::get('allowed_ips'));
			if ($organ->save()) {
				return Response::json(array('msg' => Lang::get('basic.create_success')));
			} else {
				return Response::json($msg, 500);
			}  
		}
	}
	

}