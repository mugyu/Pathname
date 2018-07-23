<?php
require_once __DIR__.'./pathname/StateTrait.php';
require_once __DIR__.'./pathname/OtherStateTrait.php';
require_once __DIR__.'./pathname/FileTrait.php';
require_once __DIR__.'./pathname/ActionTrait.php';

/**
 * Pathname
 * 
 */
class Pathname
{
	// TODO: コメントが全然足りないので足す
	// TODO: ドキュメントが無いので書く

	use Pathname\StateTrait;
	use Pathname\OtherStateTrait;
	use Pathname\FileTrait;
	use Pathname\ActionTrait;

	const VERSION = '0.1.1';

	protected static $separator_pattern = NULL;
	protected static function separator_pattern() {
		if ( ! self::$separator_pattern)
		{
			if (DIRECTORY_SEPARATOR === '/')
			{
				self::$separator_pattern = '[\/]';
			}
			else
			{
				self::$separator_pattern = '[\/\\\]';
			}
		}
		return self::$separator_pattern;
	}

	public static function getwd()
	{
		return new self(getcwd());
	}

	public static function pwd()
	{
		return self::getwd();
	}

	public static function glob($pattern)
	{
		return array_map(function($path) {return new self($path);}, glob($pattern));
	}

	protected $pathname;

	function __construct($pathname)
	{
		self::separator_pattern();
		$this->pathname = $pathname;
	}

	public function __toString()
	{
		return $this->pathname;
	}

	public function to_s()
	{
		return $this->__toString();
	}

	public function to_path()
	{
		return $this->__toString();
	}

	public function add($other)
	{
		if ( ! is_object($other) || get_class($this) !== get_class($other))
		{
			$other = new self((string)$other);
		}
		if ($other->is_absolute()) {
			return new self($other->to_s());
		}
		return new self($this->plus($this->to_s(), $other->to_s()));
	}

	protected function chop_basename($path)
	{
		$base = basename($path);
		if (preg_match('/\A'.self::$separator_pattern.'?\z/', $path))
		{
			return NULL;
		}
		return [substr($path, 0, strrpos($path, $base)), $base];
	}

	protected function plus($path1, $path2)
	{
		$prefix2 = $path2;
		$index_list2 = [];
		$basename_list2 = [];
		while ($r2 = $this->chop_basename($prefix2))
		{
			list($prefix2, $basename2) = $r2;
			array_unshift($index_list2, strlen($prefix2));
			array_unshift($basename_list2, $basename2);
		}
		if ($prefix2 !== '')
		{
			return $path2;
		}

		$prefix1 = $path1;
		while (true) {
			while ( ! empty($basename_list2) && $basename_list2[0] === '.')
			{
				array_shift($index_list2);
				array_shift($basename_list2);
			}
			if ( ! $r1 = $this->chop_basename($prefix1))
			{
				break;
			}
			list($prefix1, $basename1) = $r1;
			if ($basename1 === '.')
			{
				next;
			}
			if ($basename1 === '..' || empty($basename_list2) || $basename_list2[0] !== '..')
			{
				$prefix1 = $prefix1 . $basename1;
				break;
			}
			array_shift($index_list2);
			array_shift($basename_list2);
		}
		$r1 = $this->chop_basename($prefix1);
		if ( ! $r1 && preg_match('/'.self::$separator_pattern.'/', $prefix1))
		{
			while ( ! empty($basename_list2) && $basename_list2[0] == '..')
			{
				array_shift($index_list2);
				array_shift($basename_list2);
			}
		}
		if ( ! empty($basename_list2))
		{
			$suffix2 = substr($path2, $index_list2[0]);
			if ($r1)
			{
				if (preg_match('/'.self::$separator_pattern.'\z/', $prefix1)) {
					return $prefix1 . $suffix2;
				}
				return $prefix1 . DIRECTORY_SEPARATOR . $suffix2;
			}
			return $prefix1 . $suffix2;
		}
		else
		{
			if ($r1)
			{
				return $prefix1;
			}
			return basename($prefix1);
		}
	}

	public function is_root()
	{
		return is_null($this->chop_basename($this->pathname)) &&
		       preg_match('/'.self::$separator_pattern.'/', $this->pathname);
	}

	public function parent_path()
	{
		return $this->add('..');
	}

	public function is_absolute()
	{
		return ! $this->is_relative();
	}

	public function is_relative()
	{
		$pathname = $this->pathname;
		while ($r = $this->chop_basename($pathname))
		{
			list($pathname, $_dummy) = $r;
		}
		return $pathname === '';
	}

	public function comp(self $other)
	{
		if ($this->to_s() === $other->to_s()) {
			return 0;
		}
		if ($this->to_s() < $other->to_s()) {
			return -1;
		}
		return 1;
	}

	public function basename($suffix = NULL)
	{
		return new self(basename($this->to_s(), $suffix));
	}

	public function dirname()
	{
		return new self(dirname($this->to_s()));
	}

	public function instance_glob($pattern)
	{
		return array_map(function($path){return new self($path);}, glob($this->to_s().DIRECTORY_SEPARATOR.$pattern));
	}

	public function realpath()
	{
		$realpath = realpath($this->to_s());
		if ($realpath)
		{
			return new self($realpath);
		}
		return FALSE;
	}
}
