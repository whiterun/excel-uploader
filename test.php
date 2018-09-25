<?php
	require('start.php');
	
	$request->cbbAuthorize();
	
	$product = $request->product()['data'];
	
	$parsed = [];
	foreach ( $product['options'] as $key )
	{
		$option = [];
		foreach ( $key['option_value'] as $value )
		{
			$sku		= $key['option_id'].$value['option_value_id'];
			$option[]	= array_merge( $value, [ 'key' => ucfirst($key['name']), 'sku' => $sku ]);
		}
		
		$parsed[] = $option;
	}
	
	function combinations($product_id, $arrays, $i = 0)
	{
		if ( !isset($arrays[$i]) ) return array();
		
		if ( count($arrays) == 1 )
		{
			foreach ( $arrays[$i] as $v )
			{
				$result[] = [
					'my_product_id'	=> $product_id,
					'sku'			=> $product_id.$v['sku'],
					'image_url'		=> $v['new_prop'],
					'attributes'	=> json_encode([
						[ 'name' => $v['key'],'option' => $v['name'] ]
					], 1)
				];
			}
			
			return $result;
		}
		
		if ( $i == count($arrays) - 1 ) return $arrays[$i];
		
		// get combinations from subsequent arrays
		$tmp = combinations($product_id, $arrays, $i + 1);

		$result = [];
		
		// concat each array from tmp with each element from $arrays[$i]
		foreach ( $arrays[$i] as $v )
		{
			foreach ( $tmp as $t )
			{
				$result[] = is_array($t) ? 
					[
						'my_product_id'	=> $product_id,
						'sku'			=> $product_id.$v['sku'].$t['sku'],
						'image_url'		=> $v['new_prop'],
						'attributes'	=> json_encode([
							[ 'name' => $v['key'],'option' => $v['name'] ],
							[ 'name' => $t['key'],'option' => $t['name'] ]
						], 1)
					] :
					array($v, $t);
			}
		}

		return $result;
	}

	print_r(
		combinations($product['id'], $parsed)
	);
?>