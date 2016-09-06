<?php namespace Userdesk\Submission\Classes;

class SubmissionToken{
	private $token;
	private $secret;
	private $accessToken;
	private $refreshToken;
	
	/**
     * @param  String $token
     * @param  String $secret
     * @param  String|null $accessToken
     * @param  String|null $refreshToken
     *
     */
	public function __construct(String $token = '', String $secret = '', $accessToken = '', $refreshToken = ''){
		$this->token = $token;
		$this->secret = $secret;
		$this->accessToken = $accessToken;
		$this->refreshToken = $refreshToken;
	}
	
	/**
     * @return String
     */
	public function getToken() {
		return $this->token;
	}

	/**
     * @return String
     */
	public function getSecret() {
		return $this->secret;
	}

	/**
     * @return String
     */
	public function getAccessToken() {
		return $this->accessToken;
	}

	/**
     * @return String
     */
	public function getRefreshToken() {
		return $this->refreshToken;
	}

	/**
     * @return array
     */
	public function getTokenArray() {
		return ['token'=> $this->getToken(), 'secret'=> $this->getSecret(), 'access_token'=> $this->getAccessToken(), 'refresh_token'=> $this->getRefreshToken()];
	}

	/**
     * @param array
     */
	public function addTokenArray(array $token) {
		$this->token = array_get($token, 'token');
		$this->secret = array_get($token, 'secret');
		$this->accessToken = array_get($token, 'access_token');
		$this->refreshToken = array_get($token, 'refresh_token');
	}
}