<?php namespace toufee\Tests\Submission;

use Illuminate\Foundation\Testing\WithoutMiddleware;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Foundation\Testing\DatabaseTransactions;

use toufee\Submission\Submitter;
use toufee\Submission\SubmissionFactory;

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
        return array('toufee\Submission\SubmissionServiceProvider');
    }

    protected function getPackageAliases($app)
    {
        return array(
            'Submission' => 'toufee\Submission\Facades\Submission'
        );
    }

    /**
     * @covers toufee\Submission\Submitter::submitter
     */
    public function testCreateSubmitter()
    {
        Config::set('submission.services.twitter.client_id', 'test');
        Config::set('submission.services.twitter.client_secret', 'best');

        $submissionFactory = Mockery::mock('toufee\Submission\SubmissionFactory[createService]');
        $submissionFactory->shouldReceive('createService')->passthru();

        $submission = new Submitter($submissionFactory);
        $submitter = $submission->submitter('twitter');
        $this->assertInstanceOf('toufee\Submission\Services\Twitter', $submitter);
    }
}
