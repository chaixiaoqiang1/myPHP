<?php

use Illuminate\Console\Command;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Input\InputArgument;

class TestImportLogIntoDB extends Command {
    private function fromMail($game_id){
        if(67 == $game_id){
            return 'cs@mangaloandau.com';
        }elseif(2 == $game_id){
            return 'cs@vnwebgame.com';
        }elseif(36 == $game_id){
            return 'cs@nuthankiem.com';
        }
        else{
            return 'game@vnweb.com';
        }
    }
    /**
     * The console command name.
     *
     * @var string
     */
    protected $name = 'mail:test';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'test Mail.';

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
        $game_id = $this->argument('game_id');
        if(!$game_id){
            return;
        }
        $this->sendMail($game_id);
    }

    private function sendMail($game_id)
    {
        Log::info('vn mail test');
        $mail_subject = '印尼邮件测试';    //主题
        $from_email = $this->fromMail($game_id);
        $mail_data['mail_ok_msg'] = '越南风流三国日报测试';
        $email_to = 'xfwang@xinyoudi.com';
        Mail::send('timingActivity', $mail_data, function($message) use ($from_email, $email_to, $mail_subject) {
            $message->subject($mail_subject);
            $message->from($from_email, 'cs');
            $message->to($email_to);
        });
        $this->info('send mail success！');
    }

    /**
     * Get the console command arguments.
     *
     * @return array
     */
    protected function getArguments()
    {
        return array(
            array('game_id', InputArgument::REQUIRED, 'game_id'),
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
