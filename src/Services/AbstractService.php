<?php namespace Userdesk\Submission\Services;

use OAuth\ServiceFactory;
use OAuth\Common\Consumer\Credentials;
use OAuth\Common\Service\ServiceInterface;

use Userdesk\Submission\Classes\SubmissionToken;
use Userdesk\Submission\Classes\SubmissionResult;

use Userdesk\Submission\Classes\Items\SubmissionVideoItem;
use Userdesk\Submission\Classes\Items\SubmissionImageItem;
use Userdesk\Submission\Classes\Items\SubmissionLinkItem;
use Userdesk\Submission\Classes\Items\SubmissionStatusItem;

use Userdesk\Submission\Contracts\Service as SubmitterContract;

use Userdesk\Submission\Exceptions\MissingRouteException;
use Userdesk\Submission\Exceptions\InvalidUploadException;

use Illuminate\Http\Request;

use Config;
use URL;
use Route;

abstract class AbstractService implements SubmitterContract{
    protected $config = [];

	/**
     * @var Userdesk\Submission\Classes\SubmissionToken
     */
    protected $token;

    /**
     * @var ServiceFactory
     */
    private $oauth;

	/**
     * @var ServiceFactory
     */
    private $serviceFactory;

    /**
     * Storege name from config
     *
     * @var string
     */
    private $storageClass = '\\OAuth\\Common\\Storage\\Session';

    /**
     * Constructor
     *
     * @param array $config
     * @param ServiceFactory $serviceFactory - (Dependency injection) If not provided, a ServiceFactory instance will be constructed.
     */
    public function __construct(array $config, ServiceFactory $serviceFactory = null)
    {
        $this->config = $config;

        if (null === $serviceFactory)
        {
            // Create the service factory
            $serviceFactory = new ServiceFactory();
        }
        $this->serviceFactory = $serviceFactory;
    }

    /**
     * Add Submission Token to Service
     *
     * @param Userdesk\Submission\Classes\SubmissionToken $token
     */
    public function addToken(SubmissionToken $token){
        $this->token = $token;
    }

    /**
     * Returs oauth provider name.
     *
     * @param String $redirUrl
     * @return \OAuth\Common\Service\AbstractService
     */
    abstract public function provider(String $redirUrl = '');

    /**
     * Get Authentication details.
     *
     * @param \Illuminate\Http\Request $request;
     * @param int $state;
     *
     * @return \Userdesk\Submission\Classes\SubmissionCredentials;
     */
    abstract public function completeAuthentication(Request $request, int $state = 0);

    /**
     * Upload Video.
     *
     * @param \Userdesk\Submission\Classes\Items\SubmissionVideoItem $item;
     *
     * @return \Userdesk\Submission\Classes\SubmissionResult;
     */
    public function uploadVideo(SubmissionVideoItem $item){
        throw new InvalidUploadException('This website do not support Video  Uploads');
    }

    /**
     * Upload Image.
     *
     * @param \Userdesk\Submission\Classes\Items\SubmissionImageItem $item;
     *
     * @return \Userdesk\Submission\Classes\SubmissionResult;
     */
    public function uploadImage(SubmissionImageItem $item){
        throw new InvalidUploadException('This website do not support Image  Uploads');
    }

    /**
     * Update Status.
     *
     * @param \Userdesk\Submission\Classes\Items\SubmissionStatusItem $item;
     *
     * @return \Userdesk\Submission\Classes\SubmissionResult;
     */
    public function addStatus(SubmissionStatusItem $item){
        throw new InvalidUploadException('This website do not support Status Updates');
    }

    /**
     * Add Link.
     *
     * @param \Userdesk\Submission\Classes\Items\SubmissionLinkItem $item;
     *
     * @return \Userdesk\Submission\Classes\SubmissionResult;
     */
    public function addLink(SubmissionLinkItem $item){
        throw new InvalidUploadException('This website do not support Link Addition');
    }

    /**
     * Create storage instance
     *
     * @param string $storageName
     *
     * @return OAuth\Common\\Storage
     */
    public function createStorageInstance($storageClass)
    {
        $storage = new $storageClass();
        return $storage;
    }

    /**
     * Set the http client object
     *
     * @param string $httpClientName
     *
     * @return void
     */
    public function setHttpClient($httpClientName)
    {
        $httpClientClass = "\\OAuth\\Common\\Http\\Client\\$httpClientName";
        $this->serviceFactory->setHttpClient(new $httpClientClass());
    }

    /**
     * @param  string $service
     * @param  array $credentials
     * @param  string $url
     * @param  array $scope
     *
     * @return \OAuth\Common\Service\AbstractService
     */
    protected function consumer($service, $credentials = array(), $url = null, $scope = null) {

    	if(!empty($this->oauth)){
    		return $this->oauth;
    	}

    	$scope = array_get($credentials, "scope", []);
        // get storage object
        $storage = $this->createStorageInstance($this->storageClass);
        
        // return the service consumer object
        $this->oauth = $this->serviceFactory->createService($service, $this->getCredentials($credentials, $url), $storage, $scope);

        return $this->oauth;
    }

    /**
     * @param  array $credentials
     *
     * @return \OAuth\Common\Consumer\Credentials
     */
    protected function getCredentials($credentials = array(), $url = null){
    	$clientId = array_get($credentials, "client_id");
    	$clientSecret = array_get($credentials, "client_secret");

    	return new Credentials(
            $clientId,
            $clientSecret,
            $url ? : URL::current()
        );
    }

    /**
     * Redirect to Authentication URL.
     *
     * @param int $state;
     *
     * @return \Illuminate\Http\Response;
     */
    public function authenticate(int $state){
        if(!Route::has('package.Userdesk.submission.authenticate')) {
            throw new MissingRouteException();
        }     
    }
}