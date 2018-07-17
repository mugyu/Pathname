<?php
$base = preg_replace('/\/|\\\/', '/', preg_replace('/.tests$/', '', __DIR__));
define('__BASE__', $base);
