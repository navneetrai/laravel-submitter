<?php namespace Userdesk\Submission\Classes\Items;

class SubmissionImageItem{
	private $title;
	private $description;
	private $keywords;
	private $imagePath;
	
	/**
     * @param  String $title
     * @param  String $description
     * @param  String $keywords
     * @param  String $imagePath
     *
     */
	public function __construct(String $title, String $description, String $imagePath, String $keywords = ''){
		$this->title = $title;
		$this->description = $description;
		$this->imagePath = $imagePath;
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
	public function getImage() {
		return $this->imagePath;
	}
	
	/**
     * @return String
     */
	public function getType(){
		$pathinfo = pathinfo($this->imagePath);
		$extension = strtolower($pathinfo['extension']);

		if(($extension == 'jpg')||($extension == 'jpeg')){
			return 'image/jpeg';
		}else if($extension == 'png'){
			return 'image/png';
		}else if($extension == 'gif'){
			return 'image/gif';
		}
	}
}