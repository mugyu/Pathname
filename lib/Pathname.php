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
				self::$separator_pattern = '/\//';
			}
			else
			{
				self::$separator_pattern = '/\/|\\\/';
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
		$this->pathname = preg_replace(self::separator_pattern(), '/', $pathname);
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

	protected function plus($path1, $path2)
	{
		// TODO: 最適化は全くしていない
		$segments1 = explode('/', preg_replace('/([^\/])\/\z/', '\1', $path1));
		$segments2 = explode('/', $path2);
		foreach($segments2 as $index => $segment)
		{
			if ($segment === '..')
			{
				if (count($segments1) === 0) {
					$segments1[] = $segment;
				}
				elseif (count($segments1) !== 1 || $segments1[0] !== '') {
					array_pop($segments1);
				}
				continue;
			}
			elseif ($segment === '.') {
				continue;
			}
			$segments1[] = $segment;
		}
		return implode('/', $segments1);
	}

	public function is_root()
	{
		return $this->pathname === '/';
	}

	public function parent_path()
	{
		return $this->add('..');
	}

	public function is_absolute()
	{
		return strpos($this->pathname, '/') === 0;
	}

	public function is_relative()
	{
		return ! $this->is_absolute();
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
		return array_map(function($path){return new self($path);}, glob($this->to_s().'/'.$pattern));
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
