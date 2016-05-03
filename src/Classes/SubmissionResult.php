<?php namespace Userdesk\Submission\Classes;

class SubmissionResult{
	boolean $pass;
	private $url;
	private $message;
	
	/**
     * @param  boolean $pass
     * @param  String $url
     * @param  String $message
     *
     */
	public function __construct(boolean $pass = false, String $message = '', String $url = ''){
		$this->pass = $pass;
		$this->url = $url;
		$this->message = $message;
	}
	
	/**
     * @return boolean
     */
	public function getStatus() {
		return $this->pass?'Pass':'Fail';
	}

	/**
     * @return String
     */
	public function getUrl() {
		return $this->url;
	}

	/**
     * @return String
     */
	public function getMessage() {
		return $this->message;
	}
}