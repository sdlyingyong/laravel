<?php

//在框架执行时 ,把这里配置赋值给类属性,类中遍历属性来根据用户配置加载方法,完成过滤
return array(

	/*
	|--------------------------------------------------------------------------
	| Filters
	|--------------------------------------------------------------------------
	|
	| Filters provide a convenient method for filtering access to your route
	| functions. To make your life easier, we have already setup basic filters
	| for authentication and CSRF protection.
	|
	| For more information, check out: http://laravel.com/docs/basics/routes#filters
	|
	*/

	'before' => function()
	{
		// Do stuff before every request is executed.	
	},


	'after' => function($response)
	{
		// Do stuff after every request is executed.
	},


	'auth' => function()
	{
		return ( ! Auth::check()) ? Redirect::to_login() : null;
	},


	'csrf' => function()
	{
		return (Input::get('csrf_token') !== Form::raw_token()) ? Response::view('error/500', 500) : null;
	},

);