<?php
namespace Pathname;
require_once __DIR__.'/../Pathname.php';

class ChildrenIterator implements \Iterator
{
	protected $directoryIterator;
	protected $with_directory = TRUE;
	protected $current_item;
	function __construct($path, $with_directory = TRUE)
	{
		$this->directoryIterator = new \DirectoryIterator($path);
		$this->with_directory = $with_directory;
	}

	public function current()
	{
		return new \Pathname($this->current_item->__toString());
	}

	public function key()
	{
		return $this->directoryIterator->key();
	}

	public function next()
	{
		return $this->directoryIterator->next();
	}

	public function rewind()
	{
		return $this->directoryIterator->rewind();
	}

	public function valid()
	{
		$valid = $this->directoryIterator->valid();
		if ( ! $valid)
		{
			return FALSE;
		}
		return $this->_next();
	}

	protected function _next()
	{
		$item = $this->directoryIterator->current();
		if ($item->__toString() === '.' || $item->__toString() === '..')
		{
			$this->next();
			$valid = $this->valid();
			if ( ! $valid)
			{
				return FALSE;
			}
			return $this->_next();
		}
		if ( ! $this->with_directory && $item->isDir())
		{
			$this->next();
			$valid = $this->valid();
			if ( ! $valid)
			{
				return FALSE;
			}
			return $this->_next();
		}
		$this->current_item = $item;
		return TRUE;
	}
}
