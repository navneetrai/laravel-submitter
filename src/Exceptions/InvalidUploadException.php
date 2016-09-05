<?php namespace Userdesk\Submission\Exceptions;

use Exception;

/**
 * Generic library-level exception.
 */
class InvalidUploadException extends Exception{
	public $message;

	/**
     * Create a new exception instance.
     *
     * @param  string  $data
     * @param  array  $data
     * @return void
     */
	public function __construct(string $message){
		$this->message = $message;
		parent::__construct($message);
	}
 	
 	/**
     * Get the underlying response instance.
     *
     * @return string
     */
	public function getMessage(){
		return $this->message;
	}
}