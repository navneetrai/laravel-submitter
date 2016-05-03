# Social Media Submitter for Laravel 5

[![Build Status](https://travis-ci.org/navneetrai/laravel-submitter.svg)](https://travis-ci.org/navneetrai/laravel-submitter)
[![Coverage Status](https://coveralls.io/repos/navneetrai/laravel-submitter/badge.svg)](https://coveralls.io/r/navneetrai/laravel-submitter)
[![Total Downloads](https://poser.pugx.org/navneetrai/laravel-submitter/downloads.svg)](https://packagist.org/packages/navneetrai/laravel-submitter)
[![Latest Stable Version](https://poser.pugx.org/navneetrai/laravel-submitter/v/stable.svg)](https://packagist.org/packages/navneetrai/laravel-submitter)
[![Latest Unstable Version](https://poser.pugx.org/navneetrai/laravel-submitter/v/unstable.svg)](https://packagist.org/packages/navneetrai/laravel-submitter)
[![License](https://poser.pugx.org/navneetrai/laravel-submitter/license.svg)](https://packagist.org/packages/navneetrai/laravel-submitter)

laravel-submitter is a simple laravel 5 library for uploading media (videos and images) and adding status updates and website link to a user account on popular social sites like [Facebook](https://www.facebook.com), [Twitter](https://twitter.com) and [Youtube](https://youtube.com). 

This library also help in obtaining user authentication tokens which can be used for delayed submissions.

---
 
- [Supported services](#supported-services)
- [Installation](#installation)
- [Registering the Package](#registering-the-package)
- [Configuration](#configuration)
- [Usage](#usage)

## Supported services

The library supports [Facebook](https://www.facebook.com), [Twitter](https://twitter.com) and [Youtube](https://youtube.com). More services will be implemented soon.

Included service implementations:

 - Facebook
 - Twitter
 - Youtube
- more to come!


## Installation

Add laravel-submitter to your composer.json file:

```
"require": {
  "navneetrai/laravel-submitter": "^1.0"
}
```

Use composer to install this package.

```
$ composer update
```

### Registering the Package

Register the service provider within the ```providers``` array found in ```config/app.php```:

```php
'providers' => [
  // ...
  
  Userdesk\Submission\SubmissionServiceProvider::class,
]
```

Add an alias within the ```aliases``` array found in ```config/app.php```:


```php
'aliases' => [
  // ...
  
  'Submission'     => Userdesk\Submission\Facades\Submission::class,
]
```

## Configuration

There are two ways to configure laravel-submitter.

#### Option 1

Create configuration file for package using artisan command

```
$ php artisan vendor:publish --provider="Userdesk\Submission\SubmissionServiceProvider"
```

#### Option 2

Create configuration file manually in config directory ``config/subscription.php`` and put there code from below.

```php
<?php

return [

	/*
	|--------------------------------------------------------------------------
	| Submission Config
	|--------------------------------------------------------------------------
	*/

	/**
	 * Services
	 */
	'services' => [

		'facebook' => [
			'client_id'     => '',
			'client_secret' => '',
			'scope'         => ['email', 'public_profile', 'publish_actions'],
		],

		'twitter' => [
		    'client_id'     => '',
		    'client_secret' => '',
		    // No scope - oauth1 doesn't need scope
		],

		'youtube' => [
		    'client_id'     => '',
		    'client_secret' => '',
		    'scope'         => [
							    'profile', 'email',
							    'https://www.googleapis.com/auth/youtube',
								'https://www.googleapis.com/auth/youtube.upload',
								'https://www.googleapis.com/auth/youtube.readonly'
								],
		],

	]

];
```

### Credentials

Add your credentials to ``config/submission.php`` (depending on which option of configuration you choose)


## Usage

### Basic usage

Just follow the steps below and you will be able to get a submitter instance:

```php
$submitter = Submission::submitter('twitter');
```

#### Redirecting Users to Website for authentication

To start the authentication process you can call the authenticate method of your submitter instance:

```php
$submitter = Submission::submitter($website);
return $submitter->authenticate($state);
```

You should also pass an integer as ``$state`` to this method. This variable will act as a unique identifier for your request and you can use it later for storing user authentication token.

You will get a Redirect Response which will take user to Website for Authentication.

#### Completing Submission

User authentication is an oauth based two-step process. Also the process for authentication differs from site to site. This package is designed to encapsulate these differences any provide a uniform interface across both Oauth 1 sites (like Twitter) and Oauth 2 sites (like Facebook)

For this purpose you'll need to add a named route to your ``routes.php`` file in ``app\Http`` directory for authentication to automatically handle Oauth redirects.

```php
Route::get('/any-base-url-of-your-liking/{website}/{state?}', ['as'=>'package.Userdesk.submission.authenticate', 'uses'=>'YourSubmissionController@authenticate']);
```

You can set redirection URL for each individual site as ``http://your-website-host.com/any-base-url-of-your-liking/website-name``

So, if we assume that your website host is ``http://www.example.com`` and you used ``authenticate`` as your base URL then in your Facebook developer profile you should set Redirection URL as ``http://www.example.com/authenticate/facebook``

This method called above will automatically redirect users, after authentication, to our named route.

To complete the authentication process the ``authenticate`` function in ``YourSubmissionController`` should look like:

```php
public function authenticate(Request $request, String $website, int $state = 0){
	$submitter = Submission::submitter($website);
	$credentials = $submitter->completeAuthentication($request, $state);

	//Fetch Information from Credentials Object

	$state = $credentials->getState();
	$user = $credentials->getUser();

	$username = $user->getId();		
	$info = $user->getProfile();

	$token = $credentials->getToken();
	$tokenArray = $token->getTokenArray();

	//You can now save tokenArray to your database for later use
}
```

#### Using Stored Token to Submit Videos

You can use the token stored above to submit videos on the target website using this code:

```php
$submitter = Submitter::submitter($website);

try{	
	$token = new SubmissionToken();
	$token->addTokenArray($tokenArray);

	$video = new SubmissionVideoItem($title, $description, $localThumb, $localVideo, $keywords);

	$submitter->addToken($token);
	$result = $submitter->uploadVideo($video);
	
	$status = $result->getStaus();
	$url = $result->getUrl();
}catch(Exception $e){

}			
```

Keywords should be a comma separated string. Not all website uses keywords for Video Submission.

Each website has its own policy for video uploads. Your video should confirm to website policy for upload to be successful.

e.g. [For Twitter videos can't be longer than 30 seconds](https://dev.twitter.com/rest/media/uploading-media#videorecs)


#### Using Stored Token to Submit Images

You can use the token stored above to submit images on the target website using this code:

```php
$submitter = Submitter::submitter($website);

try{	
	$token = new SubmissionToken();
	$token->addTokenArray($tokenArray);

	$image = new SubmissionImageItem($title, $description, $localImage, $keywords);

	$submitter->addToken($token);
	$result = $submitter->uploadImage($image);
	
	$status = $result->getStaus();
	$url = $result->getUrl();
}catch(Exception $e){

}			
```
Keywords should be a comma separated string. Not all website uses keywords for Image Submission.

Some websites like Youtube do not support image uploads.

Each website has its own policy for image uploads. Your image should confirm to website policy for upload to be successful.


#### Using Stored Token to Submit Links

You can use the token stored above to submit links on the target website using this code:

```php
$submitter = Submitter::submitter($website);

try{	
	$token = new SubmissionToken();
	$token->addTokenArray($tokenArray);

	$link = new SubmissionLinkItem($title, $description, $link, $keywords);

	$submitter->addToken($token);
	$result = $submitter->addLink($link);
	
	$status = $result->getStaus();
	$url = $result->getUrl();
}catch(Exception $e){

}			
```
Keywords should be a comma separated string. Off the current websites only Facebook support link submission.

Each website has its own policy for link submission. Your link should confirm to website policy for upload to be successful.


#### Using Stored Token to Add Status updates/Tweets

You can use the token stored above to add status update/tweet on the target website using this code:

```php
$submitter = Submitter::submitter($website);

try{	
	$token = new SubmissionToken();
	$token->addTokenArray($tokenArray);

	$update = new SubmissionStatusItem($title, $status, $keywords);

	$submitter->addToken($token);
	$result = $submitter->addStatus($update);
	
	$status = $result->getStaus();
	$url = $result->getUrl();
}catch(Exception $e){

}			
```

Some websites like Youtube do not support status updates.

Each website has its own policy for status updates. Your link should confirm to website policy for upload to be successful.

e.g. Twitter needs status to be within 140 characters.