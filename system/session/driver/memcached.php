<?php namespace System\Session\Driver;

class Memcached implements \System\Session\Driver {

	/**
	 * Load a session by ID.
	 *
	 * @param  string  $id
	 * @return array
	 */
	public function load($id)
	{
		return \System\Cache::driver('memcached')->get($id);
	}

	/**
	 * Save a session.
	 *
	 * @param  array  $session
	 * @return void
	 */
	//保存sessioin到memcached
	public function save($session)
	{
		\System\Cache::driver('memcached')->put($session['id'], $session, \System\Config::get('session.lifetime'));
	}

	/**
	 * Delete a session by ID.
	 *
	 * @param  string  $id
	 * @return void
	 */
	public function delete($id)
	{
		\System\Cache::driver('memcached')->forget($id);
	}

	/**
	 * Delete all expired sessions.
	 *
	 * @param  int   $expiration
	 * @return void
	 */
	//memcached缓存自动失效
	public function sweep($expiration)
	{
		// Memcached sessions will expire automatically.
	}

}