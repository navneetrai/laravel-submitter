<?php namespace Userdesk\Submission\Classes;

class SubmissionUser{
	private $id;
	private $link;
	private $profile;
	
	/**
     * @param  String $id
     * @param  String $link
     * @param  array $profile

     *
     */
	public function __construct(String $id, String $link, array $profile){
		$this->id = $id;
		$this->link = $link;
		$this->profile = $profile;
	}
	
	/**
     * @return String
     */
	public function getId() {
		return $this->id;
	}

	/**
     * @return String
     */
	public function getLink() {
		return $this->link;
	}

	/**
     * @return array
     */
	public function getProfile() {
		$this->addProfileLink();
		return $this->profile;
	}

	private function addProfileLink(){
		if(empty($this->profile['link'])){
			$this->profile['link'] = $this->link;
		}
	}
}