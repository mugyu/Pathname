<?php
define('DS', DIRECTORY_SEPARATOR);
function ds($path)
{
	return preg_replace('/\/|\\\/', DIRECTORY_SEPARATOR, $path);
}
define('__BASE__', ds(preg_replace('/.tests$/', '', __DIR__)));
