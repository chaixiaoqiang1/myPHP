<?php

class SlaveMingGeLogController extends SlaveServerBaseController{

    const FILE_KEY = 'MingGeLog';
    const table_name = 'log_mingge';

    private $db_name_con = '';
    private $log_file = '';
    private $log_file_bak = '';

	public function importMingGeLog()
	{
		$this->db_name = Input::get('db_name');
		$this->log_file = Input::get('log_file');
		$this->log_file_bak = Input::get('log_file_bak');
		
		$this->setDB();
		$this->db_name_con = DB::connection($this->db_name);
		$db_name_sch = Schema::connection($this->db_name);

		//判断是否有log_mingge表格，没有建一个
        if (!$db_name_sch->hasTable('log_mingge'))
        {
            $db_name_sch->create('log_mingge', function($table)
            {
                $table->increments('id');
            });
            $db_name_sch->table('log_mingge', function($table)
            {
            	$table->integer('operatorID');
            	$table->integer('serverID');
            	$table->integer('playerID');
            	$table->integer('action_type');
            	$table->integer('time');
            	$table->integer('from_id');
            	$table->integer('from_exp');
            	$table->integer('to_id');
            	$table->integer('to_exp');
            	$table->integer('roleID');
            });
        }

        //读目录
        $files = scandir($this->log_file);
        if($files==false){
            //目录不存在
            Log::info('No such file path as '.$this->log_file);
            return Response::json(array('status'=>1));
        }
        if(count($files)==2){
            //空目录
            return Response::json(array('status'=>2));
        }

        $file_to_read = array();
        foreach ($files as $v_file) {
            if(strpos($v_file, self::FILE_KEY) != false){
            	$tmp = explode('.', $v_file)
                $file_to_read[$tmp[1]] = $v_file;
            }
        }
        ksort($file_to_read);
        $this->readLog($file_to_read);
        return Response::json(array('status'=>0));
	}

	private function readLog($file_arr)
    {
    	foreach ($file_arr as $filename) {
    		try{
    			$file_handle = fopen($filename, 'r')
    		}catch(Exception $e){
    			Log::error(' Can not open this file. ' . $filename );
    			continue;
    		}
    		while ($line = fgets($file_handle)) {
    			$log = explode("\t", trim($line));
			    $this->insertData($log);
    		}
    		fclose($file_handle);
    		$this->bakFile($filename);
    	}
   		return; 
    }

    private function insertData($log)
    {
        $key_array = array(
			'operatorID',
			'serverID',
			'playerID',
			'action_type',
			'time',
			'from_id',
			'from_exp',
			'to_id',
			'to_exp',
			'roleID',
        );
        $log_info = array_combine($key_array, $log);

		foreach ($log_info as $k => $v) {
			if (is_null($v)) {
				$log_info[$k] = 0;
			}
		}

		$this->db_name_con->beginTransaction();
		try {
			$this->db_name_con->table(self::table_name)->insert($log_info);
		} catch (\Exception $e) {
			Log::error(self::table_name . ' Insert Failed' . json_encode($log_info) . "\n");
			$this->db_name_con->rollback();
		}

    }

    private function bakFile($file)
    {
    	$from = $this->log_file . $file;
        if (! file_exists($this->log_file_bak))
        {
            mkdir($this->log_file_bak);
        }
        $to = $this->log_file_bak . $file;
        try
        {
            rename($from, $to);
        }
        catch (\Exception $e)
        {
            Log::error($e);
        }
    }

}