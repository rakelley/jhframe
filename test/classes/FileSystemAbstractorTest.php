<?php
namespace rakelley\jhframe\test\classes;

use \org\bovigo\vfs\vfsStream;
use \rakelley\jhframe\test\helpers\PHPUnitUtil as Utility;

/**
 * @coversDefaultClass \rakelley\jhframe\classes\FileSystemAbstractor
 */
class FileSystemAbstractorTest extends \rakelley\jhframe\test\helpers\cases\Base
{
    protected $curlMock;
    protected $fileMock;
    protected $locatorMock;
    protected $rootDir;


    protected function setUp()
    {
        $curlClass = '\rakelley\jhframe\classes\CurlAbstractor';
        $fileInterface = '\rakelley\jhframe\interfaces\IFile';
        $locatorInterface =
            '\rakelley\jhframe\interfaces\services\IServiceLocator';
        $testedClass = '\rakelley\jhframe\classes\FileSystemAbstractor';

        $this->curlMock = $this->getMockBuilder($curlClass)
                               ->disableOriginalConstructor()
                               ->getMock();

        $this->fileMock = $this->getMock($fileInterface);
        $this->fileMock->method('getNewInstance')
                       ->will($this->returnSelf());

        $this->locatorMock = $this->getMock($locatorInterface);

        $mockedMethods = [
            'getLocator',//trait implemented
            'writeUploaded',//wrapper for move_uploaded_file()
        ];
        $this->testObj = $this->getMockBuilder($testedClass)
                              ->setConstructorArgs([$this->curlMock,
                                                    $this->fileMock])
                              ->setMethods($mockedMethods)
                              ->getMock();
        $this->testObj->method('getLocator')
                      ->willReturn($this->locatorMock);

        vfsStream::setup('testDir');
        $this->rootDir = vfsStream::url('testDir') . '/';
    }


    /**
     * @covers ::__construct
     */
    public function testConstruct()
    {
        $this->assertAttributeEquals($this->curlMock, 'curl', $this->testObj);
    }


    /**
     * @covers ::Exists
     */
    public function testExists()
    {
        $filePath = $this->rootDir . 'foo.txt';
        $dirPath = $this->rootDir . 'bar';
        $falseFilePath = $this->rootDir . 'baz.txt';
        $falseDirPath = $this->rootDir . 'bat';

        touch($filePath);
        mkdir($dirPath);

        $this->assertTrue($this->testObj->Exists($filePath));
        $this->assertTrue($this->testObj->Exists($dirPath));
        $this->assertFalse($this->testObj->Exists($falseFilePath));
        $this->assertFalse($this->testObj->Exists($falseDirPath));
    }


    /**
     * @covers ::createDirectory
     */
    public function testCreateDirectory()
    {
        $path = $this->rootDir . 'bar';

        $this->assertFalse(is_dir($path));
        $this->testObj->createDirectory($path);
        $this->assertTrue(is_dir($path));
    }


    /**
     * @covers ::getFileWithPath
     * @depends testConstruct
     */
    public function testGetFileWithPath()
    {
        $path = $this->rootDir . 'foo.txt';

        $this->fileMock->expects($this->once())
                       ->method('setPath')
                       ->with($this->identicalTo($path))
                       ->will($this->returnSelf());

        $this->assertEquals($this->fileMock,
                            $this->testObj->getFileWithPath($path));
    }


    /**
     * @covers ::createFile
     * @depends testGetFileWithPath
     * @depends testConstruct
     */
    public function testCreateFile()
    {
        $path = $this->rootDir . 'foo.txt';

        $this->fileMock->expects($this->once())
                       ->method('setPath')
                       ->with($this->identicalTo($path))
                       ->will($this->returnSelf());

        $this->assertEquals($this->fileMock,
                            $this->testObj->createFile($path));
        $this->assertTrue(file_exists($path));
    }


    /**
     * @covers ::containeredInclude
     */
    public function testContaineredInclude()
    {
        $content = <<<'PHP'
<?php
    echo 'foobar' . $parameters['baz'];

PHP;
        $parameters = ['baz' => 'bat'];
        $expected = 'foobarbat';
        $path = $this->rootDir . 'Foo.php';
        file_put_contents($path, $content);

        $this->assertEquals($expected,
                            $this->testObj->containeredInclude($path,
                                                               $parameters));
    }


    /**
     * @covers ::unsafeInclude
     */
    public function testUnsafeInclude()
    {
        $content = <<<'PHP'
<?php
    echo 'foobar' . $parameters['baz'];

PHP;
        $parameters = ['baz' => 'bat'];
        $expected = 'foobarbat';
        $path = $this->rootDir . 'Foo.php';
        file_put_contents($path, $content);

        ob_start();
        $this->testObj->unsafeInclude($path, $parameters);
        $content = ob_get_contents();
        ob_end_clean();

        $this->assertEquals($expected, $content);
    }


    /**
     * @covers ::getRemoteFile
     * @depends testConstruct
     */
    public function testGetRemoteFile()
    {
        $uri = 'http://example.com/foo.txt';
        $contents = 'lorem ipsum';

        $this->curlMock->expects($this->once())
                       ->method('newRequest')
                       ->with($this->identicalTo($uri))
                       ->willReturn($this->curlMock);
        $this->curlMock->expects($this->once())
                       ->method('setReturn')
                       ->with($this->identicalTo(true))
                       ->willReturn($this->curlMock);
        $this->curlMock->expects($this->once())
                       ->method('Execute')
                       ->willReturn($contents);
        $this->curlMock->expects($this->once())
                       ->method('Close');

        $this->assertEquals($contents, $this->testObj->getRemoteFile($uri));
    }


    /**
     * @covers ::Glob
     */
    public function testGlob()
    {
        $dir = $this->rootDir . 'globTest';
        mkdir($dir);
        $files = [$dir . '/foo.txt', $dir . '/bar.txt', $dir . '/baz.txt'];
        array_map('touch', $files);
        $pattern = $dir . '/*';

        $this->assertEquals(
            glob($pattern),
            $this->testObj->Glob($pattern)
        );
    }


    /**
     * @covers ::Delete
     * @covers ::<protected>
     * @depends testExists
     */
    public function testDeleteFileSimple()
    {
        $path = $this->rootDir . 'foo.txt';
        touch($path);

        $this->testObj->Delete($path);
        $this->assertFalse(file_exists($path));
    }

    /**
     * @covers ::Delete
     * @covers ::<protected>
     * @depends testDeleteFileSimple
     */
    public function testDeleteFileArray()
    {
        $paths = [
            $this->rootDir . 'fooA.txt',
            $this->rootDir . 'fooB.txt',
            $this->rootDir . 'fooC.txt',
        ];
        array_map('touch', $paths);

        $this->testObj->Delete($paths);
        array_map(
            function($path) {
                $this->assertFalse(file_exists($path));
            },
            $paths
        );
    }

    /**
     * @covers ::Delete
     * @covers ::<protected>
     * @depends testDeleteFileSimple
     */
    public function testDeleteDirSimple()
    {
        $path = $this->rootDir . 'bar';
        mkdir($path);

        $this->testObj->Delete($path);
        $this->assertFalse(is_dir($path));
    }

    /**
     * @covers ::Delete
     * @covers ::<protected>
     * @depends testDeleteDirSimple
     */
    public function testDeleteDirMultiple()
    {
        $dirOne = $this->rootDir . 'foo';
        $dirTwo = $this->rootDir . 'bar';
        $dirThree = $this->rootDir . 'baz';
        $dirs = [$dirOne, $dirTwo, $dirThree];
        array_map('mkdir', $dirs);

        $this->testObj->Delete($dirs);
        array_walk(
            $dirs,
            function($path) {
                $this->assertFalse(is_dir($path));
            }
        );
    }

    /**
     * @covers ::Delete
     * @covers ::<protected>
     * @depends testDeleteDirSimple
     */
    public function testDeleteDirRecursive()
    {
        $levelOne = $this->rootDir . 'foo';
        $levelTwo = $levelOne . '/bar';
        $levelThree = $levelTwo . '/baz';
        $dirs = [$levelOne, $levelTwo, $levelThree];
        array_map('mkdir', $dirs);
        $files = [
            $levelOne . '/foo1.txt',
            $levelTwo . '/foo2.txt',
            $levelThree . '/foo3.txt',
        ];
        array_map('touch', $files);

        $this->testObj->Delete($levelOne, true);
        array_walk(
            $dirs,
            function($path) {
                $this->assertFalse(is_dir($path));
            }
        );
        array_walk(
            $files,
            function($path) {
                $this->assertFalse(file_exists($path));
            }
        );
    }

    /**
     * @covers ::Delete
     * @covers ::<protected>
     * @depends testDeleteDirRecursive
     */
    public function testDeleteDirMultipleRecursive()
    {
        $levelOne = $this->rootDir . 'foo';
        $levelTwo = $this->rootDir . 'bar';
        $levelOneB = $levelOne . '/baz';
        $levelTwoB = $levelTwo . '/baz';
        $dirs = [$levelOne, $levelTwo, $levelOneB, $levelTwoB];
        array_map('mkdir', $dirs);

        $this->testObj->Delete($dirs, true);
        array_walk(
            $dirs,
            function($path) {
                $this->assertFalse(is_dir($path));
            }
        );
    }

    /**
     * Expected to throw exception if trying to delete directory with children
     * with recursion arg false
     * 
     * @covers ::Delete
     * @covers ::<protected>
     * @depends testDeleteDirRecursive
     */
    public function testDeleteDirFailOnChildren()
    {
        $levelOne = $this->rootDir . 'foo';
        $levelTwo = $levelOne . '/bar';
        $levelThree = $levelTwo . '/baz';
        $dirs = [$levelOne, $levelTwo, $levelThree];
        array_map('mkdir', $dirs);
        $files = [
            $levelOne . '/foo1.txt',
            $levelTwo . '/foo2.txt',
            $levelThree . '/foo3.txt',
        ];
        array_map('touch', $files);

        $this->setExpectedException('\DomainException');
        $this->testObj->Delete($levelOne);
    }

    /**
     * Expected not to raise error or exception if path doesn't exist
     * 
     * @covers ::Delete
     * @covers ::<protected>
     * @depends testDeleteFileSimple
     * @depends testDeleteDirSimple
     */
    public function testDeleteNotExists()
    {
        $filePath = $this->rootDir . 'foo.txt';
        $dirPath = $this->rootDir . 'bar';

        $this->testObj->Delete($filePath);
        $this->testObj->Delete($dirPath);
    }
}
