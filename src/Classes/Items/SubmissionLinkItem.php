<?php namespace Userdesk\Submission\Classes\Items;

class SubmissionLinkItem{
	private $title;
	private $description;
	private $keywords;
	private $imagePath;
	private $videoPath;
	
	/**
     * @param  String $title
     * @param  String $description
     * @param  String $keywords
     * @param  String $link
     *
     */
	public function __construct(String $title, String $description, String $link, String $keywords = ''){
		$this->title = $title;
		$this->description = $description;
		$this->link = $link;
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
	public function getDescription() {
		return $this->description;
	}

	/**
     * @return String
     */
	public function getKeywords() {
		return $this->keywords;
	}

	/**
     * @return String
     */
	public function getLink() {
		return $this->link;
	}
}