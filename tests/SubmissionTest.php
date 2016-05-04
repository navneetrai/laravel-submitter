<?php namespace Userdesk\Tests\Submission;

use Illuminate\Foundation\Testing\WithoutMiddleware;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Foundation\Testing\DatabaseTransactions;

use Userdesk\Submission\Submitter;
use Userdesk\Submission\SubmissionFactory;

use Config;
use Mockery;

class SubmitterTest extends \Orchestra\Testbench\TestCase
{

    public function tearDown()
    {
        Mockery::close();
        parent::tearDown();
    }

    protected function getPackageProviders($app)
    {
        return array('Userdesk\Submission\SubmissionServiceProvider');
    }

    protected function getPackageAliases($app)
    {
        return array(
            'Submission' => 'Userdesk\Submission\Facades\Submission'
        );
    }

    /**
     * @covers Userdesk\Submission\Submitter::submitter
     */
    public function testCreateSubmitter()
    {
        Config::set('submission.services.twitter.client_id', 'test');
        Config::set('submission.services.twitter.client_secret', 'best');

        $submissionFactory = Mockery::mock('Userdesk\Submission\SubmissionFactory[createService]');
        $submissionFactory->shouldReceive('createService')->passthru();

        $submission = new Submitter($submissionFactory);
        $submitter = $submission->submitter('twitter');
        $this->assertInstanceOf('Userdesk\Submission\Services\Twitter', $submitter);
    }
}
