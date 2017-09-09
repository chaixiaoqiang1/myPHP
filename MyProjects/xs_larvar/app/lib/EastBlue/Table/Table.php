<?php namespace EastBlue\Table;
use \Log;

class Table implements TableInterface{
	
	private $path = '';
	private $data = array();

	public function init($path)
	{
		$this->path = $path;
		$this->getFileData();
		return $this;
	}

	public function initarray($path)
	{
		$this->path = $path;
		$this->getFileDataasArray();
		return $this;
	}

	private function getFileData()
	{
		if(!file_exists($this->path)){
			$data = '';
			$encoding = mb_detect_encoding($data);
		}else{
			$data = file_get_contents($this->path);
			$encoding = mb_detect_encoding($data);
		}
		if ($encoding != 'UTF-8') {
			$data = iconv('UTF-16', 'UTF-8', $data);
		}
		$this->handleFile($data);
	}

	private function getFileDataasArray()
	{
		$data = file_get_contents($this->path);
		$encoding = mb_detect_encoding($data);
		if ($encoding != 'UTF-8') {
			$data = iconv('UTF-16', 'UTF-8', $data);
		}
		$this->handleFiletoArray($data);
	}

	private function handleFile($data)
	{
		$data = explode("\n", $data);
		if (!$data) {
			$data = explode("\r\n", $data);
		}
		$arr = array();
		$keys = array();
		foreach ($data as $k => $v) {
			if ($k == 0) {
				continue;
			}
			if ($k == 1) {
				$keys = explode("\t", $v);
				foreach ($keys as &$v) {
					$v = trim($v);
				}
				unset($v);
				continue;
			}
			$values = explode("\t", $v);
			if (count($values) == count($keys)) {
				foreach ($values as &$v) {
					$v = trim($v);
				}
				unset($v);
				$arr[] = (object)array_combine($keys, $values);
			}elseif (count($values) < count($keys)) {
				foreach ($values as &$v) {
					$v = trim($v);
				}
				unset($v);
				while(count($values) < count($keys)){
					$values[] = '';
				}
				if(count($values) == count($keys)){
					$arr[] = (object)array_combine($keys, $values);
				}else{
					$arr[] = (object)array('error in Table');
				}
			} else {
				foreach ($values as &$v) {
					$v = trim($v);
				}
				unset($v);
				while(count($values) > count($keys)){
					array_pop($values);
				}
				if(count($values) == count($keys)){
					$arr[] = (object)array_combine($keys, $values);
				}else{
					$arr[] = (object)array('error in Table');
				}
			}

		}
		$this->data = (object)$arr;
	}

	private function handleFiletoArray($data)
	{
		$data = explode("\n", $data);
		if (!$data) {
			$data = explode("\r\n", $data);
		}
		$arr = array();
		$keys = array();
		foreach ($data as $k => $v) {
			if ($k == 0) {
				continue;
			}
			if ($k == 1) {
				$keys = explode("\t", $v);
				foreach ($keys as &$v) {
					$v = trim($v);
				}
				unset($v);
				continue;
			}
			$values = explode("\t", $v);
			if (count($values) == count($keys)) {
				foreach ($values as &$v) {
					$v = trim($v);
				}
				unset($v);
				$arr[] = array_combine($keys, $values);
			}elseif (count($values) < count($keys)) {
				foreach ($values as &$v) {
					$v = trim($v);
				}
				unset($v);
				while(count($values) < count($keys)){
					$values[] = '';
				}
				if(count($values) == count($keys)){
					$arr[] = array_combine($keys, $values);
				}else{
					$arr[] = array('error in Table');
				}
			} else {
				foreach ($values as &$v) {
					$v = trim($v);
				}
				unset($v);
				while(count($values) > count($keys)){
					array_pop($values);
				}
				if(count($values) == count($keys)){
					$arr[] = array_combine($keys, $values);
				}else{
					$arr[] = array('error in Table');
				}
			}

		}
		$this->data = $arr;
	}

	public function getData()
	{
		return $this->data;
	}

	//列表中追加内容
	public function addData($message_arr){
		$message= implode("\t", $message_arr);
		$message = "\n" . $message;
		$res = file_put_contents($this->path, $message, FILE_APPEND);
		return $res;
	}

	//删除内玩
	public function deleteNeiWan($message_arr){	//取出每一行数据然后对比删除，之后重新写入
		$data = file_get_contents($this->path);
		$encoding = mb_detect_encoding($data);

		if ($encoding != 'UTF-8') {
			$data = iconv('UTF-16', 'UTF-8', $data);
		}

		$data = explode("\n", $data);
		$next_line_key = "\n";
		if (!$data) {
			$data = explode("\r\n", $data);
			$next_line_key = "\r\n";
		}
		foreach ($data as $key => $value) {
			$value = explode("\t", $value);
			if($message_arr['game_id'] == $value[0] && $message_arr['neiwan_uid'] == $value[1]){
				unset($data[$key]);
			}
		}

		$res = file_put_contents($this->path, $data[0]);
		$res = file_put_contents($this->path, $next_line_key.$data[1], FILE_APPEND);

		foreach ($data as $key => $value) {
			if(!in_array($key, array(0,1))){
				$res = file_put_contents($this->path, $next_line_key.$value, FILE_APPEND);
			}
		}

		return $res;
	}
}