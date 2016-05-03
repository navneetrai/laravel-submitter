<?php namespace Userdesk\Submission\Facades;
use Illuminate\Support\Facades\Facade;

class Submission extends Facade {
    protected static function getFacadeAccessor() { 
    	return 'submission'; 
    }
}
