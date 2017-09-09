<?php

use Illuminate\Console\Command;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Input\InputArgument;
error_reporting(E_ALL ^ E_DEPRECATED);
class Cdata extends Command {
    const TABLE_NAME = 'log_gameplay';
   
    private   $db = '';
    private   $db_name = '';     
    protected $name = 'Cdata';
    protected $description = 'C d a t a';
    
    public function __construct()
    {
        parent::__construct();
    }

    public function fire()
    {
        $player_id = $this->argument('player_id');
        $start_time = $this->argument('start_time');
        $end_time = $this->argument('end_time');
        $this ->db_name = "eastblue";
/*        $this->setDB();

        try{
            $this->db = DB::connection($this->db_name);
        }catch(\Exception $e){
            Log::error($e);
            $this->db->disableQueryLog();
        }
      //    $a = array();       
        //    $result = DB::select('select * from log_gameplay',array(1));
       //   Log::info('QQQQQQQQQQQQQQQQQQQQ' .$a(1));
            $result = mysql_query("select * from log_gameplay");
            while($row = mysql_fetch_array($result))
            {
                Log::info($row[TIME] . ' ' . $row[PLAYER_ID]);
            }  
  */
       $con =  mysql_connect("localhost","root","123456");        
       mysql_select_db("eastblue",$con);
       /*
        *
        *xian shi TOP ADDCHIP
        */
       if(mysql_query('create table tab_cp as select * from log_gameplay',$con))
       {
           Log::info('Copy Successed');
       }
       if(mysql_query('update log_gameplay set log_gameplay.V_CHIPS = (select tab_cp.WIN_CHIP from   tab_cp
                                                                                              where  tab_cp.GAME_ID = log_gameplay.GAME_ID
                                                                                              and    tab_cp.PLAYER_ID = '.$player_id.')',$con))
       {
           Log::info('Update Successed');
       }
       if($result = mysql_query('select GAME_TYPE,PLAYER_ID,sum(V_CHIPS/PLAYER_LOSENUM) from log_gameplay a
                                                                               where (a.WIN_CHIP = 0 and a.PLAYER_ID != '.$player_id.' and a.GAME_ID in 
                                                                                       (select distinct b.GAME_ID from log_gameplay b 
                                                                                                where b.PLAYER_ID = '.$player_id.' and b.TIME>'.$start_time.' and b.TIME<'.$end_time.'
                                                                                                 )) group by a.PLAYER_ID order by sum(V_CHIPS/PLAYER_LOSENUM)DESC',$con))
       {
           Log::info('select Successed');
       }
       if(mysql_query('drop table tab_cp',$con))
       {
          Log::info('Drop Successed');
       }
        while($row = mysql_fetch_array($result))
        {
            Log::info($row['PLAYER_ID'] . ' ' . $row[1]);
        }
       /*
        *
        *xian shi TOP DeductCHIP
        */
        if(mysql_query('create table tab_cp as select * from log_gameplay',$con))
        {   
          Log::info('Copy Successed222222222222222');
        }
        if(mysql_query('update log_gameplay set V_CHIPS = WIN_CHIP',$con))
        {
            Log::info('Update SuccessED22222222222');
        }
    /*    if($result = mysql_query('select PLAYER_ID , sum(V_CHIPS/(PLAYER_NUM-PLAYER_LOSENUM))  from log_gameplay a
                                                                        where(a.WIN_CHIP > 0 and a.GAME_ID  in
                                                                            (select distinct b.GAME_ID from log_gameplay b
                                                                                    where b.PLAYER_ID = '.$player_id.')) proup by a.PLAYER_ID',$con))
      */
       if($result = mysql_query('select GAME_TYPE,PLAYER_ID ,sum(V_CHIPS/(PLAYER_LOSENUM)) from log_gameplay a where(a.WIN_CHIP > 0 and a.PLAYER_ID != '.$player_id.' and a.GAME_ID in (select distinct b.GAME_ID from log_gameplay b where b.PLAYER_ID = '.$player_id.' and b.TIME>'.$start_time.' and b.TIME<'.$end_time.')) group by a.PLAYER_ID order by sum(V_CHIPS/(PLAYER_LOSENUM))DESC',$con)) 
        {
            Log::info('Select SUccessed');
        }
        if(mysql_query('drop table tab_cp',$con))
        {
            Log::info('Drop Successed');
        }
         while($row = mysql_fetch_array($result))
        {
        Log::info($row['PLAYER_ID'] . ' ' . $row[1]);
        } 
        /*
         *tongji meitian paiju xinxi
         *
         */         
         //select BLIND,count(distinct GAME_ID) from log_gameplay group by BLIND;
         $result = mysql_query('select GAME_TYPE,BLIND,count(distinct GAME_ID),count(distinct PLAYER_ID),sum(TABLE_FEE)/PLAYER_NUM from log_gameplay where TIME>'.$start_time.' and TIME<'.$end_time.' group by BLIND',$con);
         
         // select BLIND,count(distinct PLAYER_ID) from log_gameplay group by BLIND;
         // $result = mysql_query('select BLIND,count(distinct PLAYER_ID) from log_gameplay group by BLIND',$con);
         // select BLIND,sum(TABLE_FEE)/PLAYER_NUM from log_gameplay group by BLIND;
         // $result = mysql_query('select BLIND,sum(TABLE_FEE)/PLAYER_NUM from log_gameplay group by BLIND',$con);

         //***************************************
         //***************************************
       mysql_close($con);
    }

    private function setDB()
    {
        Config::set("database.connections.{$this->db_name}",array(
            'driver'    => 'mysql',
            'host'      => Config::get('database.connections.mysql.host'),
            'database'  => $this->db_name,
            'username'  => Config::get('database.connections.mysql.username'),
            'password'  => Config::get('database.connections.mysql.password'),
            'charset'   => 'utf8mb4',
            'collation' => 'utf8mb4_unicode_ci',
            'prefix'    => '',
                    ));
    }

protected function getArguments()
{
    return array(
                array('player_id',InputArgument::REQUIRED,'Require player id'),
                array('start_time',InputArgument::REQUIRED,'Require time'),
                array('end_time',InputArgument::REQUIRED,'Require time'),
            );
}

protected function getOptions()
{
    return array(
            array('a',null,InputOption::VALUE_OPTIONAL,'aaa'.null),
            );
}
}
