<?php namespace System\Session\Driver;

class File implements \System\Session\Driver {

	/**
	 * Load a session by ID.
	 *
	 * @param  string  $id
	 * @return array
	 */
	public function load($id)
	{
		// -----------------------------------------------------
		// Look for the session on the file system.
		// -----------------------------------------------------
		if (file_exists($path = APP_PATH.'sessions/'.$id))
		{
			return unserialize(file_get_contents($path));
		}
	}

	/**
	 * Save a session.
	 *
	 * @param  array  $session
	 * @return void
	 */
	//把session写到项目下sessions/下文件里
	public function save($session)
	{
		file_put_contents(APP_PATH.'sessions/'.$session['id'], serialize($session), LOCK_EX);
	}

	/**
	 * Delete a session by ID.
	 *
	 * @param  string  $id
	 * @return void
	 */
	public function delete($id)
	{
		//删除项目路径/sessions/下的指定文件,也就是删除了session
		@unlink(APP_PATH.'sessions/'.$id);
	}

	/**
	 * Delete all expired sessions.
	 *
	 * @param  int   $expiration
	 * @return void
	 */
	//删除过期session文件,
	//获取sessions下所有文件,遍历,判断是file并且修改时间小雨过期时间的,一一删除
	public function sweep($expiration)
	{
		foreach (glob(APP_PATH.'sessions/*') as $e)
		{
			// -----------------------------------------------------
			// If the session file has expired, delete it.
			// -----------------------------------------------------
			if (filetype($file) == 'file' and filemtime($file) < $expiration)
			{
				@unlink($file);
			}			
		}
	}
	
}