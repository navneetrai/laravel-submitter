<?php namespace Userdesk\Tests\Submission;

use Illuminate\Foundation\Testing\WithoutMiddleware;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Foundation\Testing\DatabaseTransactions;

use Userdesk\Submission\SubmissionFactory;

use Config;

class SubmissionFactoryTest extends \Orchestra\Testbench\TestCase
{   

    /**
     * @covers Userdesk\Submission\SubmissionFactory::createService
     */
    public function testCreateServiceThrowsExceptionIfNoConfig(){
    	$this->setExpectedException('\\Userdesk\Submission\Exceptions\SubmissionException');
		$factory = new SubmissionFactory();
        $service = $factory->createService('twitter');
    }

    /**
     * @covers Userdesk\Submission\SubmissionFactory::createService
     * @covers Userdesk\Submission\SubmissionFactory::fullyQualifiedServiceName
     * @covers Userdesk\Submission\SubmissionFactory::buildService
     */
    public function testCreateServiceNonExistentService() {
    	Config::set('submission.services.foo.email', 'test@best.com');

        $factory = new SubmissionFactory();
        $service = $factory->createService('foo');

        $this->assertNull($service);
    }

    /**
     * @covers Userdesk\Submission\SubmissionFactory::createService
     * @covers Userdesk\Submission\SubmissionFactory::fullyQualifiedServiceName
     * @covers Userdesk\Submission\SubmissionFactory::buildService
     */
    public function testCreateServicePreLoaded(){
    	Config::set('submission.services.twitter.client_id', 'test');
        Config::set('submission.services.twitter.client_secret', 'best');

		$factory = new SubmissionFactory();
        $service = $factory->createService('twitter');

        $this->assertInstanceOf('Userdesk\\Submission\\Services\\Twitter', $service);
    }
	
	/**
     * @covers Userdesk\Submission\SubmissionFactory::registerService
     */
    public function testRegisterServiceThrowsExceptionIfNonExistentClass(){
    	$this->setExpectedException('\\Userdesk\Submission\Exceptions\SubmissionException');
		$factory = new SubmissionFactory();
        $factory->registerService('foo', 'bar');
    }

    /**
     * @covers Userdesk\Submission\SubmissionFactory::registerService
     */
    public function testRegisterServiceThrowsExceptionIfClassNotFulfillsContract(){
    	$this->setExpectedException('\\Userdesk\Submission\Exceptions\SubmissionException');
		$factory = new SubmissionFactory();
        $factory->registerService('foo', 'Userdesk\\Submission\\SubmissionFactory');
    }

    /**
     * @covers Userdesk\Submission\SubmissionFactory::registerService
     */
    public function testRegisterServiceSuccessIfClassFulfillsContract(){
		$factory = new SubmissionFactory();
        $this->assertInstanceOf(
            'Userdesk\\Submission\\SubmissionFactory',
            $factory->registerService('foo', 'Userdesk\\Submission\\Mocks\\MockService')
        );
    }

	/**
     * @covers Userdesk\Submission\SubmissionFactory::registerServiceAlias
     * @covers Userdesk\Submission\SubmissionFactory::registerService
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

        $this->assertInstanceOf('Userdesk\\Submission\\Services\\Twitter', $newService);
    }

    /**
     * @covers Userdesk\Submission\SubmissionFactory::createService
     * @covers Userdesk\Submission\SubmissionFactory::fullyQualifiedServiceName
     * @covers Userdesk\Submission\SubmissionFactory::registerService
     * @covers Userdesk\Submission\SubmissionFactory::buildService
     */
    public function testCreateServiceUserRegistered(){
    	Config::set('submission.services.foo.email', 'test@best.com');

        $factory = new SubmissionFactory();
        $service = $factory->createService('foo');

        $this->assertNull($service);

        $factory->registerService('foo', 'Userdesk\Submission\Mocks\MockService');

        $newService = $factory->createService('foo');

        $this->assertInstanceOf('Userdesk\\Submission\\Contracts\\Service', $newService);
    }

    /**
     * @covers Userdesk\Submission\SubmissionFactory::createService
     * @covers Userdesk\Submission\SubmissionFactory::fullyQualifiedServiceName
     * @covers Userdesk\Submission\SubmissionFactory::registerService
     * @covers Userdesk\Submission\SubmissionFactory::buildService
     */
    public function testCreateServiceUserRegisteredOverridesPreLoaded(){
    	Config::set('submission.services.twitter.client_id', 'test');
        Config::set('submission.services.twitter.client_secret', 'best');

        $factory = new SubmissionFactory();
        $factory->registerService('twitter', 'Userdesk\Submission\Mocks\MockService');

        $service = $factory->createService('twitter');

        $this->assertInstanceOf('Userdesk\\Submission\\Mocks\\MockService', $service);
    }
}
