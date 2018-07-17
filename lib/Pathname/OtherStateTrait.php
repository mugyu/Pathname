<?php
namespace Pathname;

trait OtherStateTrait
{
	public function exists()
	{
		return file_exists($this->to_s());
	}

	public function is_file()
	{
		return is_file($this->to_s());
	}

	public function is_dir()
	{
		return is_dir($this->to_s());
	}

	public function is_link()
	{
		return is_link($this->to_s());
	}

	public function is_readable()
	{
		return is_readable($this->to_s());
	}

	public function is_writeable()
	{
		return is_writeable($this->to_s());
	}
}
