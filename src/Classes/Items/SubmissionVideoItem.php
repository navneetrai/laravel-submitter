<?php namespace Userdesk\Submission\Classes\Items;

class SubmissionVideoItem{
	private $title;
	private $description;
	private $keywords;
	private $thumbPath;
	private $videoPath;
	
	/**
     * @param  String $title
     * @param  String $description
     * @param  String $keywords
     * @param  String $thumbPath
     * @param  String $videoPath
     *
     */
	public function __construct(String $title, String $description, String $thumbPath, String $videoPath, String $keywords){
		$this->title = $title;
		$this->description = $description;
		$this->thumbPath = $thumbPath;
		$this->videoPath = $videoPath;
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
	public function getThumb() {
		return $this->thumbPath;
	}

	/**
     * @return String
     */
	public function getVideo() {
		return $this->videoPath;
	}

	/**
     * @return String
     */
	public function getType(){
		$pathinfo = pathinfo($this->imagePath);
		$extension = strtolower($pathinfo['extension']);

		if($extension == 'mp4'){
			return 'video/mp4';
		}else if($extension == 'avi'){
			return 'video/avi';
		}else if($extension == 'mov'){
			return 'video/quicktime';
		}
	}
}