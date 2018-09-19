<?php
	Class Request
	{
		protected $tokenInfo = [];
		
		function __construct()
		{
			// $this->uri		= 'https://api.turbo.co.id/';
			$this->uri		= 'localhost:8888/';
			$this->client	= new GuzzleHttp\Client();
			$this->query	= [ 'secret_key' => '4510deb00627a4dabb800d0f485af867f4e836e7' ];
			
			$this->cbb1		= 'http://www.chinabuybuy.com/api/rest_admin/';
			$this->cbb2		= 'http://www.chinabuybuy.com/index.php?route=rest/';
			$this->headers	= [ 'Accept' => 'application/json', 'Content-Type' => 'application/json' ];
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
		
		public function cbbAuthorize()
		{
			// Use Basic Token to authorize and get Authorization Bearer
			$response	= $this->client->post($this->cbb1.'oauth2/token/client_credentials', [
				'headers' => [
					'Authorization' => 'Basic ' . $this->cbbToken()
				]
			]);
			
			// Save the auth_bearer to reuse it in every requests
			$this->saveCbbToken(json_decode((string) $response->getBody(), true));
			
			// Logging in using specific user
			$response	= $this->client->post($this->cbb1.'login', [
				'headers'	=> $this->authHeaders(),
				'json'		=> [
					'username' => 'restadmin',
					'password' => '123456'
				]
			]);
			
			return json_decode((string) $response->getBody(), true);
		}
		
		protected function cbbToken()
		{
			return base64_encode('matahari:m4t4h4r1899');
		}
		
		protected function saveCbbToken($response)
		{
			$this->tokenInfo = $response;
		}
		
		protected function authHeaders()
		{
			return array_merge( $this->headers, [
				'Authorization'	=> 'Bearer ' . $this->tokenInfo['access_token']
			]);
		}
		
		public function product()
		{
			$response	= $this->client->get($this->cbb1.'products/94761', [
				'headers'	=> $this->authHeaders()
			]);
			
			$data = str_replace([ "\n", "\r" ], [ ' ', ' ' ], $response->getBody());
			
			return json_decode((string) $data, true);
		}
	}
?>