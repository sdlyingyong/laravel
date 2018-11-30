<?php
/**
 * Laravel - A clean and classy framework for PHP web development.
 *
 * @package  Laravel
 * @version  1.0.0 Beta 1
 * @author   Taylor Otwell
 * @license  MIT License
 * @link     http://laravel.com 
 */

// --------------------------------------------------------------
// Set the framework starting time.
// --------------------------------------------------------------
//开始时间定为常量 laravel_start
define('LARAVEL_START', microtime(true));

// --------------------------------------------------------------
// Define the framework paths.
// --------------------------------------------------------------
//定义路径:项目,系统,根目录
define('APP_PATH', realpath('../application').'/');
define('SYS_PATH', realpath('../system').'/');
define('BASE_PATH', realpath('../').'/');

// --------------------------------------------------------------
// Define the PHP file extension.
// --------------------------------------------------------------
//定义文件类型,好看?
define('EXT', '.php');

// --------------------------------------------------------------
// Load the configuration and string classes.
// --------------------------------------------------------------
//引入配置 处理字符编码
require SYS_PATH.'config'.EXT;
require SYS_PATH.'str'.EXT;

// --------------------------------------------------------------
// Register the auto-loader.
// --------------------------------------------------------------
//注册自动加载类
//加载模型,package...
spl_autoload_register(require SYS_PATH.'loader'.EXT);

// --------------------------------------------------------------
// Set the Laravel starting time in the Benchmark class.
// --------------------------------------------------------------
//应用开始时间下记录方法
System\Benchmark::$marks['laravel'] = LARAVEL_START;

// --------------------------------------------------------------
// Set the error reporting level.
// --------------------------------------------------------------
//根据配置设置错误输出级别,不配置输出全部错误
error_reporting((System\Config::get('error.detail')) ? E_ALL | E_STRICT : 0);

// --------------------------------------------------------------
// Register the error handlers.
// --------------------------------------------------------------
set_exception_handler(function($e)
{
	System\Error::handle($e);	
});

//设置自定义错误处理函数
set_error_handler(function($number, $error, $file, $line) 
{
	System\Error::handle(new ErrorException($error, 0, $number, $file, $line));
});

//执行完脚本后 如果有错误 抛出处理
register_shutdown_function(function()
{
	if ( ! is_null($error = error_get_last()))
	{
		System\Error::handle(new ErrorException($error['message'], 0, $error['type'], $error['file'], $error['line']));
	}	
});

// --------------------------------------------------------------
// Set the default timezone.
// --------------------------------------------------------------
//
//注册时区 读取配置更改
date_default_timezone_set(System\Config::get('application.timezone'));

// --------------------------------------------------------------
// Load the session.
// --------------------------------------------------------------
//session可以存在文件,数据库,缓存 取决于陪配置
if (System\Config::get('session.driver') != '')
{
	System\Session::load();
}

// --------------------------------------------------------------
// Execute the global "before" filter.
// --------------------------------------------------------------
$response = System\Filter::call('before');

// --------------------------------------------------------------
// Only execute the route function if the "before" filter did
// not override by sending a response.
// --------------------------------------------------------------
if (is_null($response))
{
	// ----------------------------------------------------------
	// Route the request to the proper route.
	// ----------------------------------------------------------
	$route = System\Router::route(Request::method(), Request::uri());

	// ----------------------------------------------------------
	// Execute the route function.
	// ----------------------------------------------------------
	if ( ! is_null($route))
	{
		$response = $route->call();	
	}
	else
	{
		$response = System\Response::view('error/404', 404);
	}
}
else
{
	$response = ( ! $response instanceof System\Response) ? new System\Response($response) : $response;
}

// ----------------------------------------------------------
// Execute the global "after" filter.
// ----------------------------------------------------------
System\Filter::call('after', array($response));

// --------------------------------------------------------------
// Close the session.
// --------------------------------------------------------------
if (System\Config::get('session.driver') != '')
{
	System\Session::close();
}

// --------------------------------------------------------------
// Send the response to the browser.
// --------------------------------------------------------------
$response->send();