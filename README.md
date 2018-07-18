# Pathname

## Usage

```php
$path1 = new Pathname('/foo');
echo $path1->to_s() . PHP_EOL; // => /foo

$path2 = $path1->add('bar');
echo $path2->to_s() . PHP_EOL; // => /foo/bar

$path3 = $path2->add('../baz');
echo $path3->to_s() . PHP_EOL; // => /foo/baz

$path4 = $path3->parent_path();
echo $path4->to_s() . PHP_EOL; // => /foo
```

## Static Methods

- getwd()
- pwd()
- glob($pattern)

## Instance Methods

- __construct($pathname)
- __toString()
- add($other)
- atime()
- basename($suffix = NULL)
- block_size()
- blocks()
- close($file_handle)
- comp(self $other)
- ctime()
- delete()
- device()
- dirname()
- exists()
- gid()
- inode()
- instance_glob($pattern)
- is_absolute()
- is_dir()
- is_file()
- is_link()
- is_readable()
- is_relative()
- is_root()
- is_writeable()
- mkdir($mode = 777)
- mode()
- mtime()
- nlink()
- open($mode, $callback = NULL)
- parent_path()
- rdev()
- read($callback)
- realpath()
- rmdir()
- size()
- to_path()
- to_s()
- touch($time = NULL)
- truncate($size)
- uid()
- unlink()
