<?php

$connect = @mysqli_connect('localhost', 'root', '', 'test');

if ( !$connect ) {
	echo 'Error: ' . mysqli_connect_error();
	exit();
}

$query = mysqli_query($connect, '
	select meta_value from wpbb_postmeta where meta_key = "_wp_attached_file" and meta_value like "%2016/02%"
');

$i = 0;
while ($row = mysqli_fetch_array($query))
{
	$path = 'wp-content/uploads/' . $row['meta_value'];
	
	if ( file_exists($path) )
	{
		$split = explode('/', $row['meta_value']);
		$new_path = 'yolo/'.$split[0].'-'.$split[1];
		
		if ( !file_exists($new_path) ) mkdir($new_path);
		
		copy($path, $new_path.'/'.$split[2]);
	}
	
	echo $row['meta_value'].'<br>';
	
	$i++;
}

echo $i;

// Close connection
mysqli_close ($connect);