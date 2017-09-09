<?php

class SlaveApiLogFileController extends \BaseController {

	public function getNewUrl(){
		$date = date('Y-m-d');
		echo "<script type='text/javascript'>window.location='/slave-api/eb/log?date=$date&num=0'</script>";
	}
	public function getFile()
	{
		$date = (null!=Input::get('date')) ? Input::get('date') : date('Y-m-d');
		$num = (null!=Input::get('num')) ? (int)Input::get('num') : 0;
		$shownum = Input::get('shownum') ? Input::get('shownum') : 1000;
		$find_str = Input::get('find_str');

		$game = Game::find(Session::get('game_id'));

		$api = SlaveApi::connect($game->eb_api_url, $game->eb_api_key, $game->eb_api_secret_key);

		$response = $api->getLogFile($date, $num, $shownum, $find_str);

		if ($response->http_code != 200) {
			return $this->show_message($response->http_code, json_encode($response->body));
		}
		if (!isset($response->body->content)) {
			return $this->show_message($response->http_code, json_encode($response->body));
		}
		$arr = explode("\n", $response->body->content);
		$i = 0;
		foreach ($arr as $v) {
			echo '<pre>';
			echo ++$i.' ';
			print_r($v);
			echo '<pre>';
		}
	}

}