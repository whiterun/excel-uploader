<?php
	require('start.php');
	
	$params = $_GET;
	
	echo $request->get('all_produk', $params);
?>