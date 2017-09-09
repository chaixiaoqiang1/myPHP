<?php

class PlatformHelperController extends \BaseController
{
	public function helperIndex(){
		$game_id = Session::get('game_id');
		$platform_id = Session::get('platform_id');

		$platform = Platform::find($platform_id);

		$platform_api = PlatformApi::connect($platform->platform_api_url, $platform->api_key, $platform->api_secret_key);

		$data = array(
				'game_id' => $game_id,
				'time' => time(),
				);
		$result = $platform_api->get_helper_functions($data);

		if('200' == $result->http_code){
			if(isset($result->body->functions_info)){
				$view_data = $result->body->functions_info;
				$data = array(
	                'content' => View::make('platformapi.helper.helperindex', 
	                        array(
	                                'view_data' => $view_data,
	                                'link' => $platform->platform_api_url.'/upload_img/helper',
	                        ))
		        );
		        return View::make('main', $data);
	        }else{
	        	return $this->show_message($result->http_code, 'bad_structure-'.json_encode($result->body));
	        }
		}else{
			return $this->show_message($result->http_code, ''.json_encode($result->body));
		}
	}

	public function helperFunctionModify(){
		$data = array(
			'id' => (int)Input::get('id'),
			'name' => Input::get('name'),
			'ico_name' => Input::get('ico_name'),
			'ico_version' => Input::get('ico_version'),
			'is_open' => (Input::get('is_open') ? 1 : 0),
			'description' => Input::get('description'),
			'type' => Input::get('type'),
			'game_id' => (int)Session::get('game_id'),
			'time' => time(),
		);
		$game_id = Session::get('game_id');
		$platform_id = Session::get('platform_id');
		$platform = Platform::find($platform_id);
		$platform_api = PlatformApi::connect($platform->platform_api_url, $platform->api_key, $platform->api_secret_key);

		$function_data = Input::get('data');
		$function_data = explode(';', $function_data);
		$data['data'] = array();
		foreach ($function_data as $key => $value) {
			$tmp = explode('=>', trim($value));
			if(count($tmp) != 2){
				continue;
			}
			$data['data'][$tmp[0]] = $tmp[1];
			unset($tmp);
		}

		if(in_array($data['type'], array(4))){
			$data['data']['picture_name'] = $game_id.'_'.time();
			$data['data']['picture_version'] = 1;
			$data['data']['is_open'] = 0;
		}

		if(Input::get('update')){
			if(in_array($data['type'], array(1,4))){
				$data['data'] = array();
			}
		}
		if(Input::get('add')){
			$data['ico_name'] = ((int)Session::get('game_id')).'_'.((int)Input::get('id')).'_'.((int)Input::get('type'));
			$data['ico_version'] = 1;
		}
		$data['data'] = json_encode($data['data']);

		if(Input::get('update')){
			$result = $platform_api->update_helper_function($data);
		}elseif(Input::get('add')){
			$result = $platform_api->set_hepler_function($data);
		}

		if(200 == $result->http_code){
			return Response::json($result->body);
		}else{
			return Response::json($result->body, $result->http_code);
		}
	}

	public function helpersinglefunction(){
		$id = (int)Input::get('id');
		$type = (int)Input::get('type');
		$game_id = (int)Session::get('game_id');
		$data = array(
			'id' => $id,
			'type' => $type,
			'game_id' => $game_id,
			'time' => time(),
			);

		$platform_id = Session::get('platform_id');
		$platform = Platform::find($platform_id);
		$platform_api = PlatformApi::connect($platform->platform_api_url, $platform->api_key, $platform->api_secret_key);

		$result = $platform_api->get_helper_function_data($data);

		if(200 == $result->http_code){
			if(isset($result->body->data)){
				$view_data = $result->body->data;
				try{
					$data = array(
		                'content' => View::make('platformapi.helper.helperType'.$type.'Index', 
		                        array(
	                                'view_data' => $view_data,
	                                'type' => $type,
	                                'id' => $id,
	                                'link' => $platform->platform_api_url.'/upload_img/helper',
		                        ))
			        );
		        }catch (\Exception $e) {
		        	return $this->show_message('--', substr($e, 0, 200));
		        }
		        return View::make('main', $data);
	        }else{
	        	return $this->show_message($result->http_code, 'bad_structure-'.json_encode($result->body));
	        }
		}else{
			return Response::json($result->body, $result->http_code);
		}
	}

	public function SingleFunctionDeal(){
		$function_id = (int)Input::get('function_id');
		$type = (int)Input::get('type');
		$game_id = (int)Session::get('game_id');
		$data = array(
			'function_id' => $function_id,
			'type' => $type,
			'game_id' => $game_id,
			'time' => time(),
				);
		if('1' == $type){	//答疑类
			$data['data'] = array(
				'id' => (int)Input::get('id'),
				'answer_content' => Input::get('answer'),
				'answer_time' => time(),
				);
		}elseif('4' == $type){	//公告类
			$data['data'] = array(
				'id' => (int)Input::get('id'),
				'name' => Input::get('name'),
				'is_open' => Input::get('is_open'),
				'picture_name' => $game_id.'_'.time(),
				'picture_version' => 1,
				);
		}else{
			return Response::json(array('error'=>'Not a support type!'), 403);
		}

		$data['data'] = json_encode($data['data']);

		$platform_id = Session::get('platform_id');
		$platform = Platform::find($platform_id);
		$platform_api = PlatformApi::connect($platform->platform_api_url, $platform->api_key, $platform->api_secret_key);

		$result = $platform_api->update_hepler_function_data($data);

		if(200 == $result->http_code){
			return Response::json($result->body);
		}else{
			return Response::json($result->body, $result->http_code);
		}
	}
}