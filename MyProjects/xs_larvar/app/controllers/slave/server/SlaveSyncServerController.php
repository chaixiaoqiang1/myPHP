<?php 

class SlaveSyncServerController extends \SlaveServerBaseController{
	const DB_YY = 'yy';

	private $db_yy = 'yy';

	public function sync()
	{
		if ($this->platform_id === 4) {
			$this->db_yy = 'yyid';
		}

		$platform_servers = DB::connection(self::DB_QIQIWU)
			->table('server_list')->get();

		$yy_servers = DB::connection(self::DB_YY)
			->table('server_list')->get();
			
		$server_list = array();

		foreach ($platform_servers as $k => $v) {
			$server_list[$v->server_internal_id] = $v;
		}

		foreach ($yy_servers as $k => $v) {
			if ($this->platform_id === 1) {
				$kk = $v->server_id + 6; 
			} else {
				$kk = $v->server_id;
			}
			if (isset($server_list[$kk])) {
				$server_list[$kk]->open_server_time = $v->created_time; 
				$server_list[$kk]->match_port = $v->game_port;
			}
		}
		return Response::json($server_list);
	}

	protected function setDB()
	{
		parent::setDB();
		Config::set("database.connections." . self::DB_YY, array(
			'driver'    => 'mysql',
			'host'      => Config::get('database.connections.mysql.host'),
			'database'  => $this->db_yy,
			'username'  => Config::get('database.connections.mysql.username'),
			'password'  => Config::get('database.connections.mysql.password'),
			'charset'   => 'utf8mb4',
			'collation' => 'utf8mb4_unicode_ci',
			'prefix'    => '',
		));
	}

}