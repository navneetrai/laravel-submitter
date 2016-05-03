<?php namespace Userdesk\Submission\Classes\Items;

class SubmissionStatusItem{
	private $title;
	private $status;
	private $keywords;
	
	/**
     * @param  String $title
     * @param  String $status
     * @param  String $keywords
     *
     */
	public function __construct(String $title, String $status, String $keywords = ''){
		$this->title = $title;
		$this->status = $status;
		$this->keywords = $keywords;
	}
	
	/**
     * @return String
     */
	public function getTitle() {
		return $this->title;
	}

	/**
     * @return String
     */
	public function getStatus() {
		return $this->status;
	}

	/**
     * @return String
     */
	public function getKeywords() {
		return $this->keywords;
	}
}