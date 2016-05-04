<?php namespace Userdesk\Submission\Services;

use Illuminate\Http\Request;

use Userdesk\Submission\Classes\SubmissionCredentials;
use Userdesk\Submission\Classes\SubmissionToken;
use Userdesk\Submission\Classes\SubmissionUser;
use Userdesk\Submission\Classes\SubmissionResult;

use Userdesk\Submission\Classes\Items\SubmissionVideoItem;

use Userdesk\Submission\Exceptions\MissingTokenException;
use Userdesk\Submission\Exceptions\InvalidTokenException;
use Userdesk\Submission\Exceptions\InvalidPrivilegeException;
use Userdesk\Submission\Exceptions\InvalidUploadException;

class Youtube extends AbstractService{
	/**
	 * Returns oauth provider object.
	 *
	 * @return \OAuth\Common\Service\AbstractService
	 */
	public function provider(String $redirUrl = ''){
		return $this->consumer('Google', $this->config, $redirUrl);
	}

	/**
	 * Returns oauth provider object from saved token.
	 *
	 * @return \Google_Client
	 */
	public function providerFromToken(){
		if(empty($this->token)){
			throw new MissingTokenException('Token not Found');
		}
		try{
			$google = new \Google_Client ();
		    $google->setClientId (array_get($this->config, 'client_id'));
		    $google->setClientSecret (array_get($this->config, 'client_secret'));
		    $google->setScopes(array_get($this->config, 'scopes'));
		    $google->setAccessType ('offline');
		    $redirUrl = route('package.Userdesk.submission.authenticate', ['website'=>'youtube']);
		    $google->setRedirectUri ($redirUrl );
		    $google->refreshToken($this->token->getRefreshToken());
		  
		    return $google;
		}catch(Exception $e){
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
	    $google = $this->providerFromToken($token);

	    try{    
	        $youtube = new \Google_Service_YouTube($google);
	        $videoPath = $item->getVideo();

	         if(!empty($videoPath)){
	             // Create a snipet with title, description, tags and category id
	            $snippet = new \Google_Service_YouTube_VideoSnippet ();
	            $snippet->setTitle ($item->getTitle());
	            $snippet->setDescription ($item->getDescription());
	            $snippet->setTags (explode(',', $item->getKeywords()));

	            // Numeric video category. See
	            // https://developers.google.com/youtube/v3/docs/videoCategories/list
	            $snippet->setCategoryId ("22");

	            $status = new \Google_Service_YouTube_VideoStatus();
	            $status->privacyStatus = "public";        

	            // Create a YouTube video with snippet and status
	            $video = new \Google_Service_YouTube_Video ();
	            $video->setSnippet ($snippet);
	            $video->setStatus ($status);

	            // Size of each chunk of data in bytes. Setting it higher leads faster upload (less chunks,
	            // for reliable connections). Setting it lower leads better recovery (fine-grained chunks)
	            $chunkSizeBytes = 1 * 1024 * 1024;

	            $google->setDefer(true);

	            $insertRequest = $youtube->videos->insert("status,snippet", $video);

	            // Create a MediaFileUpload with resumable uploads
	            $media = new \Google_Http_MediaFileUpload ($google, $insertRequest,'video/*', null, true, $chunkSizeBytes);
	            $media->setFileSize (filesize($videoPath));

	            // Create a video insert request
	            $uploadStatus = false;

	            // Read file and upload chunk by chunk
	            $handle = fopen ($videoPath, "rb");
	            
	            while (!$uploadStatus && !feof ($handle)) {
	                $chunk = fread ($handle, $chunkSizeBytes);
	                $uploadStatus = $media->nextChunk ($chunk);
	            }
	            
	            fclose ($handle);

	            $google->setDefer(false);
	            
	            if(!empty($uploadStatus) && !empty($uploadStatus->id)){
	            	$url = "http://www.youtube.com/watch?v=".$uploadStatus->id; 
	            	return new SubmissionResult(true, '', $url);
            	}

            	return new SubmissionResult(false, 'Video Upload Failed');
	        }	                  
	    } catch (\Google_ServiceException $e) {
	        throw new InvalidUploadException($e->getMessage());
	    } catch (\Google_Exception $e) {
	        throw new InvalidUploadException($e->getMessage());
	    } catch (Exception $e){
	        throw new InvalidUploadException($e->getMessage());
	    }
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
	    $googleService = $this->provider();
	    $googleService->setAccessType('offline');

	    if (!is_null($code)) {
	    	$state = $request->get('state');
	        $token = $googleService->requestAccessToken($code);
	        $result = json_decode($googleService->request('https://www.googleapis.com/oauth2/v1/userinfo'), true);
	        if(!empty($result)){
	        	$link = sprintf('https://plus.google.com/u/0/%s', $result['id']);
	        	$user = new SubmissionUser($result['email'], $link, $result);

	        	$token = new SubmissionToken('', '', $token->getAccessToken(), $token->getRefreshToken());
	        	
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
		
		$redirUrl = route('package.Userdesk.submission.authenticate', ['website'=>'youtube']);
    	$googleService = $this->provider($redirUrl);
	    $googleService->setAccessType('offline');

	    $url = $googleService->getAuthorizationUri(['state'=>$state]);
	    return redirect((string)$url);        
	}
}