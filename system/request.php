<?php namespace System;

class Request {

	/**
	 * The request URI.
	 *
	 * @var string
	 */
	public static $uri;

	/**
	 * Get the request URI.
	 *
	 * @return string
	 */
	public static function uri()
	{
		// --------------------------------------------------------------
		// Have we already determined the URI?
		// --------------------------------------------------------------
        //
        //已经将uri路径保存到类变量中,则直接返回类变量中保存的值
		if ( ! is_null(static::$uri))
		{
			return static::$uri;
		}

		// --------------------------------------------------------------
		// Use the PATH_INFO variable if it is available.
		// --------------------------------------------------------------
        //赋值uri,如果path_info有值.则将其转为小写,确定路径
		if (isset($_SERVER['PATH_INFO']))
		{
			return static::$uri = static::tidy($_SERVER['PATH_INFO']);
		}

		// --------------------------------------------------------------
		// If the server REQUEST_URI variable is not available, bail out.
		// --------------------------------------------------------------
        //检查请求URI,没有则发出错误提示 没有请求uri
		if ( ! isset($_SERVER['REQUEST_URI']))
		{
			throw new \Exception('Unable to determine the request URI.');			
		}

		// --------------------------------------------------------------
		// Get the PHP_URL_PATH of the request URI.
		// --------------------------------------------------------------
		$uri = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);

		// --------------------------------------------------------------
		// Slice the application URL off of the URI.
		// --------------------------------------------------------------
		if (strpos($uri, $base_url = parse_url(Config::get('application.url'), PHP_URL_PATH)) === 0)
		{
			$uri = substr($uri, strlen($base_url));
		}

		return static::$uri = static::tidy($uri);
	}

	/**
	 * Tidy up a URI for use by Laravel. For empty URIs, a forward
	 * slash will be returned.
	 *
	 * @param  string  $uri
	 * @return string
	 */
	private static function tidy($uri)
	{
		return ($uri != '/') ? Str::lower(trim($uri, '/')) : '/';
	}

	/**
	 * Get the request method.
	 *
	 * @return string
	 */
    //
	public static function method()
	{
	    //从POST或者请求体获取到请求方式 REQUEST_METHOD
		// --------------------------------------------------------------
		// The method can be spoofed using a POST variable. This allows 
		// HTML forms to simulate PUT and DELETE methods.
		// --------------------------------------------------------------
		return (isset($_POST['request_method'])) ? $_POST['request_method'] : $_SERVER['REQUEST_METHOD'];
	}

	/**
	 * Get the requestor's IP address.
	 *
	 * @return string
	 */
	public static function ip()
	{
		if (isset($_SERVER['HTTP_X_FORWARDED_FOR']))
		{
			return $_SERVER['HTTP_X_FORWARDED_FOR'];
		}
		elseif (isset($_SERVER['HTTP_CLIENT_IP']))
		{
			return $_SERVER['HTTP_CLIENT_IP'];
		}
		elseif (isset($_SERVER['REMOTE_ADDR']))
		{
			return $_SERVER['REMOTE_ADDR'];
		}
	}

	/**
	 * Determine if the request is using HTTPS.
	 *
	 * @return bool
	 */
	public static function is_secure()
	{
		return (static::protocol() == 'https');
	}

	/**
	 * Get the HTTP protocol for the request.
	 *
	 * @return string
	 */
	public static function protocol()
	{
		return (isset($_SERVER['HTTPS']) and $_SERVER['HTTPS'] !== 'off') ? 'https' : 'http';
	}

	/**
	 * Determine if the request is an AJAX request.
	 *
	 * @return bool
	 */
	public static function is_ajax()
	{
		return (isset($_SERVER['HTTP_X_REQUESTED_WITH']) and Str::lower($_SERVER['HTTP_X_REQUESTED_WITH']) === 'xmlhttprequest');
	}

}