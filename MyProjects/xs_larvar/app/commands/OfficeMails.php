<?php

use Illuminate\Console\Command;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Input\InputArgument;

class OfficeMails extends Command {

	protected $name = 'office:mails';

	/**
	 * The console command description.
	 *
	 * @var string
	 */
	protected $description = 'office mails';

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
		$server = $this->argument('server');
		$username = $this->argument('username');
		$password = $this->argument('password');
		$data = array(
            'server' => $server,
            'username' => $username,
            'password' => $password,			
		);

        $mail_driver = new AFLMailController;
        $mail_driver->connect($data);
        $date = date("Y-m-d", time()-86400);

        //请假邮件
        $condition = "SUBJECT Re:请假 SINCE ".$date;
        $mails = $mail_driver->getCentainMails($condition);
        if($mails){
            foreach ($mails as $value) {
                $empty = '';
                $bodyHtml  = $mail_driver->getBody($value, $empty, $empty, 1);
                $encoding = mb_detect_encoding($bodyHtml);
                if ($encoding != 'UTF-8') {
                    $bodyHtml = mb_convert_encoding($bodyHtml, 'UTF-8', 'gb2312');
                }
                $l_result = trim(substr($bodyHtml, strlen('<div>'), (strpos($bodyHtml, '</div>') - strlen('<div>'))));
                if($l_result != strip_tags($l_result)){ //异常的不含批复结果的邮件
                    continue;
                }
                $body = strip_tags($bodyHtml);
                $encoding = mb_detect_encoding($body);
                if ($encoding != 'UTF-8') {
                    $body = mb_convert_encoding($body, 'UTF-8', 'gb2312');
                }
                if(strpos($body, '请假人：')){
                    $strlen = strlen('请假人：');
                    $who_pos = strpos($body, '请假人：');
                }elseif(strpos($body, '请假人:')){
                    $strlen = strlen('请假人:');
                    $who_pos = strpos($body, '请假人:');
                }else{
                    $strlen = 0;
                    $who_pos = 0;
                }
                if(0 === strpos($body, '请假人')){ //如果邮件开始是请假人等，肯定是没有批复的邮件
                    continue;
                }
                $sender = trim(substr($body, ($who_pos + $strlen), (strpos($body, '请假时间') - ($who_pos + $strlen))));
                $encoding = mb_detect_encoding($sender);
                if ($encoding != 'UTF-8') {
                    $sender = mb_convert_encoding($sender, 'UTF-8', 'gb2312');
                }

                if(strpos($body, '请假时间：')){
                    $strlen = strlen('请假时间：');
                }elseif(strpos($body, '请假时间:')){
                    $strlen = strlen('请假时间:');
                }else{
                    $strlen = 0;
                }
                $l_time = trim(substr($body, (strpos($body, '请假时间') + $strlen), (strpos($body, '请假天数') - (strpos($body, '请假时间') + $strlen))));
                $encoding = mb_detect_encoding($l_time);
                if ($encoding != 'UTF-8') {
                    $l_time = mb_convert_encoding($l_time, 'UTF-8', 'gb2312');
                }

                if(strpos($body, '请假天数：')){
                    $strlen = strlen('请假天数：');
                }elseif(strpos($body, '请假天数:')){
                    $strlen = strlen('请假天数:');
                }else{
                    $strlen = 0;
                }
                $l_days = trim(substr($body, (strpos($body, '请假天数') + $strlen), (strpos($body, '请假原因') - (strpos($body, '请假天数') + $strlen))));
                $encoding = mb_detect_encoding($l_days);
                if ($encoding != 'UTF-8') {
                    $l_days = mb_convert_encoding($l_days, 'UTF-8', 'gb2312');
                }

                if(strpos($body, '请假原因：')){
                    $strlen = strlen('请假原因：');
                }elseif(strpos($body, '请假原因:')){
                    $strlen = strlen('请假原因:');
                }else{
                    $strlen = 0;
                }
                $l_reason = trim(substr($body, (strpos($body, '请假原因') + $strlen), (strpos($body, '请假类别') - (strpos($body, '请假原因') + $strlen))));
                $encoding = mb_detect_encoding($l_reason);
                if ($encoding != 'UTF-8') {
                    $l_reason = mb_convert_encoding($l_reason, 'UTF-8', 'gb2312');
                }

                if(strpos($body, '请假类别：')){
                    $strlen = strlen('请假类别：');
                }elseif(strpos($body, '请假类别:')){
                    $strlen = strlen('请假类别:');
                }else{
                    $strlen = 0;
                }
                $l_type = trim(substr($body, (strpos($body, '请假类别') + $strlen), strlen('事假')));
                $encoding = mb_detect_encoding($l_type);
                if ($encoding != 'UTF-8') {
                    $l_type = mb_convert_encoding($l_type, 'UTF-8', 'gb2312');
                }

                $header = $mail_driver->getHeader($value);
                $data2store = array(
                    'sender' => $sender,
                    'department' => '',
                    'operator' => isset($header['fromName']) ? $header['fromName'] : '',
                    'mail_time' => isset($header['udate']) ? $header['udate'] : '',
                    'mail_type' => 1,
                    'l_time' => $l_time,
                    'l_reason' => $l_reason,
                    'l_days' => $l_days,
                    'l_type' => $l_type,
                    'l_result' => $l_result,
                    'body' => $bodyHtml,
                    'created_at' => time(),
                    );
                if(!DB::table('Office_Mails')->where('mail_time', $data2store['mail_time'])->where('sender', $data2store['sender'])->where('operator', $data2store['operator'])->first()){
                    DB::table('Office_Mails')->insert($data2store);
                }
            }
        }
        //加班申请邮件
        $condition = "SUBJECT Re:加班 SINCE ".$date;
        $mails = $mail_driver->getCentainMails($condition);
        if($mails){
            foreach ($mails as $value) {
                $empty = '';
                $bodyHtml  = $mail_driver->getBody($value, $empty, $empty, 1);
                $encoding = mb_detect_encoding($bodyHtml);
                if ($encoding != 'UTF-8') {
                    $bodyHtml = mb_convert_encoding($bodyHtml, 'UTF-8', 'gb2312');
                }
                $l_result = trim(substr($bodyHtml, strlen('<div>'), (strpos($bodyHtml, '</div>') - strlen('<div>'))));
                if($l_result != strip_tags($l_result)){ //异常的不含批复结果的邮件
                    continue;
                }
                $body = strip_tags($bodyHtml);
                $encoding = mb_detect_encoding($body);
                if ($encoding != 'UTF-8') {
                    $body = mb_convert_encoding($body, 'UTF-8', 'gb2312');
                }
                if(strpos($body, '申请人：')){
                    $strlen = strlen('申请人：');
                    $who_pos = strpos($body, '申请人：');
                }elseif(strpos($body, '申请人:')){
                    $strlen = strlen('申请人:');
                    $who_pos = strpos($body, '申请人:');
                }else{
                    $strlen = 0;
                    $who_pos = 0;
                }
                if(0 === strpos($body, '申请人')){ //如果邮件开始是请假人等，肯定是没有批复的邮件
                    continue;
                }
                $sender = trim(substr($body, ($who_pos + $strlen), (strpos($body, '加班时间') - ($who_pos + $strlen))));
                $encoding = mb_detect_encoding($sender);
                if ($encoding != 'UTF-8') {
                    $sender = mb_convert_encoding($sender, 'UTF-8', 'gb2312');
                }

                if(strpos($body, '加班时间：')){
                    $strlen = strlen('加班时间：');
                }elseif(strpos($body, '加班时间:')){
                    $strlen = strlen('加班时间:');
                }else{
                    $strlen = 0;
                }
                $l_time = trim(substr($body, (strpos($body, '加班时间') + $strlen), (strpos($body, '加班天数') - (strpos($body, '加班时间') + $strlen))));
                $encoding = mb_detect_encoding($l_time);
                if ($encoding != 'UTF-8') {
                    $l_time = mb_convert_encoding($l_time, 'UTF-8', 'gb2312');
                }

                if(strpos($body, '加班天数：')){
                    $strlen = strlen('加班天数：');
                }elseif(strpos($body, '加班天数:')){
                    $strlen = strlen('加班天数:');
                }else{
                    $strlen = 0;
                }
                $l_days = trim(substr($body, (strpos($body, '加班天数') + $strlen), (strpos($body, '加班原因') - (strpos($body, '加班天数') + $strlen))));
                $encoding = mb_detect_encoding($l_days);
                if ($encoding != 'UTF-8') {
                    $l_days = mb_convert_encoding($l_days, 'UTF-8', 'gb2312');
                }

                if(strpos($body, '加班原因：')){
                    $strlen = strlen('加班原因：');
                }elseif(strpos($body, '加班原因:')){
                    $strlen = strlen('加班原因:');
                }else{
                    $strlen = 0;
                }
                $l_reason = trim(substr($body, (strpos($body, '加班原因') + $strlen)));
                $encoding = mb_detect_encoding($l_reason);
                if ($encoding != 'UTF-8') {
                    $l_reason = mb_convert_encoding($l_reason, 'UTF-8', 'gb2312');
                }

                $l_type = '加班';

                $header = $mail_driver->getHeader($value);
                $data2store = array(
                    'sender' => $sender,
                    'department' => '',
                    'operator' => isset($header['fromName']) ? $header['fromName'] : '',
                    'mail_time' => isset($header['udate']) ? $header['udate'] : '',
                    'mail_type' => 2,
                    'l_time' => $l_time,
                    'l_reason' => $l_reason,
                    'l_days' => $l_days,
                    'l_type' => $l_type,
                    'l_result' => $l_result,
                    'body' => $bodyHtml,
                    'created_at' => time(),
                    );
                if(!DB::table('Office_Mails')->where('mail_time', $data2store['mail_time'])->where('sender', $data2store['sender'])->where('operator', $data2store['operator'])->first()){
                    DB::table('Office_Mails')->insert($data2store);
                }
            }
        }
        //调班申请邮件
        $condition = "SUBJECT Re:调班 SINCE ".$date;
        $mails = $mail_driver->getCentainMails($condition);
        if($mails){
            foreach ($mails as $value) {
                $empty = '';
                $bodyHtml  = $mail_driver->getBody($value, $empty, $empty, 1);
                $encoding = mb_detect_encoding($bodyHtml);
                if ($encoding != 'UTF-8') {
                    $bodyHtml = mb_convert_encoding($bodyHtml, 'UTF-8', 'gb2312');
                }
                $l_result = trim(substr($bodyHtml, strlen('<div>'), (strpos($bodyHtml, '</div>') - strlen('<div>'))));
                if($l_result != strip_tags($l_result)){ //异常的不含批复结果的邮件
                    continue;
                }
                $body = strip_tags($bodyHtml);
                $encoding = mb_detect_encoding($body);
                if ($encoding != 'UTF-8') {
                    $body = mb_convert_encoding($body, 'UTF-8', 'gb2312');
                }
                if(strpos($body, '申请人：')){
                    $strlen = strlen('申请人：');
                    $who_pos = strpos($body, '申请人：');
                }elseif(strpos($body, '申请人:')){
                    $strlen = strlen('申请人:');
                    $who_pos = strpos($body, '申请人:');
                }else{
                    $strlen = 0;
                    $who_pos = 0;
                }
                if(0 === strpos($body, '申请人')){ //如果邮件开始是请假人等，肯定是没有批复的邮件
                    continue;
                }
                $sender = trim(substr($body, ($who_pos + $strlen), (strpos($body, '原班时间') - ($who_pos + $strlen))));
                $encoding = mb_detect_encoding($sender);
                if ($encoding != 'UTF-8') {
                    $sender = mb_convert_encoding($sender, 'UTF-8', 'gb2312');
                }

                if(strpos($body, '原班时间：')){
                    $strlen = strlen('原班时间：');
                }elseif(strpos($body, '原班时间:')){
                    $strlen = strlen('原班时间:');
                }else{
                    $strlen = 0;
                }
                $l_time = trim(substr($body, (strpos($body, '原班时间')), (strpos($body, '新班时间') - (strpos($body, '原班时间')))));
                $encoding = mb_detect_encoding($l_time);
                if ($encoding != 'UTF-8') {
                    $l_time = mb_convert_encoding($l_time, 'UTF-8', 'gb2312');
                }

                if(strpos($body, '新班时间：')){
                    $strlen = strlen('新班时间：');
                }elseif(strpos($body, '新班时间:')){
                    $strlen = strlen('新班时间:');
                }else{
                    $strlen = 0;
                }
                $l_time .= '--'.trim(substr($body, (strpos($body, '新班时间')), (strpos($body, '申请原因') - (strpos($body, '新班时间')))));
                $encoding = mb_detect_encoding($l_time);
                if ($encoding != 'UTF-8') {
                    $l_time = mb_convert_encoding($l_time, 'UTF-8', 'gb2312');
                }

                $l_days = '-';

                if(strpos($body, '申请原因：')){
                    $strlen = strlen('申请原因：');
                }elseif(strpos($body, '申请原因:')){
                    $strlen = strlen('申请原因:');
                }else{
                    $strlen = 0;
                }
                $l_reason = trim(substr($body, (strpos($body, '申请原因') + $strlen)));
                $encoding = mb_detect_encoding($l_reason);
                if ($encoding != 'UTF-8') {
                    $l_reason = mb_convert_encoding($l_reason, 'UTF-8', 'gb2312');
                }

                $l_type = '调班';

                $header = $mail_driver->getHeader($value);
                $data2store = array(
                    'sender' => $sender,
                    'department' => '',
                    'operator' => isset($header['fromName']) ? $header['fromName'] : '',
                    'mail_time' => isset($header['udate']) ? $header['udate'] : '',
                    'mail_type' => 3,
                    'l_time' => $l_time,
                    'l_reason' => $l_reason,
                    'l_days' => $l_days,
                    'l_type' => $l_type,
                    'l_result' => $l_result,
                    'body' => $bodyHtml,
                    'created_at' => time(),
                    );
                if(!DB::table('Office_Mails')->where('mail_time', $data2store['mail_time'])->where('sender', $data2store['sender'])->where('operator', $data2store['operator'])->first()){
                    DB::table('Office_Mails')->insert($data2store);
                }
            }
        }
		$mail_driver->close_mailbox();
	}

	/**
	 * Get the console command arguments.
	 *
	 * @return array
	 */
	protected function getArguments()
	{
		return array(
			array('server', InputArgument::REQUIRED, 'Server'),
			array('username', InputArgument::REQUIRED, 'Username'),
			array('password', InputArgument::REQUIRED, 'Password'),
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
			array('example', null, InputOption::VALUE_OPTIONAL, 'An example option.', null),
		);
	}

}
