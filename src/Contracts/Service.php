<?php
namespace Userdesk\Submission\Contracts;

use Userdesk\Submission\Classes\SubmissionToken;

use Userdesk\Submission\Classes\Items\SubmissionVideoItem;
use Userdesk\Submission\Classes\Items\SubmissionImageItem;
use Userdesk\Submission\Classes\Items\SubmissionLinkItem;
use Userdesk\Submission\Classes\Items\SubmissionStatusItem;

use Illuminate\Http\Request;

interface Service{
	/**
	 * Returs oauth provider name.
	 *
	 * @param String $redirUrl
	 * @return \OAuth\Common\Service\AbstractService
	 */
	public function provider(String $redirUrl = '');

	/**
	 * Get Authentication details.
	 *
	 * @param \Illuminate\Http\Request $request;
	 * @param int $state;
	 *
	 * @return \Userdesk\Submission\Classes\SubmissionCredentials;
	 */
	public function completeAuthentication(Request $request, int $state = 0);

	/**
	 * Redirect to Authentication URL.
	 *
	 * @param int $state;
	 *
	 * @return \Illuminate\Http\Response;;
	 */
	public function authenticate(int $state);


	/**
     * Add Submission Token to Service
     *
     * @param Userdesk\Submission\Classes\SubmissionToken $token
     */
    public function addToken(SubmissionToken $token);

    /**
     * Upload Video.
     *
     * @param \Userdesk\Submission\Classes\Items\SubmissionVideoItem $item;
     *
     * @return \Userdesk\Submission\Classes\SubmissionResult;
     */
    public function uploadVideo(SubmissionVideoItem $item);

    /**
     * Upload Image.
     *
     * @param \Userdesk\Submission\Classes\Items\SubmissionImageItem $item;
     *
     * @return \Userdesk\Submission\Classes\SubmissionResult;
     */
    public function uploadImage(SubmissionImageItem $item);

    /**
     * Update Status.
     *
     * @param \Userdesk\Submission\Classes\Items\SubmissionStatusItem $item;
     *
     * @return \Userdesk\Submission\Classes\SubmissionResult;
     */
    public function addStatus(SubmissionStatusItem $item);

    /**
     * Add Link.
     *
     * @param \Userdesk\Submission\Classes\Items\SubmissionLinkItem $item;
     *
     * @return \Userdesk\Submission\Classes\SubmissionResult;
     */
    public function addLink(SubmissionLinkItem $item);
}