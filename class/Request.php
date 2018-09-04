<?php
Class Request {
	function __construct()
	{
		// $this->uri		= 'https://api.turbo.co.id/';
		$this->uri		= 'localhost:8888/';
		$this->query	= [ 'secret_key' => '4510deb00627a4dabb800d0f485af867f4e836e7' ];
		$this->client	= new GuzzleHttp\Client();
	}
	
	public function get( $method, $query = [])
	{
		$collection = $this->query;
		
		if ( !empty($query) )
		{
			$collection = array_merge($collection, $query);
		}
		
		$response = $this->client->get( $this->uri . $method, [ 'query' => $collection ]);
		return $response->getBody();
	}
	
	public function post( $method, $params = [])
	{
		$response = $this->client->post( $this->uri . $method . '?' . http_build_query($this->query), [
			'headers'		=> [
				'Accept'		=> 'application/json',
				'Content-Type'	=> 'application/json'
			],
			'json' => $params
		]);
		// return $response->getBody();
	}
}
?>