<?php
	set_time_limit (600);
	header('Content-Type: application/json');
	date_default_timezone_set('Asia/Jakarta');
	
	require('vendor/autoload.php');
	require('class/Request.php');
	require('class/simplexlsx.class.php');
	
	$request	= new Request;
?>