<?php namespace Userdesk\Submission\Services;

use Illuminate\Http\Request;

use Userdesk\Submission\Classes\SubmissionCredentials;
use Userdesk\Submission\Classes\SubmissionToken;
use Userdesk\Submission\Classes\SubmissionUser;
use Userdesk\Submission\Classes\SubmissionResult;

use Userdesk\Submission\Classes\Items\SubmissionVideoItem;
use Userdesk\Submission\Classes\Items\SubmissionImageItem;
use Userdesk\Submission\Classes\Items\SubmissionLinkItem;
use Userdesk\Submission\Classes\Items\SubmissionStatusItem;

use Userdesk\Submission\Exceptions\MissingTokenException;
use Userdesk\Submission\Exceptions\InvalidTokenException;
use Userdesk\Submission\Exceptions\InvalidPrivilegeException;
use Userdesk\Submission\Exceptions\InvalidUploadException;

use Facebook\Exceptions\FacebookResponseException;
use Facebook\Exceptions\FacebookSDKException;

class Facebook extends AbstractService{

	/**
	 * Returns oauth provider object.
	 *
	 * @return \OAuth\Common\Service\AbstractService
	 */
	public function provider(String $redirUrl = ''){
		return $this->consumer('Facebook', $this->config, $redirUrl);
	}

	/**
	 * Returns oauth provider object from saved token.
	 *
	 * @return \Facebook\Facebook
	 */
	public function providerFromToken(){
		if(empty($this->token)){
			throw new MissingTokenException('Token not Found');
		}
		try{
			$config = array(
		        'app_id'     => array_get($this->config, 'client_id'),
		        'app_secret' => array_get($this->config, 'client_secret'),
		        'default_graph_version' => 'v2.6',
		        'default_access_token'  => $this->token->getAccessToken(),
		    );
		  
		    return new \Facebook\Facebook($config); 
		}catch(Exception $e){
	        throw new InvalidTokenException('Cannot verify saved token. User has either revoked the priveleges or created a new token.');
	    }
	}

	/**
     * Upload Image.
     *
     * @param \Userdesk\Submission\Classes\SubmissionImageItem $item;
     *
     * @return \Userdesk\Submission\Classes\SubmissionResult;
     */
    public function uploadImage(SubmissionImageItem $item){	  
	    $facebook = $this->providerFromToken();  

	    $params = array(
	        'source'       => $facebook->fileToUpload($item->getImage()),
	        'title'        => $item->getTitle(),
	        'message'      => $item->getDescription()
	    );
	            
	    try {
	    	$userId = $facebook->getUser();
	        $response = $facebook->post('/'.$userId.'/videos', $params);      
	    } catch(FacebookResponseException $e) {
	    	throw new InvalidUploadException($e->getMessage());	      
	    } catch(FacebookSDKException $e) {
	      	throw new InvalidUploadException($e->getMessage());
	    } catch(Exception$e) {
	        throw new InvalidUploadException($e->getMessage());
	    }

	    $graphNode = $response->getGraphNode();
	    
	    if(!empty($graphNode['id'])){
        	$url = "https://www.facebook.com/photo.php?v=".$graphNode['id']; 
        	return new SubmissionResult(true, '', $url);
    	}

    	return new SubmissionResult(false, 'Image Upload Failed');
	}


	/**
     * Upload Video.
     *
     * @param \Userdesk\Submission\Classes\SubmissionVideoItem $item;
     *
     * @return \Userdesk\Submission\Classes\SubmissionResult;
     */
    public function uploadVideo(SubmissionVideoItem $item){	  
	    $facebook = $this->providerFromToken();  

	    $params = array(
	        'content_category' => 'OTHER',
	        //'file_url'     => $project->video,
	        'source'       => $facebook->videoToUpload($item->getVideo()),
	        'title'        => $item->getTitle(),
	        'thumb'        => $facebook->fileToUpload($item->getThumb())
	    );
	            
	    try {
	    	$userId = $facebook->getUser();
	        $response = $facebook->post('/'.$userId.'/videos', $params);    
	    } catch(FacebookResponseException $e) {
	    	throw new InvalidUploadException($e->getMessage());	      
	    } catch(FacebookSDKException $e) {
	      	throw new InvalidUploadException($e->getMessage());
	    } catch(Exception$e) {
	        throw new InvalidUploadException($e->getMessage());
	    }

	    $graphNode = $response->getGraphNode();
	    
	    if(!empty($graphNode['id'])){
        	$url = "https://www.facebook.com/photo.php?v=".$graphNode['id']; 
        	return new SubmissionResult(true, '', $url);
    	}

    	return new SubmissionResult(false, 'Video Upload Failed');
	}

	/**
     * Add Link.
     *
     * @param \Userdesk\Submission\Classes\SubmissionLinkItem $item;
     *
     * @return \Userdesk\Submission\Classes\SubmissionResult;
     */
    public function addLink(SubmissionLinkItem $item){	  
	    $facebook = $this->providerFromToken();  

	    $params = array(
	        'link'       => $item->getLink(),
	        'tags'        => $item->getKeywords(),
	        'message'      => $item->getDescription()
	    );
	            
	    try {
	    	$userId = $facebook->getUser();
	        $response = $facebook->post('/'.$userId.'/feed', $params);   
	    } catch(FacebookResponseException $e) {
	    	throw new InvalidUploadException($e->getMessage());	      
	    } catch(FacebookSDKException $e) {
	      	throw new InvalidUploadException($e->getMessage());
	    } catch(Exception$e) {
	        throw new InvalidUploadException($e->getMessage());
	    }

	    $graphNode = $response->getGraphNode();
	    
	    if(!empty($graphNode['id'])){
        	$url = "https://www.facebook.com/photo.php?v=".$graphNode['id']; 
        	return new SubmissionResult(true, '', $url);
    	}

    	return new SubmissionResult(false, 'Link Update Failed');
	}

	/**
     * Add Status.
     *
     * @param \Userdesk\Submission\Classes\SubmissionStatusItem $item;
     *
     * @return \Userdesk\Submission\Classes\SubmissionResult;
     */
    public function addStatus(SubmissionStatusItem $item){	  
	    $facebook = $this->providerFromToken();  

	    $params = array(
	        'tags'        => $item->getKeywords(),
	        'message'      => $item->getStatus()
	    );
	            
	    try {
	    	$userId = $facebook->getUser();
	        $response = $facebook->post('/'.$userId.'/feed', $params);    
	    } catch(FacebookResponseException $e) {
	    	throw new InvalidUploadException($e->getMessage());	      
	    } catch(FacebookSDKException $e) {
	      	throw new InvalidUploadException($e->getMessage());
	    } catch(Exception$e) {
	        throw new InvalidUploadException($e->getMessage());
	    }

	    $graphNode = $response->getGraphNode();
	    
	    if(!empty($graphNode['id'])){
        	$url = "https://www.facebook.com/photo.php?v=".$graphNode['id']; 
        	return new SubmissionResult(true, '', $url);
    	}

    	return new SubmissionResult(false, 'Status Update Failed');
	}

	/**
	 * Get Authentication details.
	 *
	 * @param \Illuminate\Http\Request $request;
	 * @param int $state;
	 *
	 * @return \Userdesk\Submission\Classes\SubmissionCredentials;
	 */
	public function completeAuthentication(Request $request, int $state = 0){
		$code = $request->get('code');
    	$fb = $this->provider();

	    if (!is_null($code)) {
	    	$state = $request->get('state');
	        $token = $fb->requestAccessToken($code);
	        $result = json_decode($fb->request('/me'), true);
	        if(!empty($result)){
		        $link = sprintf('https://www.facebook.com/%s', $result['id']);
	        	$user = new SubmissionUser($result['id'], $link, $result);

		        $token = new SubmissionToken('', '', $token->getAccessToken(), '');

	        	return new SubmissionCredentials($state, $user, $token);
	        }
	    	throw new InvalidPrivilegeException('Cannot verify user information. please check that user has given proper priveleges.');
	    }else{
	    	throw new InvalidTokenException('Cannot verify token. Please check config');
	    }
	}

	/**
	 * Redirect to Authentication URL.
	 *
	 * @param int $state;
	 *
	 * @return \Illuminate\Http\Response;;
	 */
	public function authenticate(int $state){
		parent::authenticate($state);
		
		$redirUrl = route('package.Userdesk.submission.authenticate', ['website'=>'facebook']);
    	$fb = $this->provider($redirUrl);
	   	$url = $fb->getAuthorizationUri(['state'=>$state]);
	   	return redirect((string)$url); 	        
	}
}