<?php
namespace Pathname;

trait StateTrait
{
	protected function stat()
	{
		static $stat = NULL;
		if (is_null($stat))
		{
			$stat = stat($this->to_s());
		}
		return $stat;
	}

	public function device()
	{
		return $this->stat()[0];
	}

	public function inode()
	{
		return $this->stat()[1];
	}

	public function mode()
	{
		return $this->stat()[2];
	}

	public function nlink()
	{
		return $this->stat()[3];
	}

	public function uid()
	{
		return $this->stat()[4];
	}

	public function gid()
	{
		return $this->stat()[5];
	}

	public function rdev()
	{
		return $this->stat()[6];
	}

	public function size()
	{
		return $this->stat()[7];
	}

	public function atime()
	{
		return new DateTimeImmutable('@'.(string)$this->stat()[8]);
	}

	public function mtime()
	{
		return new DateTimeImmutable('@'.(string)$this->stat()[9]);
	}

	public function ctime()
	{
		return new DateTimeImmutable('@'.(string)$this->stat()[10]);
	}

	public function block_size()
	{
		return $this->stat()[11];
	}

	public function blocks()
	{
		return $this->stat()[12];
	}
}
