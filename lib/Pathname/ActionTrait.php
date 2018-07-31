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
}
