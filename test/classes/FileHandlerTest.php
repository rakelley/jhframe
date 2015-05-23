<?php
namespace rakelley\jhframe\test\classes;

use \rakelley\jhframe\test\helpers\PHPUnitUtil as Utility;

/**
 * @coversDefaultClass \rakelley\jhframe\classes\FileHandler
 */
class FileHandlerTest extends \rakelley\jhframe\test\helpers\cases\Base
{
    protected $configMock;
    protected $systemMock;


    protected function setUp()
    {
        $configInterface = '\rakelley\jhframe\interfaces\services\IConfig';
        $systemInterface =
            '\rakelley\jhframe\interfaces\services\IFileSystemAbstractor';
        $testedClass = '\rakelley\jhframe\classes\FileHandler';

        $this->configMock = $this->getMock($configInterface);

        $this->systemMock = $this->getMock($systemInterface);

        $mockedMethods = [
            'Validate',//abstract
            'Write',//abstract
            'getConfig',//trait implemented
        ];
        $this->testObj = $this->getMockBuilder($testedClass)
                              ->disableOriginalConstructor()
                              ->setMethods($mockedMethods)
                              ->getMock();
        $this->testObj->method('getConfig')
                      ->willReturn($this->configMock);
        Utility::setProperties(['fileSystem' => $this->systemMock],
                               $this->testObj);
    }

    protected function setUpWithConstructor()
    {
        Utility::setProperties(['fileSystem' => null, 'directory' => null],
                               $this->testObj);
        Utility::callConstructor($this->testObj, [$this->systemMock]);
    }


    /**
     * @covers ::__construct
     */
    public function testConstruct()
    {
        $publicDir = '/foo/bar';
        $this->configMock->method('Get')
                         ->with($this->identicalTo('ENV'),
                                $this->identicalTo('public_dir'))
                         ->willReturn($publicDir);

        $properties = ['relativePath' => '/baz/bat/'];
        Utility::setProperties($properties, $this->testObj);
        $this->setUpWithConstructor();

        $this->assertAttributeEquals($publicDir . $properties['relativePath'],
                                     'directory', $this->testObj);
        $this->assertAttributeEquals($this->systemMock, 'fileSystem',
                                     $this->testObj);
    }


    /**
     * @covers ::Read
     */
    public function testRead()
    {
        $properties = ['directory' => '/foo/bar/'];
        Utility::setProperties($properties, $this->testObj);
        $key = 'baz.txt';
        $path = $properties['directory'] . $key;

        $this->systemMock->expects($this->once())
                         ->method('Exists')
                         ->With($this->identicalTo($path))
                         ->willReturn(true);
        $this->assertEquals($path, $this->testObj->Read($key));
    }

    /**
     * @covers ::Read
     * @depends testRead
     */
    public function testReadFailure()
    {
        $properties = ['directory' => '/foo/bar/'];
        Utility::setProperties($properties, $this->testObj);
        $key = 'baz.txt';
        $path = $properties['directory'] . $key;

        $this->systemMock->expects($this->once())
                         ->method('Exists')
                         ->With($this->identicalTo($path))
                         ->willReturn(false);
        $this->assertEquals(null, $this->testObj->Read($key));
    }

    /**
     * @covers ::Read
     * @depends testRead
     */
    public function testReadNoExtension()
    {
        $properties = ['directory' => '/foo/bar/'];
        Utility::setProperties($properties, $this->testObj);
        $key = 'baz';
        $pattern = $properties['directory'] . $key . '.*';
        $results = ['baz.txt', 'baz.conf', 'baz.php'];

        $this->systemMock->expects($this->once())
                         ->method('Glob')
                         ->With($this->identicalTo($pattern))
                         ->willReturn($results);
        $this->assertEquals($results[0], $this->testObj->Read($key));
    }

    /**
     * @covers ::Read
     * @depends testReadNoExtension
     */
    public function testReadNoExtensionFailure()
    {
        $properties = ['directory' => '/foo/bar/'];
        Utility::setProperties($properties, $this->testObj);
        $key = 'baz';
        $pattern = $properties['directory'] . $key . '.*';
        $results = [];

        $this->systemMock->expects($this->once())
                         ->method('Glob')
                         ->With($this->identicalTo($pattern))
                         ->willReturn($results);
        $this->assertEquals(null, $this->testObj->Read($key));
    }


    /**
     * @covers ::Delete
     * @depends testRead
     */
    public function testDelete()
    {
        $properties = ['directory' => '/foo/bar/'];
        Utility::setProperties($properties, $this->testObj);
        $key = 'baz.txt';
        $path = $properties['directory'] . $key;

        $this->systemMock->expects($this->once())
                         ->method('Exists')
                         ->With($this->identicalTo($path))
                         ->willReturn(true);
        $this->systemMock->expects($this->once())
                         ->method('Delete')
                         ->With($this->identicalTo($path));

        $this->testObj->Delete($key);
    }

    /**
     * @covers ::Delete
     * @depends testDelete
     */
    public function testDeleteMissing()
    {
        $properties = ['directory' => '/foo/bar/'];
        Utility::setProperties($properties, $this->testObj);
        $key = 'baz.txt';
        $path = $properties['directory'] . $key;

        $this->systemMock->expects($this->once())
                         ->method('Exists')
                         ->With($this->identicalTo($path))
                         ->willReturn(false);
        $this->systemMock->expects($this->never())
                         ->method('Delete');

        $this->testObj->Delete($key);
    }


    /**
     * @covers ::makeRelative
     */
    public function testMakeRelative()
    {
        $properties = ['directory' => '/foo/bar/baz/', 'relativePath' => 'baz/'];
        Utility::setProperties($properties, $this->testObj);
        $path = '/foo/bar/baz/bat.txt';
        $expected = 'baz/bat.txt';

        $this->assertEquals($expected, $this->testObj->makeRelative($path));
    }
}
