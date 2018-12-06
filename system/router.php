<?php namespace System;

class Router {

	/**
	 * All of the loaded routes.
	 *
	 * @var array
	 */
	public static $routes;

	/**
	 * The named routes that have been found so far.
	 *
	 * @var array
	 */
	public static $names = array();

	/**
	 * Search a set of routes for the route matching a method and URI.
     * //绑定执行路由对应的方法和网址
	 *
	 * @param  string  $method
	 * @param  string  $uri
	 * @return Route
	 */
	//传入请求方法和请求路径
    //返回一个路由对象
	public static function route($method, $uri)
	{
		// --------------------------------------------------------------
		// Add a forward slash to the URI if necessary.
		// --------------------------------------------------------------
        //检查路径是否是/,是的话转为/路径,否则不变
		$uri = ($uri != '/') ? '/'.$uri : $uri;

		// --------------------------------------------------------------
		// Load all of the application routes.
		// --------------------------------------------------------------
        //引入包含路径的数组,类似于
//              [
//          	    'GET /' => function(){
//                      return View::make('home/index');
//                  },
//                    'POST /index' => function(){
//                        return View:make('index/index');
//                    }
//              ]
        //获得路由对应的模板
		static::$routes = require APP_PATH.'routes'.EXT;

		// --------------------------------------------------------------
		// Is there an exact match for the request?
		// --------------------------------------------------------------
        //如果从app/routes取到路由,则交给system/route类,返回一个路由对象路由
		if (isset(static::$routes[$method . ' ' . $uri]))
		{
			return new Route(static::$routes[$method.' '.$uri]);
		}

		// --------------------------------------------------------------
		// No exact match... check each route individually.
		// --------------------------------------------------------------
        //没有匹配的情况
        //将route中的数组遍历 method作为key uri作为回调函数
		foreach (static::$routes as $keys => $callback)
		{
			// --------------------------------------------------------------
			// Only check routes that have multiple URIs or wildcards.
			// All other routes would have been caught by a literal match.
			// --------------------------------------------------------------
            //查找method中 (出现或者 , 出现,那么进行处理
			if (strpos($keys, '(') !== false or strpos($keys, ',') !== false )
			{
				// --------------------------------------------------------------
				// Multiple routes can be assigned to a callback using commas.
				// --------------------------------------------------------------
                //里面有,的字符串,解析成数组进行遍历,也就是遍历route,
				foreach (explode(', ', $keys) as $route)
				{
					// --------------------------------------------------------------
					// Change wildcards into regular expressions.
					// --------------------------------------------------------------
                    //框架提供的 any响应多个请求method,:num
					$route = str_replace(':num', '[0-9]+', str_replace(':any', '.+', $route));

					// --------------------------------------------------------------
					// Test the route for a match.
					// --------------------------------------------------------------
                    //如果匹配到method 和 uri 的话 ,返回一个路由对象
					if (preg_match('#^' . $route . '$#', $method.' '.$uri))
					{
					    //route(uri,参数)
                        //参数由拆分组织参数的函数处理后提供
						return new Route($callback, static::parameters(explode('/', $uri), explode('/', $route)));
					}
				}				
			}
		}
	}

	/**
	 * Find a route by name.
	 *
	 * @param  string  $name
	 * @return array
	 */
	public static function find($name)
	{
		// ----------------------------------------------------
		// Have we already looked up this named route?
		// ----------------------------------------------------
		if (array_key_exists($name, static::$names))
		{
			return static::$names[$name];
		}

		// ----------------------------------------------------
		// Instantiate the recursive array iterator.
		// ----------------------------------------------------
		$arrayIterator = new \RecursiveArrayIterator(static::$routes);

		// ----------------------------------------------------
		// Instantiate the recursive iterator iterator.
		// ----------------------------------------------------
		$recursiveIterator = new \RecursiveIteratorIterator($arrayIterator);

		// ----------------------------------------------------
		// Iterate through the routes searching for a route
		// name that matches the given name.
		// ----------------------------------------------------
		foreach ($recursiveIterator as $iterator)
		{
			$route = $recursiveIterator->getSubIterator();

			if ($route['name'] == $name)
			{
				return static::$names[$name] = array($arrayIterator->key() => iterator_to_array($route));
			}
		}
	}

	/**
	 * Get the parameters that should be passed to the route callback.
	 *
	 * @param  array  $uri_segments
	 * @param  array  $route_segments
	 * @return array
	 */
	//获取路由传递给回调的参数
	private static function parameters($uri_segments, $route_segments)
	{
		$parameters = array();

		// --------------------------------------------------------------
		// Spin through the route segments looking for parameters.
		// --------------------------------------------------------------
        //遍历路由的segments
		for ($i = 0; $i < count($route_segments); $i++)
		{
			// --------------------------------------------------------------
			// Any segment wrapped in parentheses is a parameter.
			// --------------------------------------------------------------
            //如果元素拥有(,把这个uri元素存入返回的参数数据中
			if (strpos($route_segments[$i], '(') === 0)
			{
				$parameters[] = $uri_segments[$i];
			}
		}

		return $parameters;		
	}

}