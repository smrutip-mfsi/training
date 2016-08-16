<?php

/*
|--------------------------------------------------------------------------
| Application Routes
|--------------------------------------------------------------------------
|
| Here is where you can register all of the routes for an application.
| It's a breeze. Simply tell Laravel the URIs it should respond to
| and give it the controller to call when that URI is requested.
|
*/



Route::get('/', ['as' => 'home', function () {
		return view('home');
}]);


Route::group(['middleware' => 'user_access'], function ()
{
	Route::get('dashboard', 
		[
			'as' => 'dashboard',
			'uses' => 'DashBoardController@dashboard'
		]);

	Route::get('details', ['uses' => 'DatatablesController@getIndex']);
	Route::get('edit', 'EmployeeController@edit');
	Route::get('delete', 'EmployeeController@delete');
	Route::get('view', 'EmployeeController@view');
	Route::get('add_user', 'AuthController@register');
	Route::get('permission', 'AuthController@displayPermissionManager');
	Route::get('change_permission', 'AuthController@changePermission');


});

Route::get('twitter', 'EmployeeController@twitter');

Route::get('register', 
	[
		'as' => 'register',
		'uses' => 'AuthController@register'
	]);

Route::post('do-register', 
	[
		'as' => 'do-register',
		'uses' => 'AuthController@doRegister'
	]);

Route::get('login', 
	[
		'as' => 'login',
		'uses' => 'AuthController@login'
	]);

Route::post('do-login', 
	[
		'as' => 'do-login',
		'uses' => 'AuthController@doLogin'
	]);

Route::get('logout', 
	[
		'as' => 'logout',
		'uses' => 'DashBoardController@logout'
	]);

Route::get('login/verify', 
	['uses' => 'AuthController@emailVerification']);

Route::get('reset-password', 'AuthController@resetPassword');

Route::post('do-reset-password', 'AuthController@doResetPassword');

Route::get('linkedin-login', 
	[
		'as' => 'linkedin-login',
		'uses' => 'AuthController@redirectToProviderLinkedin'
	]);

Route::get('twitter-login', 
	[
		'as' => 'twitter-login',
		'uses' => 'AuthController@redirectToProviderTwitter'
	]);

Route::get('callback', 
	[
		'as' => 'callback',
		'uses' => 'AuthController@handleProviderCallback'
	]);

Route::post('users/{id?}', 
	[
		'uses' => 'ApiController@getUsers',
		function ($id = 0)
		{
			
		}
	]);