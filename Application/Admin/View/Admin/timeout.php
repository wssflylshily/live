<?php 

	ignore_user_abort();  // 浏览器关闭一样能执行
	set_time_limit(0);  // php 无限执行

	$interval = 10;

	file_put_contents('E:/a.txt','123',FILE_APPEND);

	sleep($interval);
 ?>

	