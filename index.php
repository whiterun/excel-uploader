<?php
	set_time_limit (600);
	header('Content-Type: application/json');
	date_default_timezone_set('Asia/Jakarta');
	
	require('vendor/autoload.php');
	require('class/Request.php');
	
	$data		= [];
	$request	= new Request;
	$reader		= new SpreadsheetReader('yolo1.xlsx');
	
	foreach ( $reader as $index => $row)
	{
		if ( $index >= 1 )
		{
			if ( $row[12] != '#N/A' )
			{
				$stripped	= [];
				
				if ( !empty($row[7]) )
				{
					$strip_desc	= preg_split("/[\n;]+/", $row[7]);
					
					foreach ( $strip_desc as $strip )
					{
						if ( !empty($strip) )
						{
							$fetch = explode(':', $strip);
							
							if ( count($fetch) > 1 )
							{
								if ( !empty($fetch[1]) )
								{
									$stripped[] = [
										'label'		=> $fetch[0] ? trim($fetch[0]) : 'Other',
										'deskripsi'	=> trim($fetch[1])
									];
								}
							}
							else
							{
								if ( !empty(preg_replace( "/\r|\n/", "", $fetch[0])) )
								{
									$stripped[] = [
										'label'		=> 'Other',
										'deskripsi'	=> trim($fetch[0])
									];
								}
							}
						}
					}
				}
				
				$images		= [];
				$strip_img	= preg_split("/[|]+/", $row[11]);
				
				foreach ( $strip_img as $image )
				{
					$images[] = [
						'deskripsi'     => $row[6],
						'image_50x50'   => $image,
						'image_100x100' => $image,
						'image_300x300' => $image
					];
				}
				
				$data = [
				// $data[] = [
					'informasi'		=> [
						'unspsc'					=> $row[1],
						'nama_produk'				=> $row[4],
						'no_produk_penyedia'		=> $row[10],
						'id_manufaktur'				=> $row[3],
						'id_kategori_produk_lkpp'	=> $row[2],
						'id_unit_pengukuran_lkpp'	=> 3,
						'deskripsi_singkat'			=> $row[6],
						'deskripsi_lengkap'			=> $row[7],
						'kuantitas_stok'			=> 1,
						'produk_aktif'				=> 1,
						'apakah_produk_lokal'		=> 1,
						'berlaku_sampai'			=> date('Y-m-d', strtotime('+1 year')),
						'url_produk'				=> $row[12],
						'image_50x50'				=> $strip_img[0],
						'image_100x100'				=> $strip_img[0],
						'image_300x300'				=> $strip_img[0],
						'image_800x800'				=> $strip_img[0],
					],
					'spesifikasi'	=> $stripped,
					'harga'			=> [
						'harga_retail'		=> $row[8],
						'harga_pemerintah'	=> $row[9],
						'ongkos_kirim'		=> 0,
						'kurs_id'			=> 1
					],
					'image'		=> $images,
					'lampiran'	=> []
				];
				
				$request->post('produk', $data);
				
				if ($index > 0 && $index % 20 == 0) {
					sleep(2);
				}
			}
		}
	}
	
	// echo json_encode($data);
	echo $request->get('all_produk');
?>