<?php
use PHPUnit\Framework\TestCase;
require_once __DIR__."/../lib/File.php";

class FileTest extends TestCase
{
	/**
	 * @test
	 * @group pwd
	 */
	public function test_getcwd()
	{
		$this->assertSame(__BASE__, ds(\File::getcwd()));
	}

	/**
	 * @test
	 * @group pwd
	 */
	public function test_pwd()
	{
		$this->assertSame(__BASE__, ds(\File::pwd()));
	}

	/**
	 * @test
	 */
	public function test_open_without_callback()
	{
		$fh = \File::open("./tests/assets/test.txt", "r");
		$this->assertSame('stream', get_resource_type($fh));
		$this->assertSame('hello', fread($fh, 5));
		\File::close($fh);
	}

	/**
	 * @test
	 */
	public function test_open_with_callback()
	{
		$result = '';
		\File::open("./tests/assets/test.txt", "r", function($fh) use(&$result) {
			$result = fgets($fh);
		});
		$this->assertSame("hello, world!\n", $result);
	}

	/**
	 * @test
	 */
	public function test_read()
	{
		$result = '';
		\File::read("./tests/assets/test.txt", function($value) use(&$result) {
			$result .= $value;
		});
		$this->assertSame("hello, world!\ngood bye!\n", $result);
	}

	/**
	 * @test
	 */
	public function test_truncate()
	{
		$test_path = './tests/assets/truncate.txt';
		$fh = fopen($test_path, "w");
		fwrite($fh, "12345678901234567890");
		fclose($fh);

		$this->assertTrue(\File::truncate($test_path, 10));
		clearstatcache($test_path);
		$this->assertSame(10, filesize($test_path));

		$this->assertTrue(\File::truncate($test_path, 50));
		clearstatcache($test_path);
		$this->assertSame(50, filesize($test_path));

		@unlink($test_path);
	}
}
