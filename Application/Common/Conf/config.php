<?php
return array(

		'DEFAULT_MODULE'     => 'Home', //é»˜è®¤æ¨¡å—
    	'URL_MODEL'          => 2, //URLæ¨¡å¼
    	'SESSION_AUTO_START' => true, //æ˜¯å¦å¼€å¯session	
	// 'TMPL_PARSE_STRING'=> array(
	//  '__UPLOAD__' => __ROOT__.'/uploads'
	//  ),
    	//'SHOW_PAGE_TRACE' =>true, 
    	'DB_TYPE'   => 'mysql', // æ•°æ®åº“ç±»åž?
	'DB_HOST'   => '127.0.0.1', // æœåŠ¡å™¨åœ°å€
	'DB_NAME'   => 'goto', // æ•°æ®åº“å
	'DB_USER'   => 'root', // ç”¨æˆ·å?
	'DB_PWD'    => '123456', // å¯†ç 
	'DB_PORT'   => 3306, // ç«¯å£
	'DB_PREFIX' => 'hl_', // æ•°æ®åº“è¡¨å‰ç¼€ 
	'DB_CHARSET'=> 'utf8', // å­—ç¬¦é›
	'DB_DEBUG'  =>  TRUE, // æ•°æ®åº“è°ƒè¯•æ¨¡å¼?å¼€å¯åŽå¯ä»¥è®°å½•SQLæ—¥å¿— 3.2.3æ–°å¢ž
	//'TMPL_ACTION_ERROR' => APP_PATH . 'Common/dispatch_jump.tpl',
	//'TMPL_ACTION_SUCCESS' => APP_PATH . 'Common/dispatch_jump.tpl',
	//'URL_MODEL'=>1,
	//
	
	'DB_PARAMS' => array(\PDO::ATTR_CASE => \PDO::CASE_NATURAL),
	'gg'=>array(
		'1'=>array('desc'=>'主页 banner广告一 1100*140','url'=>'/uploads/gg/20161129153841733.jpg'),
		'2'=>array('desc'=>'主页 底部广告展示图 1100*140','url'=>'/uploads/gg/20161129153854613.jpg'),
		'3'=>array('desc'=>'主页 直播部分左一展示广告 260*500','url'=>'/uploads/gg/20161129175404804.jpg'),
		'4'=>array('desc'=>'主页 左右2侧广告图 210*510 左侧填写在大图里 右侧填写在小图里','url'=>'/uploads/gg/20161129153818467.jpg'),
		'5'=>array('desc'=>'直播间首页顶部主体广告展示图( PC大屏1900*499,移动小屏幕1080*460 需要传2张图','url'=>'/uploads/gg/20161129154136741.jpg'),
		'6'=>array('desc'=>'直播间部分 中部广告展示图 1060*200','url'=>'/uploads/gg/20161129153931660.jpg'),
		'7'=>array('desc'=>'精选视频顶部主体广告展示图( PC大屏1900*499,移动小屏幕1080*460 需要传2张图','url'=>'/uploads/gg/20161129154210997.jpg'),
		'8'=>array('desc'=>'精选视频（点播）播放部分 中部广告展示图：1060*200','url'=>'/uploads/gg/20161129153959252.jpg'),
	),

	// 微信登录 的id 和 秘钥 (pc 和 公众平台)
	'appid'=>'wxb2653546d2791dec',
	'appsecret'=>'30dd6aa339cdee6e3aff891ad4c1e050',
	
	'pappid'=>'wx0352f21f4bc5cc08',
	'pappsecret'=>'d6d0c1d1951891bd488259dc5bda44c0',
	
	// 阿里直播应用名称
	'DomainName'=>'http://gotozb.yicehua.cn/',
	
	// 百度api
	'ipurl'=>'http://api.map.baidu.com/location/ip?ak=yTrxi0B7AhMaMXVQwaKHslYZzHTQzzU1',
	// 'ipak'=>'CB74iMN48e8R3FFSdS1YfrRr14bfZOb2',

	// qq登录 ak 和 ap
    'qqid'=>'101389568',
    'qqsecret'=>'22e6b2228675e39de3f5d558743ef2d2',

	'sinaid'=>'2023471864',
	'sinasecret'=>'d8bdb7c18d748625d5c09d7f72023643',
	
	'WEB'=>"http://goto.yicehua.cn",
);
