<?php namespace System;

class Filter {

	/**
	 * The loaded route filters.
	 *
	 * @var array
	 */
	public static $filters;

	/**
	 * Call a set of route filters.
	 *
	 * @param  string  $filter
	 * @param  array   $parameters
	 * @return mixed
	 */
	public static function call($filters, $parameters = array())
	{
		// --------------------------------------------------------------
		// Load the route filters.
		// --------------------------------------------------------------
        //首次执行,从别的文件加载过滤器到类属性
		if (is_null(static::$filters))
		{
			static::$filters = require APP_PATH.'filters'.EXT;
		}

		//遍历过滤器
		foreach (explode(', ', $filters) as $filter)
		{
			// --------------------------------------------------------------
			// Verify that the filter is defined.
			// --------------------------------------------------------------
            //检查过滤器类属性是否有这个,没有抛异常给提示
			if ( ! isset(static::$filters[$filter]))
			{
				throw new \Exception("Route filter [$filter] is not defined.");						
			}

			//调用带有参数数组的用户函数,
            //函数在过滤器对应里面(),参数传入
			$response = call_user_func_array(static::$filters[$filter], $parameters);

			// --------------------------------------------------------------
			// If the filter returned a response, return it.
			// --------------------------------------------------------------
            //如果有返回值,则返回响应
			if ( ! is_null($response))
			{
				return $response;
			}
		}
	}

}