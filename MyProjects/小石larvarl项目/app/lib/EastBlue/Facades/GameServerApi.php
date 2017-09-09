<?php namespace EastBlue\Facades;

use \Illuminate\Support\Facades\Facade;

class GameServerApi extends Facade {

    // Announce，关于公告的常量
    const ANNOUNCE_INTERVAL_TYPE_REPEAT = 1;

    const ANNOUNCE_INTERVAL_TYPE_ONCE = 2;

    const ANNOUNCE_INTERVAL_TYPE_TIME_DOWN = 3;

    const ANNOUNCE_POSITION_CENTER = 1;

    const ANNOUNCE_POSITION_CHAT = 2;

    const ANNOUNCE_POSITION_BOTH = 3;
    
    // GM
    const GM_TYPE_BUG = 1;

    const GM_TYPE_COMPLAINT = 2;

    const GM_TYPE_ADVICE = 3;

    const GM_TYPE_OTHER = 4;
    
    // QQ
    const QQ_TASK_TYPE_CHECK = 1;

    const QQ_TASK_TYPE_CHECK_AWARD = 2;

    const QQ_TASK_TYPE_AWARD = 3;
    
    // Match
    const MATCH_TYPE_ZHENGBA = 1;

    const MATCH_TYPE_KUAFU_ZHENGBA = 2;

    //WarLords 
    const MATCH_TYPE_WARLORD = 5;

    //天下第一
    const MATCH_TYPE_TIANXIA_DIYI=6;

    //天下第一
    const MATCH_TYPE_TIANXIA_DIYI_X=7;

	protected static function getFacadeAccessor() {
		return 'gameserverapi'; 
	}
}