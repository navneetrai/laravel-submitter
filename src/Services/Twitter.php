<?php namespace Userdesk\Submission\Services;

use Illuminate\Http\Request;
use Illuminate\Http\Response;

use Userdesk\Submission\Classes\SubmissionCredentials;
use Userdesk\Submission\Classes\SubmissionToken;
use Userdesk\Submission\Classes\SubmissionUser;
use Userdesk\Submission\Classes\SubmissionResult;

use Userdesk\Submission\Classes\Items\SubmissionVideoItem;
use Userdesk\Submission\Classes\Items\SubmissionImageItem;
use Userdesk\Submission\Classes\Items\SubmissionStatusItem;

use Userdesk\Submission\Exceptions\MissingRouteException;
use Userdesk\Submission\Exceptions\InvalidTokenException;
use Userdesk\Submission\Exceptions\InvalidPrivilegeException;
use Userdesk\Submission\Exceptions\InvalidUploadException;

use OAuth\OAuth1\Token\StdOAuth1Token;
use OAuth\Common\Http\Exception\TokenResponseException;

class Twitter extends AbstractService{
	
	/**
	 * Returns oauth provider object.
	 *
	 * @return \OAuth\Common\Service\AbstractService
	 */
	public function provider(String $redirUrl = ''){
		return $this->consumer('Twitter', $this->config, $redirUrl);
	}

	/**
	 * Returns oauth provider object from saved token.
	 *
	 * @return \OAuth\Common\Service\AbstractService
	 */
	public function providerFromToken(){
		if(empty($this->token)){
			throw new MissingTokenException('Token not Found');
		}
		try{
			$tw = $this->provider();
		    $storage = $tw->getStorage();

		    $oToken = new StdOAuth1Token($this->token->getToken());
		    //$token->setAccessToken();
		    $token->setAccessTokenSecret($this->token->getSecret());

		    $storage->storeAccessToken('Twitter', $oToken);

		    return $tw;
		}catch(TokenResponseException $e){
	        throw new InvalidTokenException('Cannot verify saved token. User has either revoked the priveleges or created a new token.');
	    }
		
	}

	/**
     * Upload Video.
     *
     * @param \Userdesk\Submission\Classes\SubmissionVideoItem $item;
     *
     * @return \Userdesk\Submission\Classes\SubmissionResult;
     */
    public function uploadVideo(SubmissionVideoItem $item){
    	$tw = $this->providerFromToken();

        $video = file_get_contents($item->getVideo());
        $media_data = $tw->request('https://upload.twitter.com/1.1/media/upload.json', 'POST', array('command'=>'INIT', 'total_bytes'=>strlen($video), 'media_type'=>$item->getType()));
        $media = json_decode($media_data, true);
        $media_id = $media['media_id'];

        if($media_id){
            $tw->request('https://upload.twitter.com/1.1/media/upload.json', 'POST', array('command'=>'APPEND', 'segment_index'=>0, 'media_id'=>$media_id, 'media_data'=>base64_encode($video)));
            $tw->request('https://upload.twitter.com/1.1/media/upload.json', 'POST', array('command'=>'FINALIZE', 'media_id'=>$media_id));

            $response = $tw->request('statuses/update.json', 'POST', array('status' => $item->getTitle(), 'media_ids'=>$media_id));

            $status = json_decode($response, true);

            if(!empty($status['id']) && !empty($status['user'])){
            	$url = sprintf('https://twitter.com/%s/status/%s', $status['user']['screen_name'], $status['id']);
            	return new SubmissionResult(true, '', $url);
            }

            return new SubmissionResult(false, 'Video Upload Failed');
        }else{
        	throw new InvalidUploadException($e->getMessage());
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
		$tw = $this->providerFromToken();

        $image = file_get_contents($item->getImage());
        $media_data = $tw->request('https://upload.twitter.com/1.1/media/upload.json', 'POST', array('command'=>'INIT', 'total_bytes'=>strlen($image), 'media_type'=>$item->getType()));
        $media = json_decode($media_data, true);
        $media_id = $media['media_id'];

        if($media_id){
            $tw->request('https://upload.twitter.com/1.1/media/upload.json', 'POST', array('command'=>'APPEND', 'segment_index'=>0, 'media_id'=>$media_id, 'media_data'=>base64_encode($image)));
            $tw->request('https://upload.twitter.com/1.1/media/upload.json', 'POST', array('command'=>'FINALIZE', 'media_id'=>$media_id));

            $response = $tw->request('statuses/update.json', 'POST', array('status' => $item->getTitle(), 'media_ids'=>$media_id));

            $status = json_decode($response, true);

            if(!empty($status['id']) && !empty($status['user'])){
            	$url = sprintf('https://twitter.com/%s/status/%s', $status['user']['screen_name'], $status['id']);
            	return new SubmissionResult(true, '', $url);
            }

            return new SubmissionResult(false, 'Image Upload Failed');
        }else{
        	throw new InvalidUploadException($e->getMessage());
        }
	}

	/**
     * Add Status.
     *
     * @param \Userdesk\Submission\Classes\SubmissionStatusItem $item;
     *
     * @return \Userdesk\Submission\Classes\SubmissionResult;
     */
    public function addStatus(SubmissionStatusItem $item) {
    	$tw = $this->providerFromToken();

        $response = $tw->request('statuses/update.json', 'POST', array('status' => $item->getStatus()));

        $status = json_decode($response, true);

        if(!empty($status['id']) && !empty($status['user'])){
        	$url = sprintf('https://twitter.com/%s/status/%s', $status['user']['screen_name'], $status['id']);
        	return new SubmissionResult(true, '', $url);
        }

        return new SubmissionResult(false, 'status Update Failed');
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
		$token  = $request->get('oauth_token');
    	$verify = $request->get('oauth_verifier');

   		$tw = $this->provider();

	    if (!is_null($token) && !is_null($verify)) {
	        $token = $tw->requestAccessToken($token, $verify);	        
	        $result = json_decode($tw->request('account/verify_credentials.json'), true);
	        if(!empty($result)){
		        $link = sprintf('http://www.twitter.com/%s', $result['screen_name']);
	        	$user = new SubmissionUser($result['id'], $link, $result);

		        $token = new SubmissionToken($token->getAccessToken(), $token->getAccessTokenSecret(), '', '');
				
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
	 * @return \Illuminate\Http\Response;
	 */
	public function authenticate(int $state){
		parent::authenticate($state);

		$redirUrl = \URL::route('package.Userdesk.submission.authenticate', ['website'=>'twitter', 'state'=>$state]);
    	
    	$tw = $this->provider($redirUrl);    		

	   	$reqToken = $tw->requestRequestToken();
	    $url = $tw->getAuthorizationUri(['oauth_token' => $reqToken->getRequestToken()]);
	    return redirect((string)$url);     
	}
}