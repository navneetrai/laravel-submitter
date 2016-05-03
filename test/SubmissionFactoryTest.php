<?php namespace toufee\Tests\Submission;

use Illuminate\Foundation\Testing\WithoutMiddleware;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Foundation\Testing\DatabaseTransactions;

use toufee\Submission\SubmissionFactory;

use Config;

class SubmissionFactoryTest extends \Orchestra\Testbench\TestCase
{   

    /**
     * @covers toufee\Submission\SubmissionFactory::createService
     */
    public function testCreateServiceThrowsExceptionIfNoConfig(){
    	$this->setExpectedException('\\toufee\Submission\Exceptions\SubmissionException');
		$factory = new SubmissionFactory();
        $service = $factory->createService('twitter');
    }

    /**
     * @covers toufee\Submission\SubmissionFactory::createService
     * @covers toufee\Submission\SubmissionFactory::fullyQualifiedServiceName
     * @covers toufee\Submission\SubmissionFactory::buildService
     */
    public function testCreateServiceNonExistentService() {
    	Config::set('submission.services.foo.email', 'test@best.com');

        $factory = new SubmissionFactory();
        $service = $factory->createService('foo');

        $this->assertNull($service);
    }

    /**
     * @covers toufee\Submission\SubmissionFactory::createService
     * @covers toufee\Submission\SubmissionFactory::fullyQualifiedServiceName
     * @covers toufee\Submission\SubmissionFactory::buildService
     */
    public function testCreateServicePreLoaded(){
    	Config::set('submission.services.twitter.client_id', 'test');
        Config::set('submission.services.twitter.client_secret', 'best');

		$factory = new SubmissionFactory();
        $service = $factory->createService('twitter');

        $this->assertInstanceOf('toufee\\Submission\\Services\\Twitter', $service);
    }
	
	/**
     * @covers toufee\Submission\SubmissionFactory::registerService
     */
    public function testRegisterServiceThrowsExceptionIfNonExistentClass(){
    	$this->setExpectedException('\\toufee\Submission\Exceptions\SubmissionException');
		$factory = new SubmissionFactory();
        $factory->registerService('foo', 'bar');
    }

    /**
     * @covers toufee\Submission\SubmissionFactory::registerService
     */
    public function testRegisterServiceThrowsExceptionIfClassNotFulfillsContract(){
    	$this->setExpectedException('\\toufee\Submission\Exceptions\SubmissionException');
		$factory = new SubmissionFactory();
        $factory->registerService('foo', 'toufee\\Submission\\SubmissionFactory');
    }

    /**
     * @covers toufee\Submission\SubmissionFactory::registerService
     */
    public function testRegisterServiceSuccessIfClassFulfillsContract(){
		$factory = new SubmissionFactory();
        $this->assertInstanceOf(
            'toufee\\Submission\\SubmissionFactory',
            $factory->registerService('foo', 'toufee\\Submission\\Mocks\\MockService')
        );
    }

	/**
     * @covers toufee\Submission\SubmissionFactory::registerServiceAlias
     * @covers toufee\Submission\SubmissionFactory::registerService
     */
    public function testRegisterServiceAlias(){
    	Config::set('submission.services.twitter.client_id', 'test');
        Config::set('submission.services.twitter.client_secret', 'best');
    	Config::set('submission.services.alias.client_id', 'test');
        Config::set('submission.services.alias.client_secret', 'best');

        $factory = new SubmissionFactory();
        $service = $factory->createService('alias');

        $this->assertNull($service);

        $factory->registerServiceAlias('twitter', 'alias');

        $newService = $factory->createService('alias');

        $this->assertInstanceOf('toufee\\Submission\\Services\\Twitter', $newService);
    }

    /**
     * @covers toufee\Submission\SubmissionFactory::createService
     * @covers toufee\Submission\SubmissionFactory::fullyQualifiedServiceName
     * @covers toufee\Submission\SubmissionFactory::registerService
     * @covers toufee\Submission\SubmissionFactory::buildService
     */
    public function testCreateServiceUserRegistered(){
    	Config::set('submission.services.foo.email', 'test@best.com');

        $factory = new SubmissionFactory();
        $service = $factory->createService('foo');

        $this->assertNull($service);

        $factory->registerService('foo', 'toufee\Submission\Mocks\MockService');

        $newService = $factory->createService('foo');

        $this->assertInstanceOf('toufee\\Submission\\Contracts\\Service', $newService);
    }

    /**
     * @covers toufee\Submission\SubmissionFactory::createService
     * @covers toufee\Submission\SubmissionFactory::fullyQualifiedServiceName
     * @covers toufee\Submission\SubmissionFactory::registerService
     * @covers toufee\Submission\SubmissionFactory::buildService
     */
    public function testCreateServiceUserRegisteredOverridesPreLoaded(){
    	Config::set('submission.services.twitter.client_id', 'test');
        Config::set('submission.services.twitter.client_secret', 'best');

        $factory = new SubmissionFactory();
        $factory->registerService('twitter', 'toufee\Submission\Mocks\MockService');

        $service = $factory->createService('twitter');

        $this->assertInstanceOf('toufee\\Submission\\Mocks\\MockService', $service);
    }
}
