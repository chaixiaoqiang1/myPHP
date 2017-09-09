<?php

/*
|--------------------------------------------------------------------------
| Application Routes
|--------------------------------------------------------------------------
|
| Here is where you can register all of the routes for an application.
| It's a breeze. Simply tell Laravel the URIs it should respond to
| and give it the Closure to execute when that URI is requested.
|
*/


//Slave Server 
Route::group(array(
	'prefix' => 'slave/api/v1',
	'before' =>	'slave_api_key|slave_api_sign'
	), function() {
		Route::get('player/log', 'SlaveCreatePlayerLogController@getLog');
		Route::get('player/getidbyname', 'SlaveCreatePlayerLogController@getidbyname');
		Route::get('player/getnamebyid', 'SlaveCreatePlayerLogController@getnamebyid');
		Route::get('economy/log', 'SlaveEconomyLogController@getLog');
		Route::get('login/log', 'SlaveLoginLogController@getLog');
		Route::get('login/trend', 'SlaveLoginLogController@getTrendByTime');
		Route::get('login/total', 'SlaveLoginLogController@getLoginTotalByTime');
		Route::get('player/playerinfo', 'SlaveCreatePlayerLogController@getCreatePlayerInfo');
		Route::get('player/rank', 'SlaveCreatePlayerLogController@getLevelRank');
		Route::get('player/trend', 'SlaveCreatePlayerLogController@getLevelTrend');
		Route::get('player/created', 'SlaveCreatePlayerLogController@getCreatedNumberByTime');
		Route::get('player/retention', 'SlaveCreatePlayerLogController@getRetention');
		Route::get('player/channel/retention', 'SlaveCreatePlayerLogController@getChannelRetention');
		Route::get('player/economy', 'SlaveEconomyLogController@getPlayerEconomy');
		Route::get('player/simple-economy', 'SlaveEconomyLogController@getSimplePlayerEconomy');
		Route::get('player/simple-total', 'SlaveEconomyLogController@getSimplePlayerEconomyTotal');
		Route::get('player/all-economy', 'SlaveEconomyLogController@getAllPlayerEconomy');
		Route::get('player/economy/stat', 'SlaveEconomyLogController@getPlayerEconomyStatistics');
		Route::get('player/economy/rank', 'SlaveEconomyLogController@getPlayerEconomyRank');
		Route::get('player/economy/analysis', 'SlaveEconomyLogController@getPlayerEconomyAnalysis');
		Route::get('player/economy/find-boss-killer', 'SlaveEconomyLogController@findBossKiller');
		Route::get('player/levelup', 'SlaveLevelUpLogController@getPlayerLevelUp');
		Route::get('server/economy', 'SlaveEconomyLogController@getServerEconomyStatistics');
		Route::get('server/opened', 'SlaveOpenServerController@execScript');
		Route::get('project/release', 'SlaveReleaseProjectController@release');
		Route::get('project/check', 'SlaveReleaseProjectController@check');
		Route::get('server/sync', 'SlaveSyncServerController@sync');
		Route::get('payment/order/stat/server', 'SlavePaymentController@getServerOrderStatistics');
		Route::get('payment/order/stat/game', 'SlavePaymentController@getGameOrderStatistics');
		Route::get('payment/order/order_sn', 'SlavePaymentController@getOrderByOrderSN');
		Route::get('payment/order/order_id', 'SlavePaymentController@getOrderByOrderID');
		Route::get('payment/order/lucky-order_sn', 'SlavePaymentController@getLuckyOrderSN');
		Route::get('payment/order/tradeseq', 'SlavePaymentController@getOrderByTradeseq');
		Route::get('payment/order', 'SlavePaymentController@getOrders');
		Route::get('payment/all-order', 'SlavePaymentController@getAllOrders');
		Route::get('payment/order/user', 'SlavePaymentController@getUserOrder');
		Route::get('payment/order/unpay', 'SlavePaymentController@getUnPayOrders');
		Route::get('payment/order/players-in-trouble', 'SlavePaymentController@getPlayersInTrouble');
		
		Route::get('payment/order/rank', 'SlavePaymentController@getYuanbaoRank');
		Route::get('payment/order/mgrank', 'SlavePaymentController@getYuanbaoRankForMG');
		Route::get('payment/order/all-rank', 'SlavePaymentController@getAllYuanbaoRank');
		Route::get('payment/order/dispute', 'SlavePaymentController@getFBDisputeOrders');
		Route::get('payment/order/refund', 'SlavePaymentController@getRefundOrders');
		Route::get('payment/pay-type', 'SlavePaymentController@getPayTypeStat');
		Route::get('payment/server/revenue', 'SlavePaymentController@getServerRevenueByDay');
		Route::get('user/device', 'SlaveUserController@getUserDevice');
		Route::get('user/stat', 'SlaveUserController@getStat');
		Route::get('user/statyy', 'SlaveUserController@getStatyy');
		Route::get('user/stat/ad', 'SlaveUserController@getAdStat');
		Route::get('user/sxd/stat', 'SlaveUserController@SXDGetStat');
		Route::get('user', 'SlaveUserController@getUser');
		Route::get('user/player', 'SlaveUserController@getUserByPlayer');
		Route::get('user/neiwan', 'SlaveUserController@neiwan');
		Route::get('user/player/stat', 'SlaveUserController@getCreatePlayerStat');
		Route::get('user/channel/order', 'SlaveUserController@getChannelOrderStat');
		Route::get('user/channel/retention', 'SlaveUserController@getChannelRetentionStat');
		Route::get('ad/fb', 'SlaveAdReportController@getFBStat');
		Route::get('ad/sxd/fb', 'SlaveAdReportController@SXDGetFBStat');
		Route::get('user/weekly', 'SlaveUserController@getStatOverServers');
		Route::get('eb/log', 'SlaveLogFileController@getFile');

		Route::get('platform/exchange-rate', 'SlavePaymentController@getExchangeRate');
		Route::get('platform/pay-type', 'SlavePaymentController@getPayType');
		Route::get('platform/merchant-data', 'SlavePaymentController@getMerchantData');
		Route::get('platform/pay-method', 'SlavePaymentController@getPayMethod');
		Route::get('platform/pay-currency', 'SlavePaymentController@getPayCurrency');
		Route::get('platform/pay-amount','SlavePaymentController@getPayAmount');

		Route::get('user/th/stat', 'SlaveUserController@THGetStat');
		Route::get('ad/th/fb', 'SlaveUserController@THGetFBStat');

		//查询时间段内设备新增用户
		Route::get('user/device-search', 'SlaveUserController@getUserDeviceInfo');
		Route::get('user/device-player-search', 'SlaveUserController@getDevicePlayerInfo');

		//德州扑克新需求
		Route::get('poker/payment/order', 'SlavePaymentController@getPokerOrderStat');
		Route::get('poker/payment/oldpays', 'SlavePaymentController@getPokerOldPays');
		Route::get('poker/user/logday', 'SlavePaymentController@getPokerLogDay');
		Route::get('poker/user/paydays', 'SlavePaymentController@getPokerPaypayDays');
		Route::get('poker/user/logdays', 'SlavePaymentController@getPokerLogDays');
		Route::get('poker/user/day-data', 'SlavePaymentController@getPokerDayData');
		Route::get('poker/user/day-pay', 'SlavePaymentController@getPokerDayPay');
		//不同盲注场玩牌的统计
		Route::get('poker/user/queryPlayCount', 'SlaveUserController@queryPlayCount');
		//筹码流向 by mumu
		Route::get('poker/user/queryChips', 'SlaveUserController@queryChips');
		//牌局统计 by mumu
		Route::get('poker/user/queryPoker', 'SlaveUserController@queryPoker');
		//经济日志查询 by mumu
		Route::get('poker/user/queryLogEconomy', 'SlaveUserController@queryLogEconomy');
		//待处理订单查询 by taishou
		Route::get('poker/payment/queryDelayOrder','SlavePaymentController@queryDelayOrder');



		Route::get('poker/user/day', 'SlavePaymentController@getPokerDay');
		Route::get('poker/user/paynum', 'SlavePaymentController@getPokerPayNum');
		Route::get('poker/user/week', 'SlavePaymentController@getPokerLogWeek');
		Route::get('poker/user/regnew', 'SlavePaymentController@getPokerRegNew');
		Route::get('poker/economy/rank', 'SlaveEconomyLogController@getPokerPlayerEconomyRank');
		Route::get('poker/user/economy', 'SlaveEconomyLogController@getPokerEconomyStatistics');
		Route::get('poker/user/detail', 'SlaveEconomyLogController@getPokerPlayerEconomy');
		Route::get('poker/user/playerinfo', 'SlaveCreatePlayerLogController@getCreatePlayerInfo');
		Route::get('poker/user/allserver', 'SlaveEconomyLogController@getAllServerEconomy');
		Route::get('poker/cash/info', 'SlavePaymentController@getPokerCashInfo');
		Route::get('poker/round/data', 'SlavePaymentController@getPokerRoundsData');
		Route::get('poker/round/sign', 'SlavePaymentController@getPokerSignData');
		Route::get('uid/playerid', 'SlaveUserController@getPlayerIdByUid');
		Route::get('poker/uid/games', 'SlaveUserController@getPlayerGames');
		Route::get('poker/game/info', 'SlaveUserController@getPokerGames');
		Route::get('poker/user/unlog', 'SlaveUserController@getPokerUsers');
		Route::get('poker/user/log', 'SlaveUserController@getAllUsers');
		Route::get('poker/user/player_name', 'SlaveUserController@getPokerInfo');
		Route::get('poker/user/paynums', 'SlaveUserController@getPokerPayNum');

		Route::get('poker/user/pay-num', 'SlavePaymentController@getPayNumPoker');

		Route::get('user/item', 'SlaveEconomyLogController@userItemData');
		Route::get('user/user-info', 'SlaveUserController@getIdByName');
		Route::get('user/user-exp', 'SlaveEconomyLogController@getUserExp');
		//.......德扑退款查询
		Route::get('poker/pokeruserinfo','SlaveUserController@getPokerRefund');

		Route::get('user/ip-info','SlaveEconomyLogController@getUserIP');//ip
		Route::get('user/user-ip','SlaveUserController@getIdByName2');
		Route::get('player/economy/find-boss-killer-num', 'SlaveEconomyLogController@findBossKillerNum');
		Route::get('economy/server-consume', 'SlaveEconomyLogController@serverConsumeData');
		Route::get('economy/server-remain','SlaveEconomyLogController@getServerRemainYuanbao');
		Route::get('economy/recharge/success','SlavePaymentController@getSuccessOrders');
		Route::get('server/consume', 'SlaveEconomyLogController@getServersConsume');

		Route::get('user/info/log', 'SlaveCreatePlayerLogController@getUserInfoFromLog');
		Route::get('player/log-info', 'SlaveCreatePlayerLogController@getUserFromLog');
		
		Route::get('economy/yanwu/three','SlaveEconomyLogController@findYwcThree'); 

		Route::get('user/th','SlaveUserController@getUserTH');
		Route::get('user/player/th', 'SlaveUserController@getUserByPlayerTH');
		Route::get('server/get', 'SlaveUserController@getServerUnion');
		Route::get('player/player-info', 'SlaveUserController@getCreatePlayer');
		
		Route::get('dragon/log', 'SlaveUserController@getDragonLog');
		Route::get('economy/server-remain-player', 'SlaveEconomyLogController@getServerRemainPlayer');
		Route::get('poker/recharge-info', 'SlavePaymentController@getRechargeUID');
		Route::get('poker/user-info', 'SlaveUserController@getPokerUserInfo');
		Route::get('player/paydata', 'SlavePaymentController@playerPayData');
		Route::get('player/info', 'SlaveUserController@getPlayerUid');
		Route::get('poker/login', 'SlaveUserController@getPokerLogin');
		Route::get('user/xs', 'SlaveUserController@getCreatePlayer_xs');
		Route::get('poker/login-times', 'SlaveEconomyLogController@loginPlayersData');
		Route::get('poker/chips-range', 'SlaveEconomyLogController@chipsRangeData');
		Route::get('poker/player-login', 'SlaveLoginLogController@getPlayerLoginLog');
		Route::get('poker/rounds-range', 'SlaveEconomyLogController@roundsRangeData');
		Route::get('poker/games', 'SlaveUserController@getGamesData');
		Route::get('poker/log-data', 'SlaveUserController@getLogLogin');
		Route::get('poker/back-stat-old', 'SlaveUserController@getPokerBackStatOld');
		Route::get('poker/back-stat', 'SlaveUserController@getPokerBackStat');
		Route::get('poker/first-pay', 'SlaveUserController@getFirstPayPlayer');
		Route::get('poker/anony-player', 'SlaveUserController@getAnonyPlayer');
		//Route::get('party/get-member', 'SlaveUserController@getPartyMember');
		Route::get('shop/soldStatics', 'SlaveUserController@getSoldStatics');
		Route::get('mail/fromSlave', 'SlaveUserController@getRechargeFailInfoFromSlave');
		Route::get('poker/dataDaily', 'SlavePokerDataController@getPokerDataDailyFromSlave');

		Route::get('user/user_record','SlaveEconomyLogController@chipsRecordData2');
		Route::get('poker/chips_record_poker', 'SlaveEconomyLogController@chipsRecordData');
		Route::get('user/same_ip','SlaveUserController@sameIpData');
		Route::get('poker/matchRank','SlaveUserController@matchRankData');
		Route::get('poker/user/queryEconomy', 'SlaveUserController@queryEconomy');
		//gm
		Route::get('gm/questions', 'SlaveUserController@getGMQuestions');
		Route::get('poker/matchArea','SlaveUserController@matchAreaData');
		Route::get('poker/gameArea','SlaveUserController@gameAreaData');

		Route::get('import/mingge', 'SlaveMingGeLogController@importMingGeLog');

		Route::get('server/serverinfo', 'SlaveUserController@searchServerPlayer');

		Route::get('player/login/time', 'SlaveUserController@getPlayerLoginTime');

		Route::get('payment/order/search', 'SlavePaymentController@yuanbaoRankSearch');
		Route::get('player/economy/count', 'SlaveEconomyLogController@getPlayerEconomyCount');
		Route::get('player/name', 'SlaveEconomyLogController@getPlayerName');
		Route::get('mingge/log', 'SlaveUserController@getMingGeLog');
		Route::get('user/phone', 'SlaveUserController@getUserPhone');
		Route::get('player/economy/yysg/stat', 'SlaveEconomyLogController@getYysgPlayerEconomyStatistics');
		Route::get('player/economy/yysg', 'SlaveEconomyLogController@getYysgPlayerEconomy');
		Route::get('user/lonely/exp', 'SlaveEconomyLogController@getUserLonelyExp');
		Route::get('yysg/log/search', 'SlaveUserController@getPlayerLogDate');
		Route::get('mnsg/log/item', 'SlaveItemLogController@getPlayerLogItemData');

		//夜夜三国查询礼包销量
		Route::get('yysg/giftbag_num', 'SlavePaymentController@getyysggiftbagnum');
		//夜夜三国查询货币消耗
		Route::get('yysg/monetary_num', 'SlavePaymentController@getyysgmonetarynum');
		//夜夜三国查询玩家生命周期
		Route::get('yysg/lifetime', 'SlaveUserController@getYysgLifetime');
		//手游查询logindevice表
		Route::get('yysg/logindevice', 'SlaveUserController@getLogindeviceInfo');
		//手游把在线玩家数写入日志库
		Route::get('mobilegame/writeonlinenum', 'SlaveUserController@MGwriteOnlineNum');
		Route::get('log/wj/is_eat','SlaveUserController@playerWjData');
		//手游查询时间段内玩家平均在线时长
		Route::get('mg/avgonlinetime','SlaveUserController@MGavgonlinetime');
		//查询实名注册人数
		Route::get('users/signnum','SlaveUserController@signupnum');       
		//查看game_package表中的数据
		Route::get('payment/game_package', 'SlavePaymentController@getgamepackage');
		//执行手动输入的sql语句
		Route::post('execute/sql', 'SlaveUserController@getSqlresult');
		//检测playerid是否属于该夜夜三国check/yysgplayer
		Route::post('check/yysgplayer', 'SlaveUserController@checkyysgplayer');
	/*
	 *Get Google Validate Data
	 */	
	    Route::get('ggvalidate/modify','SlavePaymentController@ggvalidateInfo');
//third_product data
	    Route::get('third_product/getdata','SlavePaymentController@thirdproductData');
	    Route::get('third_product/update','SlavePaymentController@thirdproductUpdate');
	    
	    Route::get('player/outflow','SlaveLoginLogController@getRegistByTime');

	    Route::get('economy/player/abnormal','SlaveEconomyLogController@getAbnormalDada');

	    Route::get('player/like', 'SlaveUserController@getplayerinfobyincomplete');
	    Route::get('player/likeserver', 'SlaveUserController@getplayerinfobyincompleteserver');

	    Route::post('player/importance', 'SlaveUserController@getPlayerImportance');
	    
	    Route::get('log/equipment','SlaveUserController@playerEquipmentData');

	    Route::get('joyspade/daily/data', 'SlaveEconomyLogController@getPokerdailydata');

	    Route::get('log/player/wj','SlaveUserController@playerGetWjData');

	    Route::get('economy/parts', 'SlaveEconomyLogController@getSpendonParts');
	    Route::get('economy/each/player', 'SlaveEconomyLogController@getEconomyEachPlayer');
	    Route::get('economy/whole/server', 'SlaveEconomyLogController@getEconomyWholeServer');

		Route::get('payment/info/firstpay', 'SlavePaymentController@getFirstPayInfo');

		Route::get('payment/info/amount', 'SlavePaymentController@getAmountInfo');

		Route::get('paytrend/info', 'SlavePaymentController@getPayTrendInfo');

		Route::get('economy/remainyuanbao', 'SlaveEconomyLogController@getRemainYuanbao');

		Route::get('poker/writegiftbag', 'SlaveEconomyLogController@PokerWriteGiftbag');

		Route::get('poker/unsendgiftbag', 'SlaveEconomyLogController@PokerGetGiftbag');

		Route::get('poker/changegiftbagstatu', 'SlaveEconomyLogController@PokerChangeGiftbagStatu');

		Route::get('economy/expensesum','SlaveEconomyLogController@getExpenseSum');

		Route::get('firstpay/analysis', 'SlavePaymentController@getFirstPayAnalysis');

		Route::get('activity/analysis', 'SlaveEconomyLogController@getActivityAnalysis');

		Route::get('count/usernum', 'SlaveUserController@CountUserNum');

		Route::get('user/consumption/rank', 'SlavePaymentController@getConsumptionRank');

		Route::get('user/stat/signup', 'SlaveUserController@CountUserStatSignup');
		Route::get('user/stat/createplayer', 'SlaveUserController@CountUserStatCreateplayer');
		Route::get('user/stat/levelten', 'SlaveUserController@CountUserStatLevelten');

		Route::get('partner/log', 'SlaveEconomyLogController@CountCreatePartnerLog');

		Route::get('user/basic/count', 'SlaveUserController@getBasicCount');

		Route::get('getuid/byplayerinfo', 'SlaveUserController@getUidbyPlayerInfo');

		Route::get('mobile/getallpaymethods', 'SlavePaymentController@getAllPaymethods');

		Route::get('mobile/getgameproducts', 'SlavePaymentController@getAllGameProducts');

		Route::get('poker/activity/data','SlaveEconomyLogController@getActivityData');

		Route::get('yysg/newer/point', 'SlavePlayerController@getNewerPointInfo');

		Route::get('score/rank/log', 'SlaveCreatePlayerLogController@getScoreRankData');

		Route::get('mobilegame/getformdata', 'SlaveMobileLogController@getFormData');

		Route::get('mobilegame/modifydata', 'SlaveMobileLogController@modifyData');

		//第三方登录相关
		Route::get('mobilegame/getinformaitiondata', 'SlaveMobileLogController@getInformaitionData');

		Route::get('setup/stat', 'SlaveUserController@getSetupStat');

		Route::get('weekly/channel', 'SlaveUserController@getWeeklyChannelStat');

		Route::post('webgame/writeonlinenum', 'SlaveUserController@WEBwriteOnlineNum');
		//在create_player表中根据player_id获取玩家信息
		Route::get('createplayer/getplayer', 'SlaveUserController@getCreatePlayerById');

		Route::get('rzzw/rewardrecord', 'SlaveRewardController@RzzwRewardData');

		Route::get('mnsg/logsummon', 'SlaveSummonController@mnsglogsummonData');

		Route::post('filter/orders', 'SlavePaymentController@getfilterorders');

		Route::post('getdata/device', 'SlaveUserController@getDataByDeviceids');

		Route::get('setup/weekly', 'SlaveUserController@getWeeklySetup');

		Route::get('signupcreate/info', 'SlaveUserController@getSignupCreateInfo');

		Route::get('calculate/retention', 'SlaveCreatePlayerLogController@CalculateRetention');
		//德扑破产用户
		Route::get('poker/bankruptcy', 'SlaveEconomyLogController@PokerBankruptcy');
		//获得单个玩家一段时间内的充值信息 
		Route::post('payment/dollar/player', 'SlavePaymentController@getPayDollarByPlayerId');
		//获取单个玩家某段时间内获取的元宝总数
		Route::post('economy/yuanbao/player', 'SlaveEconomyLogController@geyPlayerYuanbaoIncrease');
		//活动送送送中统计充值数据由原来的使用元宝排行接口改为自己使用一个接口
		Route::get('player/payment/filter', 'SlavePaymentController@PlayerPaymentFilter');
		//游戏基础信息统计
		Route::post('basic/game/info/query', 'SlaveBasicGameInfoQueryController@BasicGameInfoQuery');
		//获取支付方式返利活动的比例
		Route::get('payment/method/activity', 'SlavePaymentController@getPaymentMethodActivity');
		//统计单服开服一定天数之间的登陆和留存数据
		Route::get('openserver/days/info', 'SlavePlayerController@OpenServerFrontDays');
		//风流三国将魂日志
		Route::get('flsg/mergegem/log', 'SlavePlayerController@getMergeGemData');

		Route::get('operation/log', 'SlaveCreatePlayerLogController@getOperationData');
		//手游item表的一些统计逻辑
		Route::get('mg/item/count', 'SlaveItemLogController@getItemCount');
		//获取一段时间内的创建玩家信息
		Route::get('server/create/players', 'SlaveCreatePlayerLogController@getServerCreatePlayers');
		Route::get('command/test', 'SlaveUserController@getCommandTest');
		Route::get('yysg/award', 'SlaveRewardController@getYYSGAward');
		Route::get('yysg/award/user', 'SlaveRewardController@getYYSGAwardUser');
		Route::get('mnsg/formation', 'SlaveMobileLogController@getFormationData');
		//获取美人猜猜猜日志信息
		Route::get('flsg/guess/log', 'SlaveWebLogController@getguessdata');
		Route::get('yysg/player/partnerdel', 'SlaveMobileLogController@getPartnerDel');
	}
);
//slave端接口，不进行key验证
Route::group(array(
	'prefix' => 'slave/api/v2',
	), function() {
		Route::get('get/online', 'SlaveForThirdPartyController@getGameOnlineNum');
		Route::get('get/player/create', 'SlaveForThirdPartyController@getPlayerCreateNum');
		Route::get('get/login', 'SlaveForThirdPartyController@getLoginData');
		Route::get('get/order', 'SlaveForThirdPartyController@getOrderData');
	}
);

//Master Server
Route::group(array(
		'before' => 'auth|permission|platform|games',
	), function () {
		Route::pattern('id', '[0-9]+');
		Route::get('users/close','UserController@close');
		Route::post('change/users/copypermition', 'UserController@copypermission');
		Route::resource('users', 'UserController');
		Route::resource('apps', 'AppController');
		Route::resource('groups', 'AppGroupController');
		Route::resource('department', 'DepartmentController');
		Route::resource('regions', 'RegionController');
		Route::resource('log', 'LogController');
		Route::resource('platforms', 'PlatformController');
		Route::resource('games', 'GameController');

		Route::resource('currency', 'CurrencyController');
		Route::resource('exchange-rate', 'ExchangeRateController');
		Route::post('pay-amount/check','PayAmountController@check');
		Route::post('pay-amount/batch-update','PayAmountController@batch_update');
		Route::get('pay-amount/create','PayAmountController@create_index');
		Route::post('pay-amount/create','PayAmountController@store');
		Route::get('pay-amount/check','PayAmountController@index');
		Route::post('pay-amount/get-type','PayAmountController@getType');
		Route::post('pay-amount/get-payment','PayAmountController@getPayment');
		// Route::resource('pay-amount', 'PayAmountController');
		Route::post('payment/get-payments', 'PlatformPaymentController@getPayment');

		Route::get('change/language', 'UserController@ChangeLanguage');
		Route::post('change/language', 'UserController@ChangeLanguageChange');

		//游戏信息
		Route::get('game/gameinformation','GameController@gameInformationIndex');

		//人事邮件
		Route::get('office/mails','OfficeMailsController@index');
		Route::post('office/mails','OfficeMailsController@maildeal');

		//脚本查询游戏服务器sql
		Route::get('game-server/sql','SlaveApiSqlController@gameserverindex');
		Route::post('game-server/sql','SlaveApiSqlController@gameserversql');
  /*
  /*
   *Mobile Pay-Type Change 
   */
        Route::get('mobile_paytype/paytypeInfo','PayTypeController@payTypeLoad');
        Route::get('mobile_paytype/modify','PayTypeController@payTypeModify');
        Route::get('mobile_paytype/add','PayTypeController@paytypeAdd');                  
        Route::post('mobile_paytype/update','PayTypeController@payTypeUpdate');      

        Route::get('update/servers', 'ServerController@UpdateServersIndex');
        Route::post('update/servers', 'ServerController@UpdateServers');          


		Route::resource('pay-type', 'PayTypeController');	
		Route::resource('merchant-data', 'MerchantDataController');	
		Route::resource('payment', 'PaymentController');
		Route::get('pay-method/batch-update','PaymentController@batch_update_index');
		Route::post('pay-method/batch-update','PaymentController@batch_update');
		Route::get('pay-method/huodong-index','PaymentController@huodong_index');
		Route::get('pay-method/huodong-edit','PaymentController@huodong_edit');
		Route::post('pay-method/huodong-update','PaymentController@huodong_update');
		Route::resource('payment-currency', 'PayCurrencyController');  
		Route::resource('project', 'ProjectController');
//		Route::get('project/{id}/release', 'ProjectController@release'); 
		Route::post('project/release', 'ProjectController@release'); 
		Route::post('project/check', 'ProjectController@check'); 

		Route::get('servers/{id}/sync', 'ServerController@syncServer');
		Route::get('servers/{id}/init', 'ServerController@initGameServer');
		Route::get('servers/{id}/open', 'ServerController@openServer');
		Route::get('servers/{id}/close', 'ServerController@closeServer');
		Route::get('servers/{id}/script', 'ServerController@openServerByScript');
		Route::get('servers/test_store', 'ServerController@testStore');
		Route::resource('servers', 'ServerController');

		Route::resource('organizations', 'OrganizationController');
       
		Route::group(array('prefix' => 'ad'), function() {
			Route::get('action1', 'AdLpController@getAction');
			Route::get('action2', 'AdCampaignController@getAction');
			Route::get('action3', 'AdJsController@getAction');
			Route::post('upload', 'AdLpController@upload');
			Route::resource('js', 'AdJsController');
		    Route::resource('lp', 'AdLpController');
		    Route::resource('link', 'AdLinkController');
		    Route::resource('term', 'AdTermController');
		    Route::resource('source', 'AdSourceController');
		    Route::resource('campaign', 'AdCampaignController');
		});
		Route::get('home/phpinfo', 'HomeController@getPHPInfo');
		Route::get('home/platforms', 'HomeController@showPlatforms');
		Route::get('setting/ip', 'SettingController@showIp');

		//Game Server Api
		Route::group(array('prefix' => 'game-server-api'), function() {

			//测试功能
			Route::get('mid/test', 'TestController@testIndex');
			Route::post('mid/test', 'TestController@testDeal');
			Route::get('mid/manger', 'TestController@availableMidsIndex');
			Route::post('mid/manger', 'TestController@availableMidsManger');
			//items上传功能
			Route::get('upload/items', 'ActivityController@uploaditemsload');
			Route::post('upload/items', 'ActivityController@uploaditemsupload');

			Route::get('download', 'DownloadController@index');
			Route::get('player', 'ServerPlayerController@index');
			Route::post('player', 'ServerPlayerController@search');

			Route::get('yysg/comment', 'YYSGGMController@opencomments');
			Route::post('yysg/comment', 'YYSGGMController@getcomments');
			Route::post('yysg/dealcomment', 'YYSGGMController@deal_comments');

			Route::get('yysg/logindevice', 'YYSGGMController@logindeviceIndex');
			Route::post('yysg/logindevice', 'YYSGGMController@logindeviceSerach');

			Route::get('giftbag/lookuppage', 'YYSGGiftBagController@lookuppage');
			Route::post('giftbag/lookuppage', 'YYSGGiftBagController@showcertaininfo');

			Route::get('player/account', 'ServerPlayerController@accountIndex');
			Route::post('player/account', 'ServerPlayerController@account');
		
			Route::get('player/qq-loginmaster', 'ServerPlayerController@qqLoginMasterIndex');
			Route::post('player/qq-loginmaster', 'ServerPlayerController@qqLoginMaster');
			
			Route::get('eb/log/skip', 'MasterLogController@getNewUrl');
			Route::get('eb/log', 'MasterLogController@getFile');

			Route::get('player/dissolve', 'ServerPlayerController@dissolveIndex');
			Route::post('player/dissolve', 'ServerPlayerController@dissolve');

			//页游热更策划文件
			Route::get('update/excel', 'ServerPlayerController@updateexcelload');
			Route::post('update/excel', 'ServerPlayerController@updateexcelupdate');

			Route::get('player/gm', 'ServerPlayerController@setGameMasterIndex');
			Route::post('player/gm', 'ServerPlayerController@setGameMaster');
            Route::get('announce', 'AnnounceController@index');
			Route::post('announce', 'AnnounceController@send');
			Route::get('notice','AnnounceController@noticeIndex');
			Route::post('notice','AnnounceController@noticeData');
			Route::get('stopNotice','AnnounceController@stopNoticeIndex');
			Route::get('stopNotice_nv','AnnounceController@stopNoticeIndex_nv');
			Route::post('lookupNotice','AnnounceController@lookupNotice');
			Route::post('stopNotice','AnnounceController@stopNotice');
			Route::post('stopNotice_nv','AnnounceController@stopNotice_nv');
			Route::get('gift-code/create', 'GiftCodeController@create');
			Route::post('gift-code/create', 'GiftCodeController@send');
			Route::get('gift-code', 'GiftCodeController@index');
			Route::post('gift-code', 'GiftCodeController@search');		
			
			Route::get('word-filter', 'WordFilterController@index');
			Route::post('word-filter', 'WordFilterController@add');
			Route::get('gs', 'GSController@index');
			Route::post('gs', 'GSController@load');
			Route::get('gm', 'GMController@index');
			Route::post('gm', 'GMController@load');
			Route::post('gm/reply', 'GMController@reply');
			Route::get('gm/replied', 'GMController@repliedIndex');
			Route::post('gm/replied', 'GMController@sendReplied');
			Route::get('weather', 'WeatherController@index');
			Route::post('weather', 'WeatherController@setWeather');
			Route::get('dress/wing', 'DressController@wingIndex');
			Route::post('dress/wing', 'DressController@wingData');
			Route::get('mail', 'MailController@index');
			Route::post('mail', 'MailController@send');
			Route::get('chatting', 'ChattingController@index');
			Route::post('chatting', 'ChattingController@getData');
			Route::get('mail/gift-mail', 'MailController@giftMailIndex');
			Route::post('mail/gift-mail', 'MailController@giftMail');
			Route::get('promotion', 'PromotionController@index');	
			Route::post('promotion', 'PromotionController@set');
			Route::post('promotion/lookup', 'PromotionController@lookup');
			Route::post('promotion/close', 'PromotionController@close');
			
			Route::get('promotion/ns', 'PromotionController@NSIndex');	
			Route::post('promotion/ns', 'PromotionController@NSOpen');
			Route::post('promotion/ns/lookup', 'PromotionController@NSLookup');
			Route::post('promotion/ns/close', 'PromotionController@NSClose');
			Route::post('promotion/ns/urgent_open', 'PromotionController@NSUrgentOpen');
			Route::post('promotion/ns/urgent_close', 'PromotionController@NSUrgentClose');

			Route::get('promotion_ns', 'PromotionController@index_ns');
			Route::post('promotion_ns', 'PromotionController@set');
			Route::post('promotion_ns/lookup', 'PromotionController@lookup_ns');
			Route::post('promotion_ns/close', 'PromotionController@close');
			
			Route::get('promotion/beauty-gift', 'PromotionController@beautyGiftIndex');
			Route::post('promotion/beauty-gift', 'PromotionController@beautyGiftOpen');
			Route::post('promotion/beauty-gift/close', 'PromotionController@beautyGiftClose');
			Route::post('promotion/beauty-gift/lookup', 'PromotionController@beautyGiftLookup');
		
			Route::get('promotion/day-sign', 'PromotionController@activityIndex');
			Route::post('promotion/day-sign', 'PromotionController@activityOpen');
			Route::post('promotion/day-sign/close', 'PromotionController@activityClose');
			Route::post('promotion/day-sign/lookup', 'PromotionController@activityLookup');

			Route::get('promotion/turnplate', 'PromotionController@turnplateIndex');
			Route::post('promotion/turnplate', 'PromotionController@turnplateOpen');
			Route::post('promotion/turnplate/close', 'PromotionController@turnplateClose');
			Route::post('promotion/turnplate/lookup', 'PromotionController@turnplateLookup');

			Route::get('promotion/open-server', 'PromotionController@openServerIndex');
			Route::post('promotion/open-server', 'PromotionController@openServerOpen');
			Route::post('promotion/open-server/close', 'PromotionController@openServerClose');
			Route::post('promotion/open-server/lookup', 'PromotionController@openServerLookup');

			Route::get('activity', 'ActivityController@index');
			Route::post('activity/filter-data', 'ActivityController@filterData');
			Route::post('activity/send-gift', 'ActivityController@sendGift');

			Route::get('gift-bag/single-server', 'GiftBagController@index');
			Route::post('gift-bag/single-server', 'GiftBagController@send');
			Route::get('gift-bag/all-server', 'GiftBagController@allServerIndex');
			Route::post('gift-bag/all-server', 'GiftBagController@sendAllServer');
			Route::get('gift-bag/all-server-gift-bag', 'GiftBagController@allServerGiftBagIndex');
			Route::post('gift-bag/all-server-gift-bag', 'GiftBagController@allServerGiftBagData');
			
			Route::get('gift-bag/all-server-gift-bag1', 'GiftBagController@allServerGiftBagIndex1');
			Route::post('gift-bag/all-server-gift-bag1', 'GiftBagController@allServerGiftBagData1');
			
			Route::get('change-tili', 'ChangeTiliController@index');
			Route::post('change-tili', 'ChangeTiliController@changeTili');
			Route::get('change-yuanbao', 'ChangeYuanbaoController@index');
			Route::post('change-yuanbao', 'ChangeYuanbaoController@changeYuanbao');
			Route::get('update-level', 'ChangeYuanbaoController@updateLevelIndex');
			Route::post('update-level', 'ChangeYuanbaoController@updateLevel');
			Route::get('month-card', 'ChangeYuanbaoController@monthCardIndex');
			Route::post('month-card', 'ChangeYuanbaoController@monthCard');
			Route::get('change-chenghao', 'ChangeChenghaoController@index');
			Route::post('change-chenghao', 'ChangeChenghaoController@ChangeChenghao');
			
			Route::get('tournament/single-server', 'TournamentController@singleServerIndex');
			Route::post('tournament/single-server', 'TournamentController@singleServer');
			Route::post('tournament/single-server/update', 'TournamentController@singleServerUpdate');
			Route::post('tournament/single-server/load', 'TournamentController@singleServerLoad');
			Route::post('tournament/single-server/close', 'TournamentController@singleServerClose');
			
			Route::get('tournament/cross-server', 'TournamentController@crossServerIndex');
			Route::post('tournament/cross-server', 'TournamentController@crossServer');
			Route::post('tournament/cross-server/signup', 'TournamentController@crossServerSignup');
			Route::post('tournament/cross-server/lookup', 'TournamentController@crossServerLookup');
			Route::post('tournament/cross-server/close', 'TournamentController@crossServerClose');
			Route::post('tournament/cross-server/update', 'TournamentController@crossServerUpdate');
			Route::post('tournament/cross-server/look', 'TournamentController@crossServerLook');

			Route::get('tournament/melee', 'TournamentController@meleeIndex');//大乱斗
			Route::get('tournament/allserverconnect', 'TournamentController@allserverconnectIndex');//全服连接
			Route::post('tournament/melee', 'TournamentController@meleeData');
			Route::post('tournament/melee/lookup', 'TournamentController@meleeLookup');
			Route::post('tournament/melee/close', 'TournamentController@closeMelee');
			//大乱斗--界王
			Route::get('tournament/jiewang', 'TournamentController@jiewangIndex');
			Route::post('tournament/jiewang', 'TournamentController@jiewangOpen');
			Route::post('tournament/jiewang/lookup', 'TournamentController@jiewangLookup');
			Route::post('tournament/jiewang/close', 'TournamentController@jiewangClose');

            Route::get('dld/husong', 'TournamentController@husongIndex');
            Route::post('dld/husong', 'TournamentController@husongSend');

            Route::get('dld/resetXJYWC', 'DldSpecialController@index');
            Route::post('dld/resetXJYWC', 'DldSpecialController@resetXJYWC');

            //银河第一武道会
            Route::get('dld/galaxyBudokai', 'DldSpecialController@budokaiIndex');
            Route::post('dld/galaxyBudokai', 'DldSpecialController@budokaiOpen');
            Route::post('dld/galaxyBudokaiClose', 'DldSpecialController@budokaiClose');

            Route::get('dld/lvTalk', 'DldSpecialController@lvTalkIndex');
            Route::post('dld/lvTalk', 'DldSpecialController@lvTalkSwitch');
            Route::post('dld/lvTalkLookup', 'DldSpecialController@lvTalkLookup');

            //王者之战
            Route::get('kingBattle/single-server', 'KingBattleController@singleServerIndex');
            Route::post('kingBattle/single-server', 'KingBattleController@singleServer');
            Route::post('kingBattle/single-server/update', 'KingBattleController@singleServerUpdate');
            Route::post('kingBattle/single-server/load', 'KingBattleController@singleServerLoad');
            Route::post('kingBattle/single-server/close', 'KingBattleController@singleServerClose');

            //跨服王者
            Route::get('kingBattle/cross-server', 'KingBattleController@crossServerIndex');
            Route::post('kingBattle/cross-server', 'KingBattleController@crossServer');
            Route::post('kingBattle/cross-server/signup', 'KingBattleController@crossServerSignup');
            Route::post('kingBattle/cross-server/lookup', 'KingBattleController@crossServerLookup');
            Route::post('kingBattle/cross-server/close', 'KingBattleController@crossServerClose');
            Route::post('kingBattle/cross-server/update', 'KingBattleController@crossServerUpdate');
            Route::post('kingBattle/cross-server/look', 'KingBattleController@crossServerLook');

			//仙界活动--三国
			Route::get('tournament/melee/close-heaven', 'TournamentController@heavenIndex');
			Route::post('tournament/melee/close-heaven', 'TournamentController@closeHeaven');
			Route::post('tournament/melee/open-heaven', 'TournamentController@openHeaven');
			Route::post('tournament/melee/lookup-heaven', 'TournamentController@lookupHeaven');

            //三国每日更新
            Route::get('dailyUpdate', 'DailyUpdateController@index');
            Route::post('dailyUpdate', 'DailyUpdateController@set');
            Route::post('dailyUpdate/check', 'DailyUpdateController@check');


			Route::get('tournament/heaven/open', 'TournamentController@heavenStudioIndex');
			Route::post('tournament/heaven/open', 'TournamentController@heavenStudioOpen');
			Route::post('tournament/heaven/lookup', 'TournamentController@heavenStudioOpen');
			Route::post('tournament/heaven/close', 'TournamentController@heavenStudioOpen');

			Route::get('tournament/heaven_battle/open', 'TournamentController@heavenBattleIndex');
			Route::post('tournament/heaven_battle/open', 'TournamentController@heavenBattleOperate');

			Route::get('change/lingshi', 'ChangeLingshiAndQiyundianController@index');
			Route::post('change/lingshi', 'ChangeLingshiAndQiyundianController@change');
			Route::get('backpack', 'BackpackController@index');
			Route::post('backpack', 'BackpackController@addItemToBackpack');

			Route::get('beauty', 'ChangeTiliController@beautyIndex');
			Route::post('beauty', 'ChangeTiliController@changeBeauty');

			//德州扑克API
			Route::get('poker/daily','DailyGiveChipsController@chipIndex');
			Route::post('poker/daily','DailyGiveChipsController@dailyGiveChip');
			Route::get('poker/tfRecover','DailyGiveChipsController@tfRecoverIndex');
			Route::post('poker/tfRecover','DailyGiveChipsController@dailyTfRecover');

			Route::get('poker/chips', 'PokerGiveChipsController@chipIndex');
			Route::post('poker/chips', 'PokerGiveChipsController@giveChip');

			Route::get('poker/cheater', 'PokerCheaterController@cheaterIndex');
			Route::post('poker/cheater', 'PokerCheaterController@getCheaterPlayerId');

			//筹码流向查询 by Mumu
			Route::get('poker/querychip', 'PokerGiveChipsController@queryChipIndex');
			Route::post('poker/querychip', 'PokerGiveChipsController@queryChip');
			
			//不同盲注的场的玩家统计
			Route::get('poker/playcount', 'PokerQueryController@queryPlayCountIndex');
			Route::post('poker/playcount', 'PokerQueryController@queryPlayCount');

			//连胜玩家查询 by taishou
			Route::get('poker/steadPlayer', 'PokerGiveChipsController@steadPlayerIndex');
			Route::post('poker/steadPlayer', 'PokerGiveChipsController@steadPlayer');
			
			//币商设置 by taishou
			Route::get('poker/setBusiness', 'PokerGiveChipsController@setBusinessmanIndex');
			Route::post('poker/setBusiness', 'PokerGiveChipsController@setBusinessman');
			Route::post('poker/getBusiness', 'PokerGiveChipsController@getBusinessman');
			//2人刷chip玩家查询 by taishou
			Route::get('poker/tradeChips', 'PokerGiveChipsController@tradeChipsIndex');
			Route::post('poker/tradeChips', 'PokerGiveChipsController@tradeChips');
			//待处理订单查询 by taishou
			Route::get('poker/delayOrder', 'PokerGiveChipsController@delayOrderIndex');
			Route::post('poker/delayOrder', 'PokerGiveChipsController@delayOrder');
			//设置禁言 by taishou
			Route::get('poker/speakAuthority', 'PokerGiveChipsController@speakAuthorityIndex');
			Route::post('poker/speakAuthority', 'PokerGiveChipsController@speakAuthority');
			//开活动 by taishou
			Route::get('poker/activityStatus', 'PokerGiveChipsController@activityStatusIndex');
			Route::post('poker/activityStatus', 'PokerGiveChipsController@activityStatus');
			// //最近20个动作
			Route::get('poker/recentAction', 'PokerGiveChipsController@recentActionIndex');
			Route::post('poker/recentAction','PokerGiveChipsController@recentAction');
			//相同保险箱密码玩家
			Route::get('poker/sameStrongboxPasswd', 'PokerGiveChipsController@sameStrongboxPasswdIndex');
			Route::post('poker/sameStrongboxPasswd', 'PokerGiveChipsController@sameStrongboxPasswd');
			//创建自定义礼包
			Route::get('poker/createLibao', 'PokerGiveItemController@createLibaoIndex');
			Route::post('poker/createLibao', 'PokerGiveItemController@createLibao');
			//发送自定义礼包
			Route::get('poker/sendLibao', 'PokerGiveItemController@sendLibaoIndex');
			Route::post('poker/sendLibao', 'PokerGiveItemController@sendLibao');
			//牌局统计 by mumu
			Route::get('poker/queryPoker', 'PokerGiveChipsController@queryPokerIndex');
			Route::post('poker/queryPoker', 'PokerGiveChipsController@queryPoker');

			//经济日志查询 by mumu
			Route::get('poker/queryLogEconomy', 'PokerQueryController@queryLogEconomyIndex');
			Route::post('poker/queryLogEconomy', 'PokerQueryController@queryLogEconomy');

			Route::get('poker/golds', 'PokerGiveGoldsController@goldIndex');
			Route::post('poker/golds', 'PokerGiveGoldsController@giveGold');
			Route::get('poker/items', 'PokerGiveItemController@itemIndex');
			Route::post('poker/items', 'PokerGiveItemController@giveItem');
			Route::get('poker/player', 'PokerUserInfoController@playerIndex');
			Route::post('poker/player', 'PokerUserInfoController@getPlayer');
			Route::get('poker/user', 'PokerUserInfoController@userIndex');
			Route::post('poker/user', 'PokerUserInfoController@getUser');

			Route::get('poker/user-num', 'PokerUserInfoController@onlinePlayerIndex');
			Route::post('poker/user-num', 'PokerUserInfoController@onlinePlayerData');

			Route::get('poker/user-piece', 'PokerUserInfoController@userPieceIndex');
			Route::post('poker/user-piece', 'PokerUserInfoController@userPieceData');

			Route::get('poker/gm', 'PokerGMController@gmIndex');
			Route::post('poker/gm', 'PokerGMController@getGM');
			Route::post('poker/reply', 'PokerGMController@replyGM');
			Route::get('poker/replied', 'PokerGMController@repliedIndex');
			Route::post('poker/replied', 'PokerGMController@repliedGM');
			Route::get('poker/announce', 'PokerGMController@pokerAnnounceIndex');
			Route::post('poker/announce', 'PokerGMController@pokerAnnounceSend');
			Route::get('poker/reward/money', 'TexaxPokerRewardController@index');
			Route::post('poker/reward/money', 'TexaxPokerRewardController@sendMoney');

			Route::get('poker/backStat', 'PokerBackStatController@backStatIndex');
			Route::post('poker/backStat', 'PokerBackStatController@backStatGetOld');
			Route::get('poker/reFpAnony', 'PokerBackStatController@refluxFirstPayAndAnonyIndex');
			Route::post('poker/reFpAnony', 'PokerBackStatController@refluxFirstPayAndAnony');

			//夜夜三国手游API
            Route::get('players/gm', 'YYSGServerPlayerController@setGameMasterIndex');
            Route::post('players/gm', 'YYSGServerPlayerController@setGameMaster');

            Route::get('yysg/closeAccount', 'YYSGServerPlayerController@closeAccountIndex');
            Route::post('yysg/closeAccount', 'YYSGServerPlayerController@closeAccountSend');

            Route::get('yysg/bannedTalk', 'YYSGServerPlayerController@bannedTalkIndex');
            Route::post('yysg/bannedTalk', 'YYSGServerPlayerController@bannedTalkSend');

            Route::get('yysg/checkAccountStatu', 'YYSGServerPlayerController@checkAccountStatuIndex');
            Route::post('yysg/checkAccountStatu', 'YYSGServerPlayerController@checkAccountStatuSend');

            Route::get('yysg/gm', 'YYSGGMController@index');//已被功能yysg/gmTalk替代
            Route::post('yysg/gm', 'YYSGGMController@load');
            Route::post('yysg/gm/reply', 'YYSGGMController@reply');

            Route::get('yysg/gmTalk', 'YYSGGMController@gmTalk');
            Route::get('yysg/vip/gmTalk', 'YYSGGMController@gmVipTalk');
            Route::post('yysg/gmTalk', 'YYSGGMController@gmMessage');
            Route::post('yysg/gmTalkSend', 'YYSGGMController@gmMessageSend');

            Route::get('yysg/gm-replied', 'YYSGGMController@repliedIndex');
            Route::post('yysg/gm-replied', 'YYSGGMController@sendReplied');

            Route::get('yysg/gift-bag', 'YYSGGiftBagController@index');
            Route::post('yysg/gift-bag', 'YYSGGiftBagController@send');

            Route::get('yysg/gift-bag/all-server', 'YYSGGiftBagController@AllServerGiftbagIndex');
            Route::post('yysg/gift-bag/all-server', 'YYSGGiftBagController@AllServerGiftbagSend');

            Route::get('yysg/mail-gift', 'YYSGGiftBagController@mailgiftindex');
            Route::post('yysg/mail-gift', 'YYSGGiftBagController@mailgiftsend');

            Route::get('yysg/count-giftbag', 'YYSGGiftBagController@count_giftbag_load');
            Route::post('yysg/count-giftbag', 'YYSGGiftBagController@count_giftbag_check');

            Route::get('yysg/count-monetary', 'YYSGGiftBagController@count_monetary_load');
            Route::post('yysg/count-monetary', 'YYSGGiftBagController@count_monetary_check');


            //神仙道API
			Route::get('sxd/send-gift', 'SXDGameController@sendGiftIndex');
			Route::post('sxd/send-gift', 'SXDGameController@sendGiftData');
			Route::get('sxd/player', 'SXDGameController@playerIndex');
			Route::post('sxd/player', 'SXDGameController@playerData');
			Route::get('sxd/user', 'SXDGameController@userIndex');
			Route::post('sxd/user', 'SXDGameController@userData');

			Route::get('action', 'ActivityController@activityIndex');
			Route::post('action/open', 'ActivityController@activityOpen');
			Route::post('action/lookup', 'ActivityController@activityLookup');
			Route::post('action/close', 'ActivityController@activityClose');	
			//QQ活动
			Route::get('qqaction', 'PromotionController@qqIndex');
			Route::post('qqaction/open', 'PromotionController@qqActivityOpen');
			Route::post('qqaction/lookup', 'PromotionController@qqActivityLookup');
			Route::post('qqaction/close', 'PromotionController@qqActivityClose');

			Route::get('qqserver', 'PromotionController@qqOpenServerIndex');
			Route::post('qqserver/open', 'PromotionController@qqOpenServerOpen');
			Route::post('qqserver/lookup', 'PromotionController@qqOpenServerLookUp');
			Route::post('qqserver/close', 'PromotionController@qqOpenServerClose');

			//印尼商店
			Route::get('promotion/shop-open', 'PromotionController@treasureShopIndex');
			Route::post('promotion/shop-open', 'PromotionController@treasureShopOpen');
			Route::post('promotion/shop-lookup', 'PromotionController@treasureShopLookUp');
			Route::post('promotion/shop-close', 'PromotionController@treasureShopClose');

			Route::get('user/item', 'ItemExpController@userItemIndex');
			Route::post('user/item', 'ItemExpController@userItemData');
			Route::get('user/exp', 'ItemExpController@userExpIndex');
			Route::post('user/exp', 'ItemExpController@userExpData');

			//德扑退款
			Route::get('poker/refund', 'PokerGiveChipsController@userPokerIndex');
			Route::post('poker/refund', 'PokerGiveChipsController@userPokerData');

			Route::get('server/remain','ItemExpController@serverRemainyuanbaoIndex');
			Route::post('server/remain','ItemExpController@serverRemainyuanbaoData');
			Route::get('level/stop-announce', 'AnnounceController@stopAnnounceIndex');
			Route::post('level/stop-announce', 'AnnounceController@stopAnnounceData');
			Route::post('level/stop-announce/look', 'AnnounceController@stopAnnounceLookUp');
			Route::get('level/stop-announce-nvshen','AnnounceController@stopAnnounceIndexNV');
			Route::post('level/stop-announce-nvshen','AnnounceController@stopAnnounceDataNV');
			Route::post('level/stop-announce-nvshen/look','AnnounceController@stopAnnounceLookupNV');
			Route::post('gift-bag/all-server-gift-bag/getsource', 'GiftBagController@getSource');
			Route::get('promotion/qq-friends', 'PromotionController@qqInviteFriendIndex');
			Route::post('promotion/qq-friends', 'PromotionController@qqInviteFriendData');
			//合服后的一些
			Route::get('player/union', 'ServerPlayerController@indexUnion');
			Route::post('player/union', 'ServerPlayerController@searchUnion');
			Route::post('player/union/getsource', 'ServerPlayerController@getSource');

			Route::get('gm/order', 'GMController@gmOrderIndex');
			Route::post('gm/order', 'GMController@gmOrderOpen');

			Route::get('announce/update', 'AnnounceController@updateNoticeIndex');
			Route::post('announce/update', 'AnnounceController@updateNoticeSend');
			Route::post('announce/update-open', 'AnnounceController@updateNoticeOpen');
			Route::get('gift-bag/all-server-sxd', 'SXDGameController@sxdGiftGroupIndex');
			Route::post('gift-bag/all-server-sxd', 'SXDGameController@sxdGiftGroupSend');

			Route::get('battle/champion', 'GMController@battleChampionIndex');
			Route::post('battle/champion', 'GMController@battleChampionData');

			Route::get('battle/champion/download', 'GMController@downloadBattleChampionIndex');
			Route::post('battle/champion/download', 'GMController@downloadBattleChampionData');

			Route::get('user/th', 'HazgGameController@userIndex');
			Route::post('user/th', 'HazgGameController@userData');
			Route::get('th/player', 'HazgGameController@playerIndex');
			Route::post('th/player', 'HazgGameController@playerData');

			Route::get('dragon/log', 'ItemExpController@dragonBallIndex');
			Route::post('dragon/log', 'ItemExpController@dragonBallData');

			Route::get('freeze/log', 'ItemExpController@freezePlayerIndex');
			Route::post('freeze/log', 'ItemExpController@freezePlayerData');

			Route::get('player/escort', 'PromotionController@playerEscortIndex');
			Route::post('player/escort', 'PromotionController@playerEscortOpen');

			Route::get('recharge/first', 'GMController@firstRechargeIndex');
			Route::post('recharge/first', 'GMController@firstRechargeOperate');

			Route::get('poker/recharge-info', 'PokerPaymentController@rechargeCountIndex');
			Route::post('poker/recharge-info', 'PokerPaymentController@rechargeCountData');

			Route::get('player/paydata', 'PokerPaymentController@playerPayIndex');
			Route::post('player/paydata', 'PokerPaymentController@playerPayData');

			Route::get('nszj/partner', 'ItemExpController@inviteFriendIndex');
			Route::post('nszj/partner', 'ItemExpController@inviteFriendAction');

			Route::get('gm/onekey', 'ItemExpController@oneKeyIndex');
			Route::post('gm/onekey', 'ItemExpController@oneKeyOperate');
			Route::get('activity/item', 'ItemExpController@itemActivityIndex');
			Route::post('activity/item', 'ItemExpController@itemActivityOperate');
			Route::get('cross/war', 'GSController@crossWarLordsIndex');
			Route::post('cross/wars-open', 'GSController@crossWarLordsOpen');
			Route::post('cross/wars-update', 'GSController@crossWarLordsUpdate');
			Route::post('cross/wars-signup', 'GSController@crossWarLordsSignUp');
			Route::post('cross/wars-lookup', 'GSController@crossWarLordsSignLookUp');
			Route::post('cross/wars-look', 'GSController@crossWarLordsLookUp');
			Route::post('cross/wars-close', 'GSController@crossWarLordsClose');

			Route::get('user/ip', 'IPController@userIPIndex');
			Route::post('user/ip','IPController@userIPData');

			Route::get('poker/login-time', 'PokerGiveGoldsController@loginPlayersIndex');
			Route::post('poker/login-time', 'PokerGiveGoldsController@loginPlayersData');

			Route::get('poker/message', 'PokerGiveChipsController@sendMessageIndex');
			Route::post('poker/message', 'PokerGiveChipsController@sendMessageOperate');

			Route::get('poker/message-group', 'PokerGiveChipsController@sendMessageGroupIndex');
			Route::post('poker/message-group', 'PokerGiveChipsController@sendMessageGroupOperate');

			Route::get('poker/chips-range', 'PokerGiveGoldsController@chipsRangeIndex');
			Route::post('poker/chips-range', 'PokerGiveGoldsController@chipsRangeData');

			Route::get('poker/rounds-range', 'PokerGiveGoldsController@roundsRangeIndex');
			Route::post('poker/rounds-range', 'PokerGiveGoldsController@roundsRangeData');

			Route::get('poker/player-login', 'PokerGiveGoldsController@playerLoginIndex');
			Route::post('poker/player-login', 'PokerGiveGoldsController@playerLoginData');

			Route::get('poker/games', 'PokerGMController@dayGamesIndex');
			Route::post('poker/games', 'PokerGMController@dayGamesData');

			Route::get('heaven/grain', 'ItemExpController@heavenGrainIndex');
			Route::post('heaven/grain', 'ItemExpController@heavenGrainOperate');

			Route::get('poker/delete', 'PokerGMController@deleteChipsIndex');
			Route::post('poker/delete', 'PokerGMController@deleteChipsOperate');

			Route::get('poker/freeze', 'PokerGMController@freezePlayerIndex');
			Route::post('poker/freeze', 'PokerGMController@freezePlayerOperate');

			Route::get('poker/item', 'PokerGiveItemController@itemIndex');
			Route::post('poker/item', 'PokerGiveItemController@giveItem');

			Route::get('poker/item-group', 'PokerGiveItemController@itemGroupIndex');
			Route::post('poker/item-group', 'PokerGiveItemController@itemGroupSend');
			//感恩节女神活动
			Route::get('promotion/beautyNsOpen', 'PromotionController@beautyGiftNSZJIndex');
			Route::post('promotion/beautyNsOpen', 'PromotionController@beautyGiftNSZJDeal');
			Route::post('promotion/beautyNsClose', 'PromotionController@beautyGiftNSZJClose');
			Route::post('promotion/beautyNsLook', 'PromotionController@beautyGiftNSZJLook');
            //圣域争霸
            Route::get('nszj/shengyu', 'ShengyuController@shengyuIndex');
            Route::post('nszj/shengyu', 'ShengyuController@shengyuData');
            Route::post('nszj/shengyu/lookup', 'ShengyuController@shengyuLookup');
            Route::post('nszj/shengyu/close', 'ShengyuController@shengyuClose');
            //全服圣域争霸
            Route::get('nszj/all_shengyu', 'ShengyuController@allShengyuIndex');
            Route::post('nszj/all_shengyu', 'ShengyuController@shengyuData');
            Route::post('nszj/all_shengyu/lookup', 'ShengyuController@shengyuLookup');
            Route::post('nszj/all_shengyu/close', 'ShengyuController@shengyuClose');
			//Boss 复活次数
			Route::get('boss/lives', 'BossController@bosslivesIndex');
			Route::post('boss/lives', 'BossController@updateBosslives');
			//帮派功能
			Route::get('party', 'PartyController@partyMemberIndex');
			Route::post('party', 'PartyController@getPartyMember');			
			//商城商品信息
			Route::get('shop', 'ShopController@index');
			Route::post('shop', 'ShopController@shopAction');
			Route::get('shop/soldStatics', 'ShopController@soldStaticsIndex');
			Route::post('shop/soldStatics', 'ShopController@getSoldStatics');

			Route::get('poker/chips_record', 'PokerGiveGoldsController@chipsRecordIndex');
			Route::post('poker/chips_record', 'PokerGiveGoldsController@chipsRecordDate');

			Route::get('poker/same_ip', 'PokerUserInfoController@sameIpIndex');
			Route::post('poker/same_ip', 'PokerUserInfoController@sameIpData');
			//德扑比赛查询
			Route::get('poker/match_rank', 'PokerUserInfoController@matchRankIndex');
			Route::post('poker/match_rank', 'PokerUserInfoController@matchRankData');
			//天下第一
			Route::get('cross/world', 'GSController@crossWorldLordsIndex');
			Route::post('cross/world-open', 'GSController@crossWorldLordsOpen');
			Route::post('cross/world-update', 'GSController@crossWorldLordsUpdate');
			Route::post('cross/world-signup', 'GSController@crossWorldLordsSignUp');
			Route::post('cross/world-lookup', 'GSController@crossWorldLordsSignLookUp');
			Route::post('cross/world-look', 'GSController@crossWorldLordsLookUp');
			Route::post('cross/world-close', 'GSController@crossWorldLordsClose');
			Route::post('cross/world_reset', 'GSController@crossServerAllUpdate');
			//武将pk
			Route::get('cross/server_pk', 'GSController@crossServerPK');
			Route::post('cross/all-update', 'GSController@crossServerAllUpdate');
			Route::post('cross/allserver-signup', 'GSController@crossServerPKSignup');
			Route::post('cross/allserver-lookup', 'GSController@crossServerPKLookup');

            //天下第一特别版
            Route::get('cross/worldx', 'GSController@crossWorldXLordsIndex');
            Route::post('cross/worldx-open', 'GSController@crossWorldXLordsOpen');
            Route::post('cross/worldx-update', 'GSController@crossWorldXLordsUpdate');
            Route::post('cross/worldx-signup', 'GSController@crossWorldXLordsSignUp');
            Route::post('cross/worldx-lookup', 'GSController@crossWorldXLordsSignLookUp');
            Route::post('cross/worldx-look', 'GSController@crossWorldXLordsLookUp');
            Route::post('cross/worldx-close', 'GSController@crossWorldXLordsClose');
			//德扑经济查询
			Route::get('poker/queryEconomy', 'PokerQueryController@queryEconomyIndex');
			Route::post('poker/queryEconomy', 'PokerQueryController@queryEconomy');
			//德扑用户玩牌比赛场
			Route::get('poker/match_area', 'PokerUserInfoController@matchAreaIndex');
			Route::post('poker/match_area', 'PokerUserInfoController@matchAreaData');
			//德扑用户玩牌游戏场
			Route::get('poker/game_area', 'PokerUserInfoController@gameAreaIndex');
			Route::post('poker/game_area', 'PokerUserInfoController@gameAreaData');

			//女神的寂寞
			Route::get('tournament/lonely', 'TournamentController@nsLonelyIndex');
			Route::post('tournament/lonely/open', 'TournamentController@nsLonelyOpen');
			Route::post('tournament/lonely/lookup', 'TournamentController@nsLonelyOpen');
			Route::post('tournament/lonely/close', 'TournamentController@nsLonelyOpen');
			//女神转转活动
			Route::get('promotion/around', 'PromotionController@aroundIndex');
			Route::post('promotion/around/open', 'PromotionController@aroundOpen');
			Route::post('promotion/around/close', 'PromotionController@aroundClose');
			Route::post('promotion/around/lookup', 'PromotionController@aroundLookup');
			//礼包玩家查询
			Route::get('server/item', 'ItemExpController@serverItemIndex');
			Route::post('server/item', 'ItemExpController@serverItemData');
			Route::post('server/download', 'ItemExpController@downloadServerItemData');
			Route::get('server/download', 'ItemExpController@downloadServerItemIndex');
			//三国设置活动奖励
			Route::get('promotion/award/set', 'PromotionController@awardSetIndex');	
			Route::post('promotion/award/set', 'PromotionController@awardSet');
			Route::post('promotion/award/set/look', 'PromotionController@awardSetLook');
			//
			Route::get('player/login', 'ServerPlayerController@playerLoginIndex');
			Route::post('player/login', 'ServerPlayerController@playerLoginData');
			//星宿日志
			Route::get('mingge/log', 'ItemExpController@mingGeIndex');
			Route::post('mingge/log', 'ItemExpController@mingGeData');
			//女神的寂寞--查看玩家经验
			Route::get('user/lonely/exp', 'ItemExpController@userLonelyExpIndex');
			Route::post('user/lonely/exp', 'ItemExpController@userLonelyExpData');

			Route::get('yysg/player', 'YYSGServerPlayerController@yysgPlayerIndex');
			Route::post('yysg/player', 'YYSGServerPlayerController@yysgPlayerSearch');
			//设置限时抢购活动
			Route::get('promotion/limit/buy/set', 'PromotionController@limitBuyIndex');	
			Route::post('promotion/limit/buy/set', 'PromotionController@limitBuySet');
			Route::post('promotion/limit/buy/look', 'PromotionController@limitBuyLook');
			//设置团购活动
			Route::get('promotion/group/buy/set', 'PromotionController@groupBuyIndex');	
			Route::post('promotion/group/buy/set', 'PromotionController@groupBuySet');
			Route::post('promotion/group/buy/change', 'PromotionController@groupBuyChange');
			Route::post('promotion/group/buy/look', 'PromotionController@groupBuyLook');
			//设置在线奖励
			Route::get('promotion/online/award/set', 'PromotionController@onlineAwardIndex');	
			Route::post('promotion/online/award/set', 'PromotionController@onlineAwardSet');
			Route::post('promotion/online/award/look', 'PromotionController@onlineAwardLook');
			//夜夜三国查看log
			Route::get('yysg/log/search', 'YYSGServerPlayerController@playerLogIndex');
			Route::post('yysg/log/search', 'YYSGServerPlayerController@playerLogSearch');
			Route::get('mnsg/announce', 'YYSGGMController@announceIndex');
			Route::post('mnsg/announce', 'YYSGGMController@announceSend');
			//夜夜三国活动
			Route::get('activity/index', 'YYSGGMController@ActivityIndex');	
			Route::post('activity/open', 'YYSGGMController@ActivityOpen');
			Route::post('activity/close', 'YYSGGMController@ActivityClose');
			Route::post('activity/check', 'YYSGGMController@ActivityCheck');
			//夜夜三国活动公告
			Route::get('activity/announce/index', 'YYSGGMController@ActivityAnnounce');	
			Route::post('activity/announce/release', 'YYSGGMController@ActivityAnnounceRelease');
			Route::post('activity/announce/look', 'YYSGGMController@ActivityAnnounceLook');
			Route::post('activity/announce/update', 'YYSGGMController@ActivityAnnounceUpdate');
			//萌娘三国查询道具获取日志
			Route::get('mnsg/log/item', 'YYSGServerPlayerController@playerLogItemIndex');
			Route::post('mnsg/log/item', 'YYSGServerPlayerController@playerLogItemSearch');
			//夜夜三国新手指导
			Route::get('players/beginner', 'YYSGServerPlayerController@setBeginnerMasterIndex');
            Route::post('players/beginner', 'YYSGServerPlayerController@setBeginnerMaster');
            //手游查询当前在线人数
			Route::get('mobilegame/onlinenum', 'YYSGServerPlayerController@getonlinenumload');
            Route::post('mobilegame/onlinenum', 'YYSGServerPlayerController@getonlinenumpost');
            //夜夜三国查询武将是否被吃
			Route::get('log/wj/is_eat', 'YYSGServerPlayerController@playerWjIndex');
            Route::post('log/wj/is_eat', 'YYSGServerPlayerController@playerWjData');
            //手游计算时间段内玩家平均在线时长
 			Route::get('mg/avgonlinetime', 'YYSGServerPlayerController@mgonlinetimeload');
            Route::post('mg/avgonlinetime', 'YYSGServerPlayerController@mgonlinetimepost'); 
            //萌娘三国补储，此功能会自动增加玩家的vip经验
  			Route::get('mnsg/restore', 'YYSGServerPlayerController@mnsgrestoreload');
            Route::post('mnsg/restore', 'YYSGServerPlayerController@mnsgrestorepost');            
            //萌娘三国开关五行好评活动
			Route::get('mnsg/fivestars', 'YYSGServerPlayerController@fivestarsload');
            Route::post('mnsg/fivestars', 'YYSGServerPlayerController@fivestarsswitch');                    
 			//官网任务管理
  			Route::get('upload/advice', 'YYSGServerPlayerController@adviceload');
            Route::post('upload/advice', 'YYSGServerPlayerController@adviceupload'); 
            Route::get('upload/advicecheck', 'YYSGServerPlayerController@taskload');
            Route::post('upload/advicecheck', 'YYSGServerPlayerController@taskdeal');  
            Route::post('change/taskstatus', 'YYSGServerPlayerController@change_task_status'); 
            Route::post('show/task', 'YYSGServerPlayerController@show_task');             
            //查询补储记录
  			Route::get('serach/restorelog', 'ServerPlayerController@restorelogload');
            Route::post('serach/restorelog', 'ServerPlayerController@restorelogserach');   
            //合服
  			Route::get('merge/servers', 'ServerPlayerController@mergeserversload');
            Route::post('merge/servers', 'ServerPlayerController@mergeserversmerge');
            //女神全服战
  			Route::get('all/server/fight', 'PromotionController@allServerFightIndex');
            Route::post('all/server/fight', 'PromotionController@allServerFightSet');
            Route::post('all/server/fight/look', 'PromotionController@allServerFightLook');
            //夜夜三国装备查询
			Route::get('log/equipment', 'YYSGServerPlayerController@playerEquipmentIndex');
            Route::post('log/equipment', 'YYSGServerPlayerController@playerEquipmentData');
            //夜夜三国武将获取日志
			Route::get('log/player/wj', 'YYSGServerPlayerController@playerGetWjIndex');
            Route::post('log/player/wj', 'YYSGServerPlayerController@playerGetWjData'); 
            //女神矿石争夺战
            Route::get('ore/fight','ShopController@oreFightIndex'); 
            Route::post('ore/fight','ShopController@oreFightOpenOrClose'); 
            Route::post('ore/fight/look','ShopController@oreFightLook');     
            //查询服务器VIP玩家
            Route::get('webgame/vipplayer', 'ServerPlayerController@WebGameVipPlayerIndex');
            Route::post('webgame/vipplayer', 'ServerPlayerController@WebGameVipPlayerGet');
            //萌娘三国操作服务器
            Route::get('mnsgserver/openandswitch', 'YYSGGMController@switchShowServer');
            Route::post('mnsgserver/openandswitch', 'YYSGGMController@switchShowServerDo'); 
            //萌娘三国操作玩家经济信息
            Route::get('mobilegame/editplayereconomy', 'YYSGGMController@editplayereconomyIndex');
            Route::post('mobilegame/editplayereconomy', 'YYSGGMController@editplayereconomyDo'); 
            //萌娘三国修复商店
            Route::get('mnsg/repair_player_shop', 'YYSGGMController@RepairPlayerShopIndex');
            Route::post('mnsg/repair_player_shop', 'YYSGGMController@RepairPlayerShopDo'); 
            //萌娘三国发布版本预告
            Route::get('mnsg/update_new', 'YYSGGMController@UpdateNewIndex');
            Route::post('mnsg/update_new', 'YYSGGMController@UpdateNewDeal'); 
            //风流三国增加删除坐骑
			Route::get('players/mount', 'GMController@setmountIndex');
            Route::post('players/mount', 'GMController@setMount');
            //萌娘三国设置饮料机热点
			Route::get('mnsg/setSoulCasket', 'MGSwitchController@MnsgsetSoulCasketIndex');
            Route::post('mnsg/setSoulCasket', 'MGSwitchController@MnsgsetSoulCasket');
            //风流三国重置帮派Boss
			Route::get('reset/leagueBoss', 'TournamentController@resetleagueBossIndex');
            Route::post('reset/leagueBoss', 'TournamentController@resetleagueBoss');
            //夜夜三国GM聊天群发回复
            Route::get('yysg/mass/gmTalkSend', 'YYSGGMController@gmMassTalk');
            Route::post('yysg/mass/gmTalkSend', 'YYSGGMController@gmMassMessageSend');
            //风流三国查看比赛信息
            Route::get('match/lookup', 'GSController@matchLookUpIndex');
            Route::post('match/lookup', 'GSController@matchLookUp');
            //风流三国设置运营排行榜奖励
            Route::get('rank/award/set', 'AwardSetController@setRankIndex');
            Route::post('rank/award/set', 'AwardSetController@setRankAward');
            //风流三国红包补次数
            Route::get('red/packet/num', 'ChangeYuanbaoController@redPacketIndex');
            Route::post('red/packet/num', 'ChangeYuanbaoController@replacementRedPacket');
            //页游定时活动相关操作
            Route::get('activity/timing', 'ActivityController@timingIndex');
            Route::post('activity/timing', 'ActivityController@timgingOperation');
            //夜夜三国限时抢购
            Route::get('yysg/flashsale', 'MGActivityController@FlashSaleIndex');
            Route::post('yysg/flashsale', 'MGActivityController@FlashSaleOperate');
            //夜夜三国发送武将
            Route::get('yysg/send/partner', 'YYSGGiftBagController@SendPartnerIndex');
            Route::post('yysg/send/partner', 'YYSGGiftBagController@SendPartnerOperate');
            //风流三国游戏通告
            Route::get('announce/welfare', 'AnnounceController@welfareIndex');
			Route::post('announce/welfare', 'AnnounceController@welfareSend');
			//风流三国玩家布阵信息
			Route::get('player/embattle', 'ServerPlayerController@playerEmbattleIndex');
			Route::post('player/embattle', 'ServerPlayerController@getPlayerEmbattle');
			//女神最强工会 竞技王设置奖励
			Route::get('guild/award/set', 'AwardSetController@guildAwardSetIndex');
			Route::post('guild/award/set', 'AwardSetController@guildAwardSet');
			//风流三国全名PK
			Route::get('cross/server/all/pk', 'CrossServerController@peoplePKIndex');
			Route::post('cross/server/all/pk', 'CrossServerController@peoplePK');
			//game_message表添加信息
			Route::get('file/game-message', 'FileController@gameMessageload');
			Route::post('file/game-message', 'FileController@gameMessage');
			//风流三国全服等级设置
			Route::get('all/server/level', 'CrossServerController@allServerLevelIndex');
			Route::post('all/server/level', 'CrossServerController@allServerLevel');
			//女神战神比赛
			Route::get('cross/mars', 'CrossServerController@marsIndex');
			Route::post('cross/mars', 'CrossServerController@crossMars');

		});

		//Platform Api
		Route::group(array('prefix' => 'platform-api'), function() {
			Route::get('user', 'PlatformUserController@index');
			Route::post('user', 'PlatformUserController@getUserInfo');
			Route::post('user/pwd', 'PlatformUserController@updatePassword');
			Route::get('user/pwd', 'PlatformUserController@editPassword');
			Route::post('order/offer', 'PlatformPaymentController@offerYuanbao');
			Route::get('user/login-master', 'PlatformUserController@loginMasterIndex');
			Route::post('user/login-master', 'PlatformUserController@loginMasterData');
			Route::get('user/bind-email', 'PlatformUserController@bindEmailIndex');
			Route::post('user/bind-email', 'PlatformUserController@bindEmailData');
			Route::get('user/anonymous', 'PlatformUserController@upgradeAnonymousIndex');
			Route::post('user/anonymous', 'PlatformUserController@upgradeAnonymous');
			Route::get('user/neiwan', 'PlatformUserController@neiwanIndex');
			Route::post('user/neiwan', 'PlatformUserController@neiwan');
			Route::get('payment/neworder', 'PlatformPaymentController@createNewOrderIndex');
			Route::post('payment/neworder', 'PlatformPaymentController@createNewOrder');
			Route::get('payment/createorder', 'PlatformPaymentController@createOrderIndex');
			Route::post('payment/createorder', 'PlatformPaymentController@createOrder');

			Route::get('payment/neworder-poker', 'PokerGiveGoldsController@addOrderIndex');
			Route::post('payment/neworder-poker', 'PokerGiveGoldsController@addOrderData');

			//操作待处理的订单 by taishou
			Route::post('payment/delayOrder', 'PlatformPaymentController@chuliDelayOrder');

			Route::get('payment/sdk_recharge', 'PlatformPaymentController@sdkRechargeIndex');
			Route::post('payment/sdk_recharge', 'PlatformPaymentController@sdkRecharge');
			//joyCard
			Route::get('payment/joycard', 'PlatformPaymentController@joyCardIndex');
			Route::post('payment/joycard/create', 'PlatformPaymentController@joyCardCreate');
			Route::post('payment/joycard/query', 'PlatformPaymentController@joyCardQuery');
			Route::post('payment/joycard/changeowner', 'PlatformPaymentController@joyCardChangeOwner');
			Route::post('payment/joycard/download', 'PlatformPaymentController@joyCardDownload');
			Route::get('payment/joycard/download', 'PlatformPaymentController@joyCardDownloadIndex');
//game_package
			Route::get('mobilegame/game_package', 'PlatformPaymentController@game_packageIndex');
			Route::get('mobilegame/game_package/modify', 'PlatformPaymentController@game_packageModifyIndex');
			Route::get('mobilegame/game_package/add', 'PlatformPaymentController@game_packageAddNewIndex');
			Route::post('mobilegame/game_package/modify', 'PlatformPaymentController@game_packageAddModify');

			Route::get('mobilegame/game_package/sdk_ad_info', 'PlatformInformationController@GamePackageAdIndex');
			Route::post('mobilegame/game_package/sdk_ad_info', 'PlatformInformationController@GamePackageAdModify');

//google validate
			Route::get('ggvalidate/showdata','PlatformPaymentController@ggvalidateData');
			Route::get('ggvalidate/modify','PlatformPaymentController@ggvalidateModify');
			Route::get('ggvalidate/add','PlatformPaymentController@ggvalidateAdd');	
			Route::post('ggvalidate/modify','PlatformPaymentController@ggvalidateUpdate');	

//third product  modify & add
            Route::get('third_product/showdata','PlatformPaymentController@thirdproductData');
            Route::get('third_product/modify','PlatformPaymentController@thirdproductModify');
            Route::get('third_product/add','PlatformPaymentController@thirdproductAdd');
            Route::post('third_product/modify','PlatformPaymentController@thirdproductUpdate');

//mobile_payment_method Modify & Add
			Route::get('mobile_payment_method/showdata','PlatformPaymentController@paymentMethodData');
			Route::get('mobile_payment_method/modify','PlatformPaymentController@paymentMethodModify');
			Route::get('mobile_payment_method/add','PlatformPaymentController@paymentMethodAdd');
			Route::get('mobile_payment_method/queryview','PlatformPaymentController@paymentMethodQueryview');
			Route::post('mobile_payment_method/query','PlatformPaymentController@paymentMethodQuery');	
			Route::post('mobile_payment_method/modify','PlatformPaymentController@paymentMethodUpdate');
//手机推送
			Route::get('user/mobile_push','PlatformUserController@mobilePushIndex');
			Route::post('user/mobile_push','PlatformUserController@mobilePushUpdate');
			//官网小助手相关
			Route::get('mobilegame/helper','PlatformHelperController@helperIndex');
			Route::post('mobilegame/helper','PlatformHelperController@helperFunctionModify');
			Route::get('mobilegame/helper/single_function', 'PlatformHelperController@helpersinglefunction');
			Route::post('mobilegame/helper/single_function','PlatformHelperController@SingleFunctionDeal');
			//游戏信息
			Route::post('mobilegame/formdata_modify','PlatformInformationController@formdata_modify');
			
			Route::post('payment/tradeseq/add', 'PlatformPaymentController@modifyTradeseq');
			Route::post('payment/confirmyuanbao', 'PlatformPaymentController@confirmYuanbao');
			Route::get('user/award/set', 'PlatformUserController@awardSetIndex');
			Route::post('user/award/set', 'PlatformUserController@awardSet');
		});
		//Slave Api
		Route::group(array('prefix' => 'slave-api'), function() {
			Route::get('login/trend', 'SlaveApiLoginLogController@getOnlineTrend');
			Route::post('login/trend', 'SlaveApiLoginLogController@getOnlineTrendData');
			Route::get('login/total', 'SlaveApiLoginLogController@getLoginTotal');
			Route::post('login/total', 'SlaveApiLoginLogController@getLoginTotalData');
			//夜夜三国查询玩家生命周期
			Route::get('player/lifetime', 'YYSGServerPlayerController@userlifetimeIndex');
			Route::post('player/lifetime', 'YYSGServerPlayerController@userlifetimeData');
			//注册用户统计--运营
			Route::get('users/signnum', 'SlaveApiUserController@signnumusersload');
			Route::post('users/signnum', 'SlaveApiUserController@signnumusersdata');  
			
			Route::get('player/playerinfo', 'SlaveApiCreatePlayerLogController@getCreatePlayerInfo');
			Route::post('player/playerinfo', 'SlaveApiCreatePlayerLogController@getCreatePlayerInfoData');
			Route::get('player/rank', 'SlaveApiCreatePlayerLogController@getPlayerRank');
			Route::post('player/rank', 'SlaveApiCreatePlayerLogController@getPlayerRankData');
			Route::post('player/rank/download', 'SlaveApiCreatePlayerLogController@downloadGetPlayerRankData');
			Route::get('player/rank/download', 'SlaveApiCreatePlayerLogController@downloadGetPlayerRank');
			Route::get('player/trend', 'SlaveApiCreatePlayerLogController@getPlayerLevelTrend');
			Route::post('player/trend', 'SlaveApiCreatePlayerLogController@getPlayerLevelTrendData');
			
			Route::get('player/getPlayerInfo', 'SlaveApiCreatePlayerLogController@getPlayerRetention');
			
			Route::get('player/retention', 'SlaveApiCreatePlayerLogController@getPlayerRetention');
			Route::post('player/retention', 'SlaveApiCreatePlayerLogController@getPlayerRetentionData');

			Route::get('player/channel/retention', 'SlaveApiCreatePlayerLogController@getPlayerChannelRetention');
			Route::post('player/channel/retention', 'SlaveApiCreatePlayerLogController@getPlayerChannelRetentionData');

			Route::get('player/levelup', 'SlaveApiCreatePlayerLogController@getPlayerLevelUp');
			Route::post('player/levelup', 'SlaveApiCreatePlayerLogController@getPlayerLevelUpData');	
			Route::get('economy/all-server', 'SlaveApiEconomyLogController@allServerIndex');
			Route::post('economy/all-server', 'SlaveApiEconomyLogController@sendAllServer');
			Route::get('economy/all-server-down', 'SlaveApiEconomyLogController@downloadAllServerIndex');
			Route::post('economy/all-server-down', 'SlaveApiEconomyLogController@downloadAllServerData');
			Route::get('economy/player', 'SlaveApiEconomyLogController@playerIndex');
			Route::post('economy/player', 'SlaveApiEconomyLogController@sendPlayer');
			Route::get('economy/rank', 'SlaveApiEconomyLogController@rankIndex');
			Route::post('economy/rank', 'SlaveApiEconomyLogController@sendRank');
			
			Route::get('economy/analysis', 'SlaveApiEconomyLogController@analysisIndex');
			Route::post('economy/analysis', 'SlaveApiEconomyLogController@analysis');
			Route::get('economy/find-boss-killer', 'SlaveApiEconomyLogController@findBossKillerIndex');
			Route::post('economy/find-boss-killer', 'SlaveApiEconomyLogController@findBossKiller');
			Route::get('payment/order/stat', 'SlaveApiPaymentController@orderStatIndex');
			Route::post('payment/order/stat', 'SlaveApiPaymentController@sendOrderStatData');
			Route::get('economy/allserver-consume','SlaveApiEconomyLogController@getAllServersConsumeIndex');
			Route::post('economy/allserver-consume','SlaveApiEconomyLogController@getAllServersConsume');
			
			Route::get('payment/order/list', 'SlaveApiPaymentController@orderListIndex');
			Route::post('payment/order/list', 'SlaveApiPaymentController@orderListData');
			Route::post('payment/order/download', 'SlaveApiPaymentController@downloadOrderListData');
			Route::get('payment/order/download', 'SlaveApiPaymentController@downloadOrderListIndex');
			
			Route::get('payment/order/unpay', 'SlaveApiPaymentController@unpayIndex');
			Route::post('payment/order/unpay', 'SlaveApiPaymentController@unpayData');
			Route::post('payment/order/unpay-msg', 'SlaveApiPaymentController@unPayMsg');
			Route::get('payment/order/rank', 'SlaveApiPaymentController@yuanbaoIndex');
			Route::post('payment/order/rank', 'SlaveApiPaymentController@yuanbaoData');
			Route::get('payment/order/mgrank', 'SlaveApiPaymentController@yuanbaoIndexforMG');
			Route::post('payment/order/mgrank', 'SlaveApiPaymentController@yuanbaoDataforMG');
			Route::post('payment/rank/download', 'SlaveApiPaymentController@downloadYuanbaoData');
			Route::get('payment/rank/download', 'SlaveApiPaymentController@downloadYuanbaoIndex');

			Route::get('payment/order/rank-cehua', 'SlaveApiPaymentController@yuanbaoIndex');
			Route::post('payment/order/rank-cehua', 'SlaveApiPaymentController@yuanbaoData');
			
			Route::get('payment/order', 'SlaveApiPaymentController@orderPlayerIndex');
			Route::post('payment/order', 'SlaveApiPaymentController@orderPlayerData');
			Route::get('payment/order/dispute', 'SlaveApiPaymentController@disputeOrderIndex');
			Route::post('payment/order/dispute', 'SlaveApiPaymentController@disputeOrder');
			Route::post('payment/order/dispute/act', 'SlaveApiPaymentController@disputeOrderAct');
			Route::get('payment/order/refund', 'SlaveApiPaymentController@getRefundOrders');
			Route::post('payment/order/refund', 'SlaveApiPaymentController@getRefundOrdersData');
			Route::post('payment/order/refund/act', 'SlaveApiPaymentController@refundOrderAct');
			Route::get('payment/order/record', 'SlaveApiPaymentController@getRecordOrders');
			Route::post('payment/order/record', 'SlaveApiPaymentController@dealRecordOrders');
			Route::get('payment/order/record/gm', 'SlaveApiPaymentController@getRecordOrders');
			Route::post('payment/order/record/gm', 'SlaveApiPaymentController@dealRecordOrders');
			Route::get('payment/order/award', 'SlaveApiPaymentController@getAwardOrders');
			Route::post('payment/order/award', 'SlaveApiPaymentController@dealRecordOrders');
			Route::get('payment/order/award/gm', 'SlaveApiPaymentController@getAwardOrders');
			Route::post('payment/order/award/gm', 'SlaveApiPaymentController@dealRecordOrders');
			Route::get('payment/pay-type', 'SlaveApiPaymentController@payTypeIndex');
			Route::post('payment/pay-type', 'SlaveApiPaymentController@getPayTypeStat');
			

			//与上面的功能类似，不过不显示金额
			Route::get('payment/pay-type-onlyrate', 'SlaveApiPaymentController@payTypeIndex');
			Route::post('payment/pay-type-onlyrate', 'SlaveApiPaymentController@getPayTypeStat');
			//单服回报
			Route::get('payment/server/revenue', 'SlaveApiPaymentController@getServerRevenueByDay');
			Route::post('payment/server/revenue', 'SlaveApiPaymentController@getServerRevenueByDayData');
			//服务器数据对比
			Route::get('compare/server/data', 'SlaveApiPaymentController@getServerDataCompareByDay');
			Route::post('compare/server/data', 'SlaveApiPaymentController@getServerDataCompareByDayData');

			Route::get('user/stat', 'SlaveApiUserController@userStatIndex');
			Route::post('user/stat', 'SlaveApiUserController@sendUserStatData');
			//安装用户统计
			Route::get('setup/stat', 'SlaveApiUserController@SetupStatIndex');
			Route::post('setup/stat', 'SlaveApiUserController@sendSetupStatData');
			//安装用户统计--运营
			Route::get('setup/stat/yy', 'SlaveApiUserController@SetupStatIndexForYY');
			Route::post('setup/stat/yy', 'SlaveApiUserController@sendSetupStatDataForYY');
			//尝试新的方法统计注册用户
			Route::get('user/stat/test', 'SlaveApiUserController@userStatIndextest');
			Route::post('user/stat/test', 'SlaveApiUserController@sendUserStatDatatest');

			Route::get('user/statyy', 'SlaveApiUserController@userStatIndexyy');
			Route::post('user/statyy', 'SlaveApiUserController@sendUserStatDatayy');			
			Route::post('user/stat/download', 'SlaveApiUserController@downloadUserStatData');
			Route::get('user/stat/download', 'SlaveApiUserController@downloadUserStatIndex');

			Route::get('user/device', 'SlaveApiUserController@userDeviceIndex');
			Route::post('user/device', 'SlaveApiUserController@userDeviceData');

			Route::get('user/device/yy', 'SlaveApiUserController@userDeviceIndexForYY');
			Route::post('user/device/yy', 'SlaveApiUserController@userDeviceDataForYY');

			Route::get('user/sxd/stat', 'SlaveApiUserController@SXDUserStatIndex');
			Route::post('user/sxd/stat', 'SlaveApiUserController@SXDSendUserStatData');

			Route::get('user/sxd/fb', 'SXDGameController@SXDFBStatIndex');
			Route::post('user/sxd/fb', 'SXDGameController@SXDFBStatData');

			Route::get('user/fb', 'SlaveApiUserController@FBStatIndex');
			Route::post('user/fb', 'SlaveApiUserController@FBStatData');

			Route::get('user/th/stat', 'HazgGameController@THUserStatIndex');
			Route::post('user/th/stat', 'HazgGameController@THSendUserStatData');

			Route::get('user/th/fb', 'HazgGameController@THFBStatIndex');
			Route::post('user/th/fb', 'HazgGameController@THFBStatData');


			Route::get('user/weekly', 'SlaveApiUserController@weeklyReportIndex');
			Route::post('user/weekly', 'SlaveApiUserController@weeklyReportData');
			
			Route::get('user/player', 'SlaveApiUserController@createdPlayerIndex');
			Route::post('user/player', 'SlaveApiUserController@createdPlayerData');
			Route::get('user/channel', 'SlaveApiUserController@getChannelStat');
			Route::post('user/channel', 'SlaveApiUserController@getChannelStatData');
			Route::get('user/channel/yy', 'SlaveApiUserController@getChannelStatForYY');
			Route::post('user/channel/yy', 'SlaveApiUserController@getChannelStatDataForYY');
			Route::get('eb/log/skip', 'SlaveApiLogFileController@getNewUrl');
			Route::get('eb/log', 'SlaveApiLogFileController@getFile');
			Route::get('economy/server-consume', 'SlaveApiPaymentController@serverConsumeIndex');
			Route::post('economy/server-consume', 'SlaveApiPaymentController@serverConsumeData');

			Route::post('economy/server-consume/download', 'SlaveApiPaymentController@downloadServerConsumeData');
			Route::get('economy/server-consume/download', 'SlaveApiPaymentController@downloadServerConsumeIndex');
			
			//德州扑克
			Route::get('poker/revenue', 'PokerPaymentController@pokerUserActivateIndex');
			Route::get('poker/payment', 'PokerPaymentController@pokerOrderStatIndex');
			Route::post('poker/revenue', 'PokerPaymentController@pokerUserActivateData');
			Route::post('poker/payment', 'PokerPaymentController@pokerOrderStatData');
			Route::get('poker/pay-rate', 'PokerPaymentController@pokerPayRateIndex');
			Route::post('poker/pay-rate', 'PokerPaymentController@pokerPayRateData');
			Route::get('poker/pay-new', 'PokerPaymentController@pokerNewUserPayIndex');
			Route::post('poker/pay-new', 'PokerPaymentController@pokerNewUserPayData');
			Route::get('poker/user-log', 'PokerPaymentController@pokerUserAnaysisIndex');
			Route::post('poker/user-log', 'PokerPaymentController@pokerUserAnaysisData');
			Route::get('poker/user-rank', 'PokerPaymentController@pokerUserRankIndex');
			Route::post('poker/user-rank', 'PokerPaymentController@pokerUserRankData');
			Route::get('poker/user-paydetail', 'PokerPaymentController@pokerUserPayInfoIndex');
			Route::post('poker/user-paydetail', 'PokerPaymentController@pokerUserPayInfoData');
			Route::get('poker/pay-allserver', 'PokerPaymentController@pokerPayAllServerIndex');
			Route::post('poker/pay-allserver', 'PokerPaymentController@pokerPayAllServerData');
			Route::get('poker/cash', 'PokerPaymentController@pokerCashIndex');
			Route::post('poker/cash', 'PokerPaymentController@pokerCashSend');
			Route::post('poker/cash-update', 'PokerPaymentController@pokerCashUpdate');
			Route::get('poker/rounds', 'PokerGiveChipsController@pokerRoundsIndex');
			Route::post('poker/rounds', 'PokerGiveChipsController@pokerRoundsData');

			Route::get('poker/info', 'PokerGiveChipsController@pokerInfoIndex');
			Route::post('poker/info', 'PokerGiveChipsController@pokerInfoData');

			Route::get('poker/chip-info', 'PokerGiveGoldsController@pokerChipsInfoIndex');
			Route::post('poker/chip-info', 'PokerGiveGoldsController@pokerChipsInfoData');

			Route::get('poker/user-chips', 'PokerGiveChipsController@allChipsIndex');
			Route::post('poker/user-chips', 'PokerGiveChipsController@allChipsData');

			Route::get('economy/find-boss-killer-num', 'SlaveApiEconomyLogController@findBossKillerNumIndex');
			Route::post('economy/find-boss-killer-num', 'SlaveApiEconomyLogController@findBossKillerNumData');

			Route::get('economy/find-rank-three', 'SlaveApiEconomyLogController@findRankThreeIndex');
			Route::post('economy/find-rank-three', 'SlaveApiEconomyLogController@findRankThree');
			//策划充值元宝查询
			Route::get('payment/order/search-cehua', 'SlaveApiPaymentController@yuanbaoSearchIndex');
			Route::post('payment/order/search-cehua', 'SlaveApiPaymentController@yuanbaoSearchData');
			//夜夜三国消费数据查询
			Route::get('economy/yysg/player', 'SlaveApiEconomyLogController@yysgPlayerIndex');
			Route::post('economy/yysg/player', 'SlaveApiEconomyLogController@yysgSendPlayer');
			//查询sql-Panda
			Route::get('input/sql', 'SlaveApiSqlController@inputSqlIndex');
			Route::post('input/sql', 'SlaveApiSqlController@inputSqlDeal');
			Route::post('input/sql/download', 'SlaveApiSqlController@inputSqlDownload');
			Route::get('input/sql/download', 'SlaveApiSqlController@inputSqlDownloadIndex');
			//玩家流失
			Route::get('player/outflow','SlaveApiLoginLogController@playerOutflowData');
			Route::post('player/outflow','SlaveApiLoginLogController@playerOutflowQuery');
			//页游查询异常消费数据	
			Route::get('economy/player/abnormal', 'SlaveApiEconomyLogController@abnormalIndex');
			Route::post('economy/player/abnormal', 'SlaveApiEconomyLogController@abnormalDada');	
			//模糊查询玩家信息
			Route::get('player/info/like', 'SlaveApiUserController@playerinfolike');
			Route::post('player/info/like', 'SlaveApiUserController@playerinfocheck');	
			//德扑查询日报信息
			Route::get('joyspade/daily/data', 'PokerQueryController@dailydataindex');
			Route::post('joyspade/daily/data', 'PokerQueryController@dailydataquery');	
			//查询历史GM回复信息
			Route::get('gm/message', 'SlaveApiUserController@gmMessageLikeIndex');
			Route::post('gm/message', 'SlaveApiUserController@gmMessageLike');	
			//查询统计GM回复率、GM平均回复时间
			Route::get('gm/message/reply', 'SlaveApiUserController@gmMessageReply');
			Route::post('gm/message/reply', 'SlaveApiUserController@gmMessageReplyData');
			//查询日元宝消费各项占比
			Route::get('economy/parts', 'SlaveApiEconomyLogController@SpendonPartsIndex');
			Route::post('economy/parts', 'SlaveApiEconomyLogController@SpendonPartsData');	
			//查询时间段充值信息
			Route::get('payment/infooftime', 'SlaveApiPaymentController@PaymentInfoIndex');
			Route::post('payment/infooftime', 'SlaveApiPaymentController@PaymentInfoData');	
			//根据经济日志查询服务器剩余元宝量
			Route::get('economy/remainyuanbao', 'SlaveApiEconomyLogController@RemainYuanbaoIndex');
			Route::post('economy/remainyuanbao', 'SlaveApiEconomyLogController@RemainYuanbaoData');
			//用户消费排行
			Route::get('user/consumption/rank', 'SlaveApiUserController@consumptionRankIndex');
			Route::post('user/consumption/rank', 'SlaveApiUserController@consumptionRankData');	
			//查询时间段内服务器付费人数和金额
			Route::get('economy/expensesum','SlaveApiPaymentController@expenseSumIndex');
			Route::post('economy/expensesum','SlaveApiPaymentController@expenseSumData');
			//首次付费数据分析
			Route::get('payment/firstpayinfo', 'SlaveApiPaymentController@FirstPayInfoIndex');
			Route::post('payment/firstpayinfo', 'SlaveApiPaymentController@FirstPayInfoDo');	
			//查询时间段内设备新增用户
			Route::get('user/devicesearch','SlaveApiUserController@userDeviceSearchIndex');
			Route::post('user/devicesearch','SlaveApiUserController@userDeviceSearchData');
			//活动数据分析
			Route::get('activity/analysis', 'SlaveApiEconomyLogController@ActivityAnalysisIndex');
			Route::post('activity/analysis', 'SlaveApiEconomyLogController@ActivityAnalysis');
			//手游武将召唤情况，对应log_create_partner
			Route::get('partner/log', 'YYSGGMController@CountPartnerLogIndex');
			Route::post('partner/log', 'YYSGGMController@CountPartnerLog');
			//基本情况统计
			Route::get('user/basic/count', 'SlaveApiUserController@basicCountIndex');
			Route::post('user/basic/count', 'SlaveApiUserController@basicCountData');
			//查询当前游戏的礼包信息
			Route::get('giftbag/message', 'SlaveApiPaymentController@GiftbagMessageIndex');
			Route::post('giftbag/message', 'SlaveApiPaymentController@GiftbagMessage');
			//查询活动礼包信息
			Route::get('poker/activity/data', 'PokerQueryController@ActivityDataIndex');
			Route::post('poker/activity/data', 'PokerQueryController@ActivityData');
			//夜夜三国查看新手打点情况
			Route::get('yysg/newer/point', 'YYSGServerPlayerController@NewerPointIndex');
			Route::post('yysg/newer/point', 'YYSGServerPlayerController@NewerPointData');
			//風流三國神树和大乱斗排行查询
			Route::get('score/rank/log', 'SlaveApiCreatePlayerLogController@ScoreRankIndex');
			Route::post('score/rank/log', 'SlaveApiCreatePlayerLogController@ScoreRankData');
			//手游接入流程
			Route::get('mobilegame/mobilegamesprocedure', 'MobileGameController@MobileGamesProcedureIndex');
			Route::post('mobilegame/mobilegamesprocedure', 'MobileGameController@MobileGamesProcedureData');
			Route::post('mobilegame/update', 'MobileGameController@UpdateData');
			//对接文档上传下载
			Route::get('mobilegame/uploaddoc', 'MobileGameController@uploadDocIndex');
			Route::post('mobilegame/uploaddoc', 'MobileGameController@uploadDocData');
			//夜夜三国查询giftbox表
			Route::get('yysg/giftbox', 'SlaveApiGiftBoxController@giftboxYYSGIndex');
			Route::post('yysg/giftbox', 'SlaveApiGiftBoxController@giftboxYYSGDo');
			//VIP用户
			Route::get('vip/players', 'SlaveApiUserController@vipplayersIndex');
			Route::post('vip/players', 'SlaveApiUserController@vipplayersmodify');
			//忍者之王奖励发放记录
			Route::get('rzzw/reward/record', 'SlaveApiGiftBoxController@RzzwRewardIndex');
			Route::post('rzzw/reward/record', 'SlaveApiGiftBoxController@RzzwRewardUpdate');
			//萌娘三国查询log_summon
			Route::get('mnsg/summon/record', 'SlaveApiLogSummonController@mnsglogsummonIndex');
			Route::post('mnsg/summon/record', 'SlaveApiLogSummonController@mnsglogsummon');
			//充值信息过滤
			Route::get('payorder/filter', 'SlaveApiPaymentController@payorderfilterIndex');
			Route::post('payorder/filter', 'SlaveApiPaymentController@payorderfilter');
			//根据输入的设备查询对应的数据
			Route::get('input/device/user', 'SlaveApiUserController@deviceuserIndex');
			Route::post('input/device/user', 'SlaveApiUserController@deviceuser');

			//将要写一些德扑相关的功能
			//注册相关
			Route::get('poker/signupcreate/info', 'PokerQueryController@SignupCreateIndex');
			Route::post('poker/signupcreate/info', 'PokerQueryController@SignupCreateQuery');
			//留存计算
			Route::get('calculate/retention', 'SlaveApiUserController@CalculateRetentionIndex');
			Route::post('calculate/retention', 'SlaveApiUserController@CalculateRetention');
			//德扑破产信息
			Route::get('poker/bankruptcy', 'PokerQueryController@BankruptcyIndex');
			Route::post('poker/bankruptcy', 'PokerQueryController@BankruptcyData');
			//游戏基础信息
			Route::get('game/basic/info', 'SlaveApiBasicDataController@BasicInfoIndex');
			Route::post('game/basic/info', 'SlaveApiBasicDataController@BasicInfoQuery');
			//风流三国将魂日志
			Route::get('flsg/mergegem/log', 'SlaveApiSimpleLogController@MergeGemIndex');
			Route::post('flsg/mergegem/log', 'SlaveApiSimpleLogController@getMergeGemData');
			//operation日志查询
			Route::get('operation/log', 'SlaveApiSimpleLogController@operationIndex');
			Route::post('operation/log', 'SlaveApiSimpleLogController@getOperationData');
			//萌娘item表统计
			Route::get('mg/item/count', 'SlaveApiItemController@ItemCountIndex');
			Route::post('mg/item/count', 'SlaveApiItemController@ItemCountData');
			//创建玩家信息
			Route::get('server/create/players', 'SlaveApiCreatePlayerLogController@ServerCreatePlayersIndex');
			Route::post('server/create/players', 'SlaveApiCreatePlayerLogController@ServerCreatePlayersData');
			//学妹阵容信息
			Route::get('mnsg/formation', 'SlaveApiSimpleLogController@formationIndex');
			Route::post('mnsg/formation', 'SlaveApiSimpleLogController@formationData');
			//美人猜猜猜日志查询
			Route::get('flsg/guess/log', 'SlaveApiSimpleLogController@belleLogIndex');
			Route::post('flsg/guess/log', 'SlaveApiSimpleLogController@belleLogSearch');
			//夜夜三国武将分解记录
			Route::get('yysg/player/partnerdel', 'SlaveApiSimpleLogController@partnerDelIndex');
			Route::post('yysg/player/partnerdel', 'SlaveApiSimpleLogController@partnerDel');
		});

		Route::controller('/', 'HomeController');
	}
);