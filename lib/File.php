<?php
class File
{
	// TODO: とりあえず今は Pathname の一部だが、頃合いを見て独立させる
	// TODO: クラス名が一般的すぎで汎用的には使いづらそう。かといって、複雑な長い名前も用途に合わない

	/**
	 * open 
	 * 
	 * @param string $path ファイルパス
	 * @param mixed $mode アクセスモード
	 * @param callable $callback ファイルハンドル操作関数
	 * @static
	 * @access public
	 * @return void
	 */
	public static function open($path, $mode, $callback = NULL)
	{
		if (is_null($callback)) {
			return fopen($path, $mode);
		}
		$fh = fopen($path, $mode);
		call_user_func($callback, $fh);
		self::close($fh);
	}

	/**
	 * open 
	 * 
	 * @param mixed $file_handle ファイルハンドル
	 * @static
	 * @access public
	 * @return void
	 */
	public static function close($file_handle)
	{
		return fclose($file_handle);
	}

	/**
	 * read 
	 * 
	 * @param string $path ファイルパス
	 * @param callable $callback 一行別の処理
	 * @static
	 * @access public
	 * @return void
	 */
	public static function read($path, $callback)
	{
		self::open($path, "r", function($fh) use($callback)
		{
			while( ! feof($fh))
			{
				$value = fgets($fh);
				call_user_func($callback, $value);
			}
		});
	}

	/**
	 * getcwd 
	 * 
	 * @static
	 * @access public
	 * @return void
	 */
	public static function getcwd()
	{
		return preg_replace(self::separator_pattern(), '/', getcwd());
	}

	/**
	 * pwd alias getcwd 
	 * 
	 * @static
	 * @access public
	 * @return void
	 */
	public static function pwd()
	{
		return self::getcwd();
	}

	/**
	 * truncate 
	 * 
	 * @param string $path ファイルパス
	 * @param mixed $size ファイルサイズ
	 * @static
	 * @access public
	 * @return void
	 */
	public static function truncate($path, $size)
	{
		$result = NULL;
		self::open($path, "w", function($fh) use($size, &$result)
		{
			$result = ftruncate($fh, $size);
		});
		return $result;
	}

	protected static $separator_pattern = NULL;
	protected static function separator_pattern() {
		if ( ! self::$separator_pattern)
		{
			if (DIRECTORY_SEPARATOR === '/')
			{
				self::$separator_pattern = '/\//';
			}
			else
			{
				self::$separator_pattern = '/\/|\\\/';
			}
		}
		return self::$separator_pattern;
	}
}
