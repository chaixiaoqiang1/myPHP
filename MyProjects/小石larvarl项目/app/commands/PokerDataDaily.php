<?php

use Illuminate\Console\Command;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Input\InputArgument;

class PokerDataDaily extends Command {

	const FROM_EMAIL_POKER = 'cs@joyspade.com';
	const EMAIL_FLSG_TW = 'cs@game168.com.tw';

	const EMAIL_TO_QSUN = 'qsun@xinyoudi.com';
	const EMAIL_TO_JZFAN = 'jzfan@xinyoudi.com';
    const EMAIL_TO_HLCAI_Gmail = 'xydalarm@gmail.com';
	const EMAIL_TO_HLCAI = 'hlcai@xinyoudi.com';
	const EMAIL_TO_DORA = 'dora@xinyoudi.com';
	const EMAIL_TO_DLYU = 'dlyu@xinyoudi.com';
	const EMAIL_TO_LYZHU = 'lyzhu@xinyoudi.com';
	const EMAIL_TO_XIANQIN = 'bywang@xinyoudi.com';
	const EMAIL_TO_XIAOXIN = 'zgliu@xinyoudi.com';
	const EMAIL_TO_LUXI = 'cwei@xinyoudi.com';


	const EMAIL_TO_ZC_TEST = 'zczhang@xinyoudi.com';
	const EMAIL_TO_LS_TEST = 'jixsoong@gmail.com';
	const EMAIL_TO_QS_TEST = 'xydalarm@gmail.com';

	private $subject_list = array(
		11 => '台湾德州扑克数据日报',
		52 => '印尼德州扑克手游数据日报',
        57 => 'Eng德州扑克数据日报'
		);

	private $from_email_list = array(
		11 => self::FROM_EMAIL_POKER,
		52 => self::FROM_EMAIL_POKER,
        57 => self::FROM_EMAIL_POKER
		);

	private $email_to_list = array(
		self::EMAIL_TO_XIAOXIN,
		self::EMAIL_TO_XIANQIN,
        self::EMAIL_TO_JZFAN,
		self::EMAIL_TO_HLCAI,
        self::EMAIL_TO_HLCAI_Gmail,
		self::EMAIL_TO_DORA,
		self::EMAIL_TO_DLYU,
        self::EMAIL_TO_LYZHU,
        self::EMAIL_TO_LUXI,
        'qzhou@xinyoudi.com',
        'bywang@xinyoudiglobal.com',
        'cwei@xinyoudiglobal.com',
        'qzhou@xinyoudiglobal.com',
	);

	private $game_id_list = array(
		 11, 52,57
	);
	/**
	 * The console command name.
	 *
	 * @var string
	 */
	protected $name = 'poker:dataDaily';

	/**
	 * The console command description.
	 *
	 * @var string
	 */
	protected $description = 'Send Poker\'s data by daily.';

	/**
	 * Create a new command instance.
	 *
	 * @return void
	 */
	public function __construct()
	{
		parent::__construct();
	}

	/**
	 * Execute the console command.
	 *
	 * @return mixed
	 */
	public function fire()
	{
		//$this->interval = $this->argument('interval');
		$this->option = $this->option('option');
		$this->getPokerDataDaily();
	}

	private function getPokerDataDaily()
	{
		//$end_time = time();
		//$start_time = $end_time - $this->interval;

		foreach ($this->game_id_list as $game_id) {
			$from_email = '';
			$email_to = array();
			$email_subject = '';

			//检测game_id对应的发送方
			if (isset($this->from_email_list[$game_id])) {
				$from_email = $this->from_email_list[$game_id];
			} else {
				Log::info(var_export('game_id_list['.$game_id.'] not exist in from_email_list.', true));
				continue;
			}
			//判断是测试还是线上
			if($this->option=='test'){
				$email_to = array(self::EMAIL_TO_ZC_TEST, self::EMAIL_TO_LS_TEST, self::EMAIL_TO_QS_TEST);
			}elseif ($this->option=='release') {
				$email_to = $this->email_to_list;
			}else{
				Log::info(var_export('option option error.', true));
				continue;
			}
			//检查game_id对应的邮件主题
			if(isset($this->subject_list[$game_id]))
			{
				$email_subject = $this->subject_list[$game_id];
			} else {
				Log::info(var_export('game_id_list['.$game_id.'] not exist in email_subject_list.', true));
				return ;
			}

			$game = Game::find($game_id);
			if(!isset($game))
			{
				Log::info(var_export('can not find game_id '.$game_id, true));
				continue;
			}
			$server_internal_id = 1; //特殊的德州扑克
			$platform_id = $game->platform_id;
			$server = Server::getPlatformServerId($game_id, $server_internal_id)->first();
			$api = SlaveApi::connect($game->eb_api_url, $game->eb_api_key, $game->eb_api_secret_key);
			// Log::info('DataDaily---game_id-'.$game_id.'-platform_id-'.$platform_id.'-platform_server_id-'.$server->platform_server_id);
			if(isset($server->platform_server_id)){
				$response = $api->getPokerDataDailyFromSlave($game_id, $platform_id, $server_internal_id,
						$server->platform_server_id, $from_email, $email_to, $email_subject);
			}
		}
	}

	/**
	 * Get the console command arguments.
	 *
	 * @return array
	 */
	protected function getArguments()
	{
		return array(
			//array('interval', InputArgument::REQUIRED, 'Data within a period time (Unit-s).'),
		);
	}

	/**
	 * Get the console command options.
	 *
	 * @return array
	 */
	protected function getOptions()
	{
		return array(
			array('option', 'test/release', InputOption::VALUE_OPTIONAL, 'Debug or Release option.', null),
		);
	}

}
