<?php

class MasterLogController extends \BaseController {

	public function getNewUrl(){
		$date = date('Y-m-d');
		echo "<script type='text/javascript'>window.location='/game-server-api/eb/log?date=$date&num=0'</script>";
	}

	public function getFile()
	{
		header('charset=Unicode');
		$date = Input::get('date');
		$num = Input::get('num');
		$show_num = Input::get('shownum');
		$show_num = $show_num ? $show_num : 1000;
		$find_str = Input::get('find_str');
		$log_file = storage_path().'/logs/eb-' . $date. '.log';
		if (!file_exists($log_file)) {
			return Response::json(array('error', 'File Not Found'), 404);
		}
		$log_file = escapeshellarg($log_file);
		if($find_str){
			$res = array(
				'content' => `grep -n $find_str $log_file`
			);
		}else{
			$res = array(
				'content' => `tail -n +$num $log_file | head -n $show_num`
			);
		}
		$arr = explode("\n", $res['content']);
		$i = 0;
		foreach ($arr as $v) {
		    echo '<pre>';
		    echo ++$i.' ';
		    print_r($v);
		    echo '</pre>';
		}
	}
}