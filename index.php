<?php
	require('start.php');
	
	if ( $excel = SimpleXLSX::parse('yolo.xlsx') )
	{
		$sliced	= array_slice($excel->rows(), 1);
		
		foreach ( array_chunk($sliced, 20) as $index => $slices )
		{
			foreach ( $slices as $row )
			{
				if ( $row[11] != '#N/A' )
				{
					$data = $stripped = [];
					
					if ( !empty(trim($row[6])) )
					{
						$strip_desc	= preg_split("/[\n;]+/", $row[6]);
						
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
											'label'		=> 'Spec',
											'deskripsi'	=> trim($fetch[0])
										];
									}
								}
							}
						}
					}
					
					$images		= [];
					$strip_img	= preg_split("/[|]+/", $row[10]);
					
					foreach ( $strip_img as $image )
					{
						$images[] = [
							'deskripsi'     => $row[4],
							'image_50x50'   => $image,
							'image_100x100' => $image,
							'image_300x300' => $image
						];
					}
					
					// $data = [
					$data[] = [
						'informasi'		=> [
							'unspsc'					=> $row[1],
							'nama_produk'				=> $row[4],
							'no_produk_penyedia'		=> $row[9],
							'id_manufaktur'				=> $row[3],
							'id_kategori_produk_lkpp'	=> $row[2],
							'id_unit_pengukuran_lkpp'	=> 3,
							'deskripsi_singkat'			=> $row[4],
							'deskripsi_lengkap'			=> $row[6],
							'kuantitas_stok'			=> 1,
							'produk_aktif'				=> 1,
							'apakah_produk_lokal'		=> 1,
							'berlaku_sampai'			=> date('Y-m-d', strtotime('+1 year')),
							'url_produk'				=> $row[11],
							'image_50x50'				=> $strip_img[0],
							'image_100x100'				=> $strip_img[0],
							'image_300x300'				=> $strip_img[0],
							'image_800x800'				=> $strip_img[0],
							// 'check_updated'				=> $row[12],
						],
						'spesifikasi'	=> $stripped,
						'harga'			=> [
							'harga_retail'		=> $row[7],
							'harga_pemerintah'	=> $row[8],
							'ongkos_kirim'		=> 0,
							'kurs_id'			=> 1
						],
						'image'		=> $images,
						'lampiran'	=> []
					];
					
					$request->post('bulk_produk', [ 'products' => $data ]);
					
					// if ($index > 0 && $index % 20 == 0) {
						// sleep(2);
					// }
				}
			}
		}
	}
	else
	{
		echo SimpleXLSX::parse_error();
	}
	
	// echo json_encode($data);
	echo $request->get('all_produk');
?>