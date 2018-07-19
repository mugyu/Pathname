<?php
use PHPUnit\Framework\TestCase;
require_once __DIR__."/../lib/Pathname.php";

class PathnameTest extends TestCase
{
	/**
	 * @test
	 */
	public function test_version()
	{
		$this->assertRegExp('/\A\d\.\d\.\d/', Pathname::VERSION);
	}

	/**
	 * @test
	 * @group new
	 */
	public function test_getwd()
	{
		$pathname = Pathname::getwd();
		$this->assertSame(__BASE__, $pathname->to_s());
	}

	/**
	 * @test
	 * @group new
	 */
	public function test_pwd()
	{
		$pathname = Pathname::pwd();
		$this->assertSame(__BASE__, $pathname->to_s());
	}

	/**
	 * @test
	 * @group new
	 */
	public function test_parent_path()
	{
		$path = new Pathname('/foo/bar');
		$parent_path = $path->parent_path();
		$this->assertSame('/foo', $parent_path->to_s());
	}

	/**
	 * @test
	 * @group new
	 */
	public function test_static_glob()
	{
		$expected_list = glob("./tests/assets/*");
		$paths = Pathname::glob("./tests/assets/*");
		$this->assertContainsOnly('Pathname', $paths);

		$strings = array_map(function($path) {return (string)$path;}, $paths);
		foreach($expected_list as $expected)
		{
			$this->assertContains($expected, $strings);
		}
	}

	/**
	 * @test
	 * @group new
	 */
	public function test_instance_glob()
	{
		$expected_list = glob("./tests/assets/*.txt");
		$path = new Pathname("./tests/assets");
		$paths = $path->instance_glob("*.txt");
		$this->assertContainsOnly('Pathname', $paths);

		$strings = array_map(function($path) {return (string)$path;}, $paths);
		foreach($expected_list as $expected)
		{
			$this->assertContains($expected, $strings);
		}
	}

	/**
	 * @test
	 * @group new
	 */
	public function test_basename()
	{
		$path = new Pathname('./tests/assets/test.txt');
		$expected = new Pathname('test.txt');
		$this->assertInstanceOf('Pathname', $path->basename());
		$this->assertSame($expected->to_s(), $path->basename()->to_s());
	}

	/**
	 * @test
	 * @group new
	 */
	public function test_dirname()
	{
		$path = new Pathname('./tests/assets/test.txt');
		$expected = new Pathname('./tests/assets');
		$this->assertInstanceOf('Pathname', $path->dirname());
		$this->assertSame($expected->to_s(), $path->dirname()->to_s());
	}

	/**
	 * @test
	 * @group new
	 */
	public function test_realpath()
	{
		$path = new Pathname('./hoge/../piyo/../tests/fuga/../assets/./test.txt');
		$this->assertSame(__BASE__.'/tests/assets/test.txt', $path->realpath()->to_s());
	}

	/**
	 * @test
	 * @group comp
	 */
	public function test_equal_and_identity()
	{
		$path1 = new Pathname('/foo/bar');
		$path2 = new Pathname('/foo/bar');
		$path3 = new Pathname('/foo/bar/hoge');
		$path4 = $path1;

		// equal
		$this->assertTrue($path1 == $path2);
		$this->assertFalse($path1 == $path3);

		// identity
		$this->assertFalse($path1 === $path2);
		$this->assertTrue($path1 === $path4);
	}

	/**
	 * @test
	 * @group comp
	 */
	public function test_cmp()
	{
		$path1 = new Pathname('/foo/bar/1');
		$path2 = new Pathname('/foo/bar/2');
		$path3 = new Pathname('/foo/bar/2');
		$path4 = new Pathname('/foo/bar/21');
		$path5 = new Pathname('/foo/bar/3');

		$this->assertSame(-1, $path1->comp($path2));
		$this->assertSame( 0, $path2->comp($path3));
		$this->assertSame( 0, $path3->comp($path2));
		$this->assertSame( 1, $path5->comp($path3));
		$this->assertSame( 1, $path4->comp($path3));
		$this->assertSame(-1, $path4->comp($path5));
	}

	/**
	 * @test
	 * @group path
	 */
	public function test_toString()
	{
		$path = "/path/to/path";
		$pathname = new Pathname($path);
		$this->assertSame($path, '' . $pathname);
	}

	/**
	 * @test
	 * @group path
	 */
	public function test_to_s()
	{
		$path = "/path/to/path";
		$pathname = new Pathname($path);
		$this->assertSame($path, $pathname->to_s());
	}

	/**
	 * @test
	 * @group path
	 */
	public function to_path()
	{
		$path = "/path/to/path";
		$pathname = new Pathname($path);
		$this->assertSame($path, $pathname->to_path());
	}

	/**
	 * @test
	 * @group add
	 */
	public function test_add_some_directory()
	{
		$path1 = new Pathname('/usr');
		$path2 = new Pathname('bin/php');
		$path3 = $path1->add($path2);
		$this->assertSame('/usr/bin/php', $path3->to_s());
	}

	/**
	 * @test
	 * @group add
	 */
	public function test_add_root_directory()
	{
		$path1 = new Pathname('/usr');
		$path2 = new Pathname('/var/log/php');
		$path3 = $path1->add($path2);
		$this->assertSame('/var/log/php', $path3->to_s());
	}

	/**
	 * @test
	 * @group add
	 */
	public function test_add_parent_directory1()
	{
		$path1 = new Pathname('/foo/bar/baz/qux');
		$path2 = new Pathname('../../hoge/piyo');
		$path3 = $path1->add($path2);
		$this->assertSame('/foo/bar/hoge/piyo', $path3->to_s());
	}

	/**
	 * @test
	 * @group add
	 */
	public function test_add_parent_directory2()
	{
		$path1 = new Pathname('/foo/bar');
		$path2 = new Pathname('../../../hoge/piyo');
		$path3 = $path1->add($path2);
		$this->assertSame('/hoge/piyo', $path3->to_s());
	}

	/**
	 * @test
	 * @group add
	 */
	public function test_add_parent_directory3()
	{
		$path1 = new Pathname('foo/bar');
		$path2 = new Pathname('../../../hoge/piyo');
		$path3 = $path1->add($path2);
		$this->assertSame('../hoge/piyo', $path3->to_s());
	}

	/**
	 * @test
	 * @group add
	 */
	public function test_add_parent_directory4()
	{
		$path1 = new Pathname('foo/bar/');
		$path2 = new Pathname('./hoge/piyo/');
		$path3 = $path1->add($path2);
		$this->assertSame('foo/bar/hoge/piyo/', $path3->to_s());
	}

	/**
	 * @test
	 * @group pathState
	 */
	public function test_is_relative()
	{
		$path1 = new Pathname('/foo/bar');
		$this->assertFalse($path1->is_relative());
		$path2 = new Pathname('foo/bar');
		$this->assertTrue($path2->is_relative());
	}

	/**
	 * @test
	 * @group pathState
	 */
	public function test_is_absolute()
	{
		$path1 = new Pathname('/foo/bar');
		$this->assertTrue($path1->is_absolute());
		$path2 = new Pathname('foo/bar');
		$this->assertFalse($path2->is_absolute());
	}

	/**
	 * @test
	 * @group pathState
	 */
	public function test_is_root()
	{
		$path1 = new Pathname('/');
		$this->assertTrue($path1->is_root());
		$path2 = new Pathname('/foo');
		$this->assertFalse($path2->is_root());
	}

	/*****************************************
	 * ActionTrait
	 *****************************************/

	/**
	 * @test
	 */
	public function test_touch()
	{
		$test_path = './tests/assets/touch.txt';
		$path = new Pathname($test_path);
		@unlink($test_path);
		$this->assertFileNotExists($path->to_s());
		$path->touch();
		$this->assertFileExists($path->to_s());
		@unlink($test_path);
	}

	/**
	 * @test
	 * @group action
	 */
	public function test_delete()
	{
		$test_path = './tests/assets/delete.txt';
		$path = new Pathname($test_path);
		touch($test_path); 
		$this->assertFileExists($path->to_s());
		$path->delete();
		$this->assertFileNotExists($path->to_s());
	}

	/**
	 * @test
	 * @group action
	 */
	public function test_unlink()
	{
		$test_path = './tests/assets/delete.txt';
		$path = new Pathname($test_path);
		touch($test_path); 
		$this->assertFileExists($path->to_s());
		$path->unlink();
		$this->assertFileNotExists($path->to_s());
	}

	/**
	 * @test
	 * @group action
	 */
	public function test_mkdir()
	{
		$test_path = './tests/assets/directory';
		@rmdir($test_path); 
		$path = new Pathname($test_path);
		$this->assertDirectoryNotExists($path->to_s());
		$path->mkdir();
		$this->assertDirectoryExists($path->to_s());
	}

	/**
	 * @test
	 * @group action
	 */
	public function test_rmdir()
	{
		$test_path = './tests/assets/directory';
		@mkdir($test_path); 
		$path = new Pathname($test_path);
		$this->assertDirectoryExists($path->to_s());
		$path->rmdir();
		$this->assertDirectoryNotExists($path->to_s());
	}

	/*****************************************
	 * FileTrait
	 *****************************************/

	/**
	 * @test
	 * @group file
	 */
	public function test_read()
	{
		$result = '';
		$path = new Pathname("./tests/assets/test.txt");
		$path->read(function($value) use(&$result) {
			$result .= $value;
		});
		$this->assertSame("hello, world!\ngood bye!\n", $result);
	}

	/*****************************************
	 * OtherStateTrait
	 *****************************************/

	/**
	 * @test
	 * @group otherState
	 */
	public function test_exists()
	{
		$path1 = new Pathname('./tests');
		$path2 = new Pathname('./not_found');
		$this->assertTrue($path1->exists());
		$this->assertFileExists($path1->to_s());
		$this->assertFalse($path2->exists());
	}

	/**
	 * @test
	 * @group otherState
	 */
	public function test_state()
	{
		$path1 = new Pathname('./tests/assets');
		$path2 = new Pathname('./tests/assets/test.txt');
		$this->assertFalse($path1->is_file());
		$this->assertTrue($path2->is_file());
		$this->assertTrue($path1->is_dir());
		$this->assertFalse($path2->is_dir());
		$this->assertTrue($path1->is_readable());
	}

	public function test_is_link()
	{
		try
		{
			set_error_handler(function($severity, $message, $file, $line) {
				throw new ErrorException($message, 0, $severity, $file, $line);
			});
			if ( ! file_exists('./tests/assets/link_test.txt'))
			{
				symlink('./tests/assets/test.txt', './tests/assets/link_test.txt');
			}
		}
		catch (\ErrorException $e)
		{
			restore_error_handler();
			return;
		}
		finally
		{
			restore_error_handler();
		}

		$path1 = new Pathname('./tests/assets/test.txt');
		$path2 = new Pathname('./tests/assets/link_test.txt');
		$this->assertFalse($path1->is_link());
		$this->assertTrue($path2->is_link());
	}

	/*****************************************
	 * StateTrait
	 *****************************************/

	/**
	 * @test
	 * @group fileState
	 */
	public function test_size()
	{
		$test_path = './tests/assets/truncate.txt';
		$fh = fopen($test_path, "w");
		fwrite($fh, "12345678901234567890");
		fclose($fh);

		$path = new Pathname($test_path);
		$this->assertSame(20, $path->size());

		@unlink($test_path);
	}
}
