<?php namespace Userdesk\Submission\Classes;

class SubmissionCredentials{
	private $state;
	private $user;
	private $token;

	 /**
     * @param  int $state
     * @param  Userdesk\Submission\Classes\SubmissionUser $user
     * @param  Userdesk\Submission\Classes\SubmissionToken $token
     *
     */
	public function __construct(int $state, SubmissionUser $user, SubmissionToken $token){
		$this->state = $state;
		$this->user = $user;
		$this->token = $token;
	}
	
	/**
     * @return int
     */
	public function getState() {
		return $this->state;
	}

	/**
     * @return Userdesk\Submission\Classes\SubmissionUser
     */
	public function getUser() {
		return $this->user;
	}

	/**
     * @return Userdesk\Submission\Classes\SubmissionToken
     */
	public function getToken() {
		return $this->token;
	}
}