<?php namespace System\Session;

class Factory {

	/**
	 * //创建并返回一个session存储实例
	 * Create a session driver instance.
	 *
	 * @param  string  $driver
	 * @return Driver
	 */
	public static function make($driver)
	{
		switch ($driver)
		{
			case 'file':
				return new Driver\File;

			case 'db':
				return new Driver\DB;

			case 'memcached':
				return new Driver\Memcached;

			default:
				throw new \Exception("Session driver [$driver] is not supported.");
		}
	}

}