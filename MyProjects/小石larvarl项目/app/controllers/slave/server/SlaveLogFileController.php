<?php

class SlaveLogFileController extends \BaseController {

	public function getFile()
	{
		$date = Input::get('date');
		$num = Input::get('num');
		$shownum = (int)Input::get('shownum');
		$find_str = Input::get('find_str');
		$log_file = storage_path().'/logs/eb-' . $date. '.log';
		if (!file_exists($log_file)) {
			return Response::json(array('error', 'File Not Found'), 404);
		}
		$log_file = escapeshellarg($log_file);
		if($num == -1) {
		    $res = array(
		            'content' => `awk !/Import/ $log_file`
		    );
		} else {
			if($find_str){
				$res = array(
						'content' => `grep -n $find_str $log_file`
				);
			}else{
			    $res = array(
			            'content' => `tail -n +$num $log_file | head -n $shownum`
			    );
		    }
		}
		return Response::json($res);
	}

}