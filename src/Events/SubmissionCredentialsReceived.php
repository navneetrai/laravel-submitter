<?php

namespace Userdesk\Submission\Events;

use Userdesk\Submission\Classes\SubmissionCredentials;

use Userdesk\Events\Event;
use Illuminate\Queue\SerializesModels;

class SubmissionCredentialsReceived extends Event
{
    use SerializesModels;

    public $credentials;

    /**
     * Create a new event instance.
     *
     * @param  \Userdesk\Submission\Classes\SubmissionCredentials  $credentials
     * @return void
     */
    public function __construct(SubmissionCredentials $credentials){
        $this->credentials = $credentials;
    }
}
