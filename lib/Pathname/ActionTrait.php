<?php
namespace Pathname;

trait ActionTrait
{
	public function touch($time = NULL)
	{
		return touch($this->to_s(), $time);
	}

	public function delete()
	{
		return $this->unlink();
	}

	public function unlink()
	{
		return unlink($this->to_s());
	}

	public function mkdir($mode = 777)
	{
		return mkdir($this->to_s(), $mode);
	}

	public function rmdir()
	{
		return rmdir($this->to_s());
	}

	public function rename($rename_path)
	{
		rename($this->to_s(), (string)$rename_path);
		return new self($rename_path);
	}

	public function chmod($mode)
	{
		$result = chmod($this->to_s(), $mode);
		if ($result)
		{
			clearstatcache(TRUE, $this->to_s());
		}
		return $result;
	}

	public function chown($user)
	{
		$result = chown($this->to_s(), $user);
		if ($result)
		{
			clearstatcache(TRUE, $this->to_s());
		}
		return $result;
	}

	public function chgrp($group)
	{
		$result = chgrp($this->to_s(), $group);
		if ($result)
		{
			clearstatcache(TRUE, $this->to_s());
		}
		return $result;
	}
}
