<?php namespace Userdesk\Submission;

use Illuminate\Support\ServiceProvider;
use Userdesk\Submission\Submitter;

class SubmissionServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap the application services.
     *
     * @return void
     */
    public function boot()
    {
        $this->publishes([
            __DIR__.'/resources/config/submission.php' => config_path('submission.php'),
        ], 'config');
    }

    /**
     * Indicates if loading of the provider is deferred.
     *
     * @var bool
     */
    protected $defer = false;

    /**
     * Register the application services.
     *
     * @return void
     */
    public function register(){
        $this->app->bind('submission', function(){
            return new Submitter;
        });
    }

    /**
     * Get the services provided by the provider.
     *
     * @return array
     */
    public function provides(){
        return ['submission'];
    }
}
