<?php
namespace rakelley\jhframe\test\classes;

use \rakelley\jhframe\test\helpers\PHPUnitUtil as Utility;

/**
 * @coversDefaultClass \rakelley\jhframe\classes\DiskCache
 */
class DiskCacheTest extends \rakelley\jhframe\test\helpers\cases\Base
{
    protected $configMock;
    protected $fileSystemMock;
    protected $fileMock;


    protected function setUp()
    {
        $configInterface = '\rakelley\jhframe\interfaces\services\IConfig';
        $fileInterface = '\rakelley\jhframe\interfaces\IFile';
        $fileSystemInterface =
            '\rakelley\jhframe\interfaces\services\IFileSystemAbstractor';
        $testedClass = '\rakelley\jhframe\classes\DiskCache';

        $this->configMock = $this->getMock($configInterface);

        $this->fileMock = $this->getMock($fileInterface);

        $this->fileSystemMock = $this->getMock($fileSystemInterface);

        $mockedMethods = [
            'getConfig',//trait implemented
        ];
        $this->testObj = $this->getMockBuilder($testedClass)
                              ->disableOriginalConstructor()
                              ->setMethods($mockedMethods)
                              ->getMock();
        $this->testObj->method('getConfig')
                      ->willReturn($this->configMock);
    }

    /**
     * Set up config store and call test object constructor.  null is a valid
     * value for any arg so need to use array_key_exists instead of isset
     * 
     * @param array $args Args to pass to configMock
     */
    protected function setUpWithConfig(array $args=array())
    {
        $appName = (array_key_exists('appName', $args)) ? $args['appName'] :
                   'anystring';
        $dir = (array_key_exists('dir', $args)) ? $args['dir'] : null;
        $lifetime = (array_key_exists('lifetime', $args)) ? $args['lifetime'] :
                    null;

        $this->configMock->expects($this->at(0))
                         ->method('Get')
                         ->with($this->identicalTo('APP'),
                                $this->identicalTo('name'))
                         ->willReturn($appName);
        $this->configMock->expects($this->at(1))
                         ->method('Get')
                         ->with($this->identicalTo('ENV'),
                                $this->identicalTo('cache_dir'))
                         ->willReturn($dir);
        $this->configMock->expects($this->at(2))
                         ->method('Get')
                         ->with($this->identicalTo('ENV'),
                                $this->identicalTo('cache_lifetime'))
                         ->willReturn($lifetime);

        Utility::callConstructor($this->testObj, [$this->fileSystemMock]);
    }


    /**
     * @covers ::__construct
     */
    public function testConstruct()
    {
        $config = [
            'appName' => 'foobar',
            'dir' => '/foo/bar',
            'lifetime' => 1200,
        ];
        $this->setUpWithConfig($config);

        $this->assertAttributeEquals($this->fileSystemMock, 'fileSystem',
                                     $this->testObj);
        $this->assertAttributeEquals($config['appName'], 'appName',
                                     $this->testObj);
        $this->assertAttributeEquals($config['dir'], 'directory',
                                     $this->testObj);
        $this->assertAttributeEquals($config['lifetime'], 'lifetime',
                                     $this->testObj);
    }


    /**
     * Case when cache hits and lifetime is unset, expected to return decoded
     * content
     * 
     * @covers ::Read
     * @covers ::getFilePath
     * @covers ::filterKey
     * @covers ::decodeContent
     * @depends testConstruct
     * @dataProvider storedValueProvider
     */
    public function testReadHitNoExpiry($expected)
    {
        $args = [
            'dir' => '/foo/bar',
        ];
        $this->setUpWithConfig($args);

        $content = json_encode($expected);
        $key = 'any';

        $this->fileSystemMock->expects($this->once())
                             ->method('getFileWithPath')
                             ->with($this->stringContains($args['dir']))
                             ->willReturn($this->fileMock);

        $this->fileMock->expects($this->once())
                       ->method('Exists')
                       ->willReturn(true);
        $this->fileMock->expects($this->once())
                       ->method('getContent')
                       ->willReturn($content);

        $this->assertEquals(
            $expected,
            $this->testObj->Read($key)
        );
    }

    /**
     * Case when cache hits and not expired, expected to return decoded content
     * 
     * @covers ::Read
     * @depends testReadHitNoExpiry
     */
    public function testReadNotExpired()
    {
        $args = [
            'lifetime' => 1200,
        ];
        $this->setUpWithConfig($args);

        $expected = 'example string';
        $content = json_encode($expected);
        $key = 'any';
        $age = time() - 200;

        $this->fileSystemMock->method('getFileWithPath')
                             ->willReturn($this->fileMock);

        $this->fileMock->expects($this->once())
                       ->method('Exists')
                       ->willReturn(true);
        $this->fileMock->expects($this->once())
                       ->method('getAge')
                       ->willReturn($age);
        $this->fileMock->expects($this->once())
                       ->method('getContent')
                       ->willReturn($content);

        $this->assertEquals(
            $expected,
            $this->testObj->Read($key)
        );
    }

    /**
     * Case when cache hits but expired, expected to delete file and return
     * false
     * 
     * @covers ::Read
     * @depends testReadHitNoExpiry
     */
    public function testReadExpired()
    {
        $args = [
            'lifetime' => 120,
        ];
        $this->setUpWithConfig($args);

        $key = 'any';
        $age = time() - 200;

        $this->fileSystemMock->method('getFileWithPath')
                             ->willReturn($this->fileMock);

        $this->fileMock->expects($this->once())
                       ->method('Exists')
                       ->willReturn(true);
        $this->fileMock->expects($this->once())
                       ->method('getAge')
                       ->willReturn($age);
        $this->fileMock->expects($this->once())
                       ->method('Delete');
        $this->fileMock->expects($this->never())
                       ->method('getContent');

        $this->assertEquals(
            false,
            $this->testObj->Read($key)
        );
    }

    /**
     * Case when cache misses, expected to return false
     * 
     * @covers ::Read
     * @depends testReadHitNoExpiry
     */
    public function testReadMiss()
    {
        $this->setUpWithConfig();

        $key = 'any';

        $this->fileSystemMock->method('getFileWithPath')
                             ->willReturn($this->fileMock);

        $this->fileMock->expects($this->once())
                       ->method('Exists')
                       ->willReturn(false);
        $this->fileMock->expects($this->never())
                       ->method('getContent');

        $this->assertEquals(
            false,
            $this->testObj->Read($key)
        );
    }


    /**
     * @covers ::Write
     * @covers ::getFilePath
     * @covers ::filterKey
     * @covers ::encodeContent
     * @depends testConstruct
     * @dataProvider storedValueProvider
     */
    public function testWrite($content)
    {
        $args = [
            'dir' => '/foo/bar',
        ];
        $this->setUpWithConfig($args);

        $expected = json_encode($content);
        $key = 'any';

        $this->fileSystemMock->expects($this->once())
                             ->method('getFileWithPath')
                             ->with($this->stringContains($args['dir']))
                             ->willReturn($this->fileMock);

        $this->fileMock->expects($this->once())
                       ->method('setContent')
                       ->With($this->identicalTo($expected));

        $this->testObj->Write($content, $key);
    }


    /**
     * @covers ::Purge
     * @covers ::filterKey
     * @depends testConstruct
     */
    public function testPurgeAll()
    {
        $args = [
            'dir' => '/foo/bar',
        ];
        $this->setUpWithConfig($args);

        $expected = $args['dir'] . '*.json';
        $files = ['foo', 'bar', 'baz'];

        $this->fileSystemMock->expects($this->once())
                             ->method('Glob')
                             ->With($this->identicalTo($expected))
                             ->willReturn($files);
        $this->fileSystemMock->expects($this->once())
                             ->method('Delete')
                             ->with($this->identicalTo($files));

        $this->testObj->Purge();
    }

    /**
     * Case that Glob returns no matches, expected to do nothing
     * 
     * @covers ::Purge
     * @depends testPurgeAll
     */
    public function testPurgeNoResults()
    {
        $args = [
            'dir' => '/foo/bar',
        ];
        $this->setUpWithConfig($args);

        $expected = $args['dir'] . '*.json';

        $this->fileSystemMock->expects($this->once())
                             ->method('Glob')
                             ->With($this->identicalTo($expected))
                             ->willReturn([]);
        $this->fileSystemMock->expects($this->never())
                             ->method('Delete');

        $this->testObj->Purge();
    }

    /**
     * Case called with string arg
     * 
     * @covers ::Purge
     * @depends testPurgeAll
     */
    public function testPurgeOneFilter()
    {
        $args = [
            'dir' => '/foo/bar',
        ];
        $this->setUpWithConfig($args);

        $files = ['foo', 'bar', 'baz'];
        $filter = 'foobar';

        $this->fileSystemMock->expects($this->once())
                             ->method('Glob')
                             ->With($this->stringContains($args['dir']))
                             ->willReturn($files);
        $this->fileSystemMock->expects($this->once())
                             ->method('Delete')
                             ->with($this->identicalTo($files));

        $this->testObj->Purge($filter);
    }

    /**
     * Case called with array arg
     * 
     * @covers ::Purge
     * @depends testPurgeOneFilter
     */
    public function testPurgeMultipleFilters()
    {
        $args = [
            'dir' => '/foo/bar',
        ];
        $this->setUpWithConfig($args);

        $files = ['foo', 'bar', 'baz'];
        $filters = ['foobar', 'barfoo', 'barbaz'];

        $this->fileSystemMock->expects($this->exactly(count($filters)))
                             ->method('Glob')
                             ->With($this->stringContains($args['dir']))
                             ->willReturn($files);
        $this->fileSystemMock->expects($this->exactly(count($filters)))
                             ->method('Delete')
                             ->with($this->identicalTo($files));

        $this->testObj->Purge($filters);
    }


    public function storedValueProvider()
    {
        return [
            ['example string'],
            [123785],
            [['example', 'array']],
            [['complex' => ['ex', 'am', 'ple'], 'ar' => ['r','ay']]],
        ];
    }
}
