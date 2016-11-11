<?php
$redis = new Redis();
$redis->pconnect('127.0.0.1');

if ($redis) {

	$OnlineList = $redis->hLen('OnlineUser');
	


	echo $OnlineList;



}




?>