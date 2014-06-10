<?php

/*
|--------------------------------------------------------------------------
| Application Routes
|--------------------------------------------------------------------------
|
| Here is where you can register all of the routes for an application.
| It's a breeze. Simply tell Laravel the URIs it should respond to
| and give it the Closure to execute when that URI is requested.
|
*/

Route::get('/', function()
{
	return View::make('hello');
});

/**
 * POST /oauth/access_token
 *
 * Retrieve an access token to authorize future API calls.
 */
Route::post('/oauth/access_token', function()
{
  return AuthorizationServer::performAccessTokenFlow();
});

/**
 * GET /user
 *
 * A valid token is required to access this resource.
 */
Route::get('/user', array('before' => 'oauth', function()
{
  return 'This is the /user endpoint.';
}));
