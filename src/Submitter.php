<?php namespace Userdesk\Submission;

class Submitter{
	/**
     * @var SubmissionFactory
     */
	private $submissionFactory;

	/**
     * Constructor
     *
     * @param SubmissionFactory $submissionFactory - (Dependency injection) If not provided, a SubmissionFactory instance will be constructed.
     */
    public function __construct(SubmissionFactory $submissionFactory = null)  {
        if (null === $submissionFactory){
            // Create the service factory
            $submissionFactory = new SubmissionFactory();
        }
        $this->submissionFactory = $submissionFactory;
    }

	/**
     * @param  string $service
     *
     * @return \Userdesk\Submission\Contracts\Service
     */

	public function submitter($service){
		return $this->submissionFactory->createService($service);
	}
}