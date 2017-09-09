<?php

/*
|--------------------------------------------------------------------------
| Register The Artisan Commands
|--------------------------------------------------------------------------
|
| Each available Artisan command must be registered with the console so
| that it is available to be called. We'll register every command so
| the console gets access to each of the command object instances.
|
*/

//Set Database Timezone
$timezone = Config::get('app.timezone');
if ($timezone) {
	date_default_timezone_set($timezone);
}
$mysql = Config::get('database.connections.mysql');
$diff_gmt = date('P');
$mysql['options'] = array(
	PDO::MYSQL_ATTR_INIT_COMMAND => "SET time_zone = '{$diff_gmt}'",	
);
Config::set('database.connections.mysql', $mysql);

//Commands 
Artisan::add(new ImportEconomyLogIntoDB);
Artisan::add(new ImportCreatePlayerLogIntoDB);
Artisan::add(new ImportLoginLogIntoDB);
Artisan::add(new ImportLevelUpLogIntoDB);
Artisan::add(new ImportLogDataIntoDB);
Artisan::add(new AnalyzeRetentionIntoDB);
Artisan::add(new AnalyzeAllRetentionIntoDB);
Artisan::add(new DayReport);
Artisan::add(new OpenServerCommand);
Artisan::add(new ImportGameLogIntoDB);
Artisan::add(new ImportPlayerActionLogIntoDB);
Artisan::add(new Activity);
Artisan::add(new ImportItemIntoDB);
Artisan::add(new ImportExpLogIntoDB);
Artisan::add(new MailToRechargeFail);
Artisan::add(new ImportDragonLogIntoDB);
Artisan::add(new RechargeFailAlert);
Artisan::add(new DailyVisitIntoDB);
Artisan::add(new GamePlayCommand);
Artisan::add(new PokerDataDaily);
//Artisan::add(new ImportZazenLogIntoDB);
Artisan::add(new ImportLogMatchIntoDB);
Artisan::add(new ImportSettleLogIntoDB);
Artisan::add(new ImportMingGeLogIntoDB);
Artisan::add(new ImportYYSGLogDataIntoDB);
Artisan::add(new ImportXLonelyLogIntoDB);
Artisan::add(new AdTable);
Artisan::add(new AnalyzeUserRetentionIntoDB);
Artisan::add(new ImportMNSGLogDataIntoDB);
Artisan::add(new ImportFLSGEconomyLogIntoDB);
Artisan::add(new MobileGameOnline);
Artisan::add(new ImportRingsLogIntoDB);
Artisan::add(new ImportPlayCountLogIntoDB);
Artisan::add(new ImportMNSGDataIntoDB);
Artisan::add(new AnalyzeChannelRetentionIntoDB);
Artisan::add(new CountOnlineIntoDB);
Artisan::add(new TestImportLogIntoDB);
Artisan::add(new ImportActivityDataIntoDB);
Artisan::add(new ImportFLSGRanksLogIntoDB);
Artisan::add(new PlatformDailyReport);
Artisan::add(new TimingOpenActivities);
Artisan::add(new OfficeMails);
Artisan::add(new MasterMonitor);
Artisan::add(new ImportCrowdFundingLogIntoDB);
Artisan::add(new ImportMergegemLogIntoDB);
Artisan::add(new ImportOperationLogIntoDB);
Artisan::add(new PayWarningCount);
Artisan::add(new MailPayCount);
Artisan::add(new ImportGoldRollingIntoDB);
Artisan::add(new RemoveLogFileIntoBak);
Artisan::add(new ImportShakeDiceLogIntoDB);
Artisan::add(new TimingUnban);