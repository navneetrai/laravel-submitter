<?php namespace Userdesk\Submission\Mocks;

use Illuminate\Http\Request;

use Userdesk\Submission\Services\AbstractService;

use Userdesk\Submission\Classes\SubmissionCredentials;
use Userdesk\Submission\Classes\SubmissionToken;
use Userdesk\Submission\Classes\SubmissionUser;

class MockService extends AbstractService{
	
	/**
     * Returs oauth provider object.
     *
     * @param String $redirUrl
     * @return \OAuth\Common\Service\AbstractService
     */
    public function provider(String $redirUrl = ''){
    	return $this->consumer('Mock', $this->config, $redirUrl);
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
    	$user = new SubmissionUser('testUser', 'http://example.com', array());

        $token = new SubmissionToken('test', 'best', '', '');
		
		return new SubmissionCredentials($state, $user, $token);
    }

    /**
     * Redirect to Authentication URL.
     *
     * @param int $state;
     *
     * @return \Illuminate\Http\Response;;
     */
    public function authenticate(int $state){
    	$redirUrl = route('package.Userdesk.submission.authenticate', ['website'=>'mock', 'state'=>$state]);
    	return redirect($redirUrl);
    }
}