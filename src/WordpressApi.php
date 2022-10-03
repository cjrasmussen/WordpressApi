<?php
namespace cjrasmussen\WordpressApi;

use RuntimeException;

class WordpressApi
{
	private const AUTH_TYPE_BASIC = 1;

	private int $authType;
	private string $basicAuthToken;
	private string $apiUrl;

	public function __construct(string $wordpressUrl)
	{
		$this->apiUrl = trim($wordpressUrl, ' /') . '/wp-json/wp/';
	}

	/**
	 * Set the auth token if using basic auth
	 *
	 * @param string $token
	 * @return void
	 */
	public function setAuthBasicToken(string $token): void
	{
		if ($token === '') {
			throw new RuntimeException('Invalid token provided.');
		}

		$this->basicAuthToken = $token;
		$this->authType = self::AUTH_TYPE_BASIC;
	}

	/**
	 * Set the username and password if using basic auth
	 *
	 * @param string $username
	 * @param string $password
	 * @return void
	 */
	public function setAuthUserPass(string $username, string $password): void
	{
		if ($username === '') {
			throw new RuntimeException('Invalid username provided.');
		}

		if ($password === '') {
			throw new RuntimeException('Invalid password provided.');
		}

		$this->basicAuthToken = base64_encode($username . ':' . $password);
		$this->authType = self::AUTH_TYPE_BASIC;
	}

	/**
	 * Make a request to the WordPress API
	 *
	 * @param string $type
	 * @param string $request
	 * @param array $args
	 * @return mixed
	 * @throws RuntimeException
	 * @throws \JsonException
	 */
	public function request(string $type, string $request, array $args = [])
	{
		if (!$this->authType) {
			throw new RuntimeException('Auth type not set, request could not be sent.');
		}

		if (!is_array($args)) {
			$args = [$args];
		}

		$url = $this->apiUrl . $request;

		if (($type === 'GET') && (count($args))) {
			$url .= '?' . http_build_query($args);
		}

		$c = curl_init();
		curl_setopt($c, CURLOPT_HEADER, 0);
		curl_setopt($c, CURLOPT_VERBOSE, 0);
		curl_setopt($c, CURLOPT_RETURNTRANSFER, 1);
		curl_setopt($c, CURLOPT_SSL_VERIFYPEER, 1);

		if ($this->authType === self::AUTH_TYPE_BASIC) {
			curl_setopt($c, CURLOPT_HTTPHEADER, [
				'Authorization: Basic ' . $this->basicAuthToken,
			]);
		}

		curl_setopt($c, CURLOPT_URL, $url);

		if (($type !== 'GET') && (count($args))) {
			curl_setopt($c, CURLOPT_POSTFIELDS, http_build_query($args));
		}

		switch ($type) {
			case 'POST':
				curl_setopt($c, CURLOPT_POST, 1);
				break;
			case 'GET':
				curl_setopt($c, CURLOPT_HTTPGET, 1);
				break;
			default:
				curl_setopt($c, CURLOPT_CUSTOMREQUEST, $type);
		}

		$response = curl_exec($c);
		curl_close($c);

		// DECODE THE RESPONSE INTO A GENERIC OBJECT
		return json_decode($response, true, 512, JSON_THROW_ON_ERROR);
	}
}
