<?php


/*
	这个文件里包括了一些在很多地方可以用到的配置
	包括一些特殊的游戏的数据库等配置
	包括一些游戏的分组等
*/

return array(

	'agent_games' => array(38, 51, 55, 58, 65, 67, 77, 86),	//代理的游戏，某些特殊的判断可能用到

	'all_mobilegameids' => array(51,52,54,55,57,58,65,66,67,69,71,72,73,74,75,76,77,79,80,81,82,83,84,85,86),	//所有手游，不区分是否自研	Config::get('game_config.all_mobilegameids')

	'mobilegames' => array(54,66,69,72,73,74,75,76,78,79,80,81,82,83,84,85),	//自研手游	Config::get('game_config.mobilegames')

	'yysggameids' => array(54,69,72,73,74,75,76,80,81),	//夜夜三国游戏	Config::get('game_config.yysggameids')

	'mnsggameids' => array(66, 78, 79, 82, 83, 84, 85),	//萌娘三国游戏	Config::get('game_config.mnsggameids')

//夜夜三国配置
	'54' => array(
		'main_server'	=>	1,
		'database'	=>	'54.1',
		'qiqiwu'	=>	'qiqiwu',
		'payment'	=>	'payment',
		'platform_id'	=>	1,
		'language'	=>	'zh_TW',
		),

	'69' =>	array(
		'main_server'	=>	1,
		'database'	=>	'69.1',
		'qiqiwu'	=>	'qiqiwu_29',
		'payment'	=>	'payment_29',
		'platform_id'	=>	29,
		'language'	=>	'en_EN',
		),

	'72' =>	array(
		'main_server'	=>	1,
		'database'	=>	'72.1',
		'qiqiwu'	=>	'qiqiwu_55',
		'payment'	=>	'payment_55',
		'platform_id'	=>	55,
		'language'	=>	'en_US',
		),

	'73' =>	array(
		'main_server'	=>	1,
		'database'	=>	'73.1',
		'qiqiwu'	=>	'qiqiwu_38',
		'payment'	=>	'payment_38',
		'platform_id'	=>	38,
		'language'	=>	'id_ID',
		),

	'74' =>	array(
		'main_server'	=>	1,
		'database'	=>	'74.1',
		'qiqiwu'	=>	'qiqiwu',
		'payment'	=>	'payment',
		'platform_id'	=>	2,
		'language'	=>	'vn_VN',
		),

	'75' =>	array(
		'main_server'	=>	1,
		'database'	=>	'75.1',
		'qiqiwu'	=>	'qiqiwu',
		'payment'	=>	'payment',
		'platform_id'	=>	3,
		'language'	=>	'th_TH',
		),

	'76' =>	array(
		'main_server'	=>	5,
		'database'	=>	'76.5',
		'qiqiwu'	=>	'qiqiwu',
		'payment'	=>	'payment',
		'platform_id'	=>	56,
		'language'	=>	'zh_CN',
		),

	'80' =>	array(
		'main_server'	=>	1,
		'database'	=>	'80.1',
		'qiqiwu'	=>	'qiqiwu_58',
		'payment'	=>	'payment_58',
		'platform_id'	=>	58,
		'language'	=>	'ko_KR',
		),

	'81' =>	array(
		'main_server'	=>	5,
		'database'	=>	'81.5',
		'qiqiwu'	=>	'qiqiwu_59',
		'payment'	=>	'payment_59',
		'platform_id'	=>	59,
		'language'	=>	'zh_CN',
		),

//萌娘三国配置
	'66' => array(
		'main_server'	=>	1,
		'database'	=>	'66.1',
		'qiqiwu'	=>	'qiqiwu',
		'payment'	=>	'payment',
		'platform_id'	=>	1,
		),

	'78' => array(
		'main_server'	=>	1,
		'database'	=>	'78.1',
		'qiqiwu'	=>	'qiqiwu_57',
		'payment'	=>	'payment_57',
		'platform_id'	=>	57,
		),

	'79' => array(
		'main_server'	=>	1,
		'database'	=>	'79.1',
		'qiqiwu'	=>	'qiqiwu_29',
		'payment'	=>	'payment_29',
		'platform_id'	=>	29,
		),

	'82' => array(
		'main_server'	=>	1,
		'database'	=>	'82.1',
		'qiqiwu'	=>	'qiqiwu_38',
		'payment'	=>	'payment_38',
		'platform_id'	=>	38,
		),

	'83' => array(
		'main_server'	=>	1,
		'database'	=>	'83.1',
		'qiqiwu'	=>	'qiqiwu',
		'payment'	=>	'payment',
		'platform_id'	=>	2,
		),

	'84' => array(
		'main_server'	=>	1,
		'database'	=>	'84.1',
		'qiqiwu'	=>	'qiqiwu',
		'payment'	=>	'payment',
		'platform_id'	=>	3,
		),

	'85' => array(
		'main_server'	=>	1,
		'database'	=>	'85.1',
		'qiqiwu'	=>	'qiqiwu_60',
		'payment'	=>	'payment_60',
		'platform_id'	=>	60,
		),
);