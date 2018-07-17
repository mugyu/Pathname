<?php
namespace Pathname;
require_once __DIR__.'/../File.php';

trait FileTrait
{
	public function open($mode, $callback = NULL)
	{
		return \File::open($this->to_s(), $mode, $callback);
	}

	public function close($file_handle)
	{
		return \File::close($file_handle);
	}

	public function read($callback)
	{
		return \File::read($this->to_s(), $callback);
	}

	public function truncate($size)
	{
		return \File::truncate($this->to_s(), $size);
	}
}
