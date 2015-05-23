<?php
namespace rakelley\jhframe\test\classes\resources;

use \org\bovigo\vfs\vfsStream;

/**
 * @coversDefaultClass \rakelley\jhframe\classes\resources\File
 */
class FileTest extends \rakelley\jhframe\test\helpers\cases\Base
{
    protected $rootDir;


    protected function setUp()
    {
        $testedClass = '\rakelley\jhframe\classes\resources\File';

        $mockedMethods = [
            'getLocator',//trait implemented
            'getNewInstance',//trait implemented
            'getMimeType',//trait implemented
        ];
        $this->testObj = $this->getMockBuilder($testedClass)
                              ->setMethods($mockedMethods)
                              ->getMock();

        vfsStream::setup('testDir');
        $this->rootDir = vfsStream::url('testDir') . '/';
    }


    /**
     * @covers ::setPath
     */
    public function testSetPath()
    {
        $path = $this->rootDir . 'foo.txt';

        $this->assertEquals($this->testObj, $this->testObj->setPath($path));
        
        $this->assertAttributeEquals($path, 'path', $this->testObj);
    }


    /**
     * @covers ::Exists
     * @depends testSetPath
     */
    public function testExists()
    {
        $path = $this->rootDir . 'foo.txt';
        touch($path);

        $this->testObj->setPath($path);

        $this->assertTrue($this->testObj->Exists());
    }

    /**
     * @covers ::Exists
     * @depends testExists
     */
    public function testExistsFalse()
    {
        $path = $this->rootDir . 'foo.txt';

        $this->testObj->setPath($path);

        $this->assertFalse($this->testObj->Exists());
    }


    /**
     * @covers ::setContent
     * @depends testExists
     */
    public function testSetContent()
    {
        $content = 'foo bar baz bat';
        $path = $this->rootDir . 'foo.txt';

        $this->testObj->setPath($path);

        $this->testObj->setContent($content);
        $this->assertEquals($content, file_get_contents($path));
    }


    /**
     * @covers ::getContent
     * @covers ::<protected>
     * @depends testSetContent
     */
    public function testGetContent()
    {
        $content = 'foo bar baz bat';
        $path = $this->rootDir . 'foo.txt';

        $this->testObj->setPath($path);
        $this->testObj->setContent($content);

        $this->assertEquals(
            $this->testObj->getContent(),
            file_get_contents($path)
        );
    }

    /**
     * @covers ::getContent
     * @covers ::<protected>
     * @depends testGetContent
     */
    public function testGetContentNotExists()
    {
        $path = $this->rootDir . 'foo.txt';

        $this->testObj->setPath($path);

        $this->setExpectedException('\DomainException');
        $this->testObj->getContent();
    }


    /**
     * @covers ::Append
     * @depends testSetContent
     */
    public function testAppend()
    {
        $content = "foo bar baz bat\n";
        $toAppend = "lorem ipsum dolor\n";
        $expected = $content . $toAppend;
        $path = $this->rootDir . 'foo.txt';

        $this->testObj->setPath($path);
        $this->testObj->setContent($content);

        $this->testObj->Append($toAppend);
        $this->assertEquals($expected, file_get_contents($path));
    }


    /**
     * @covers ::Delete
     * @depends testExists
     */
    public function testDelete()
    {
        $path = $this->rootDir . 'foo.txt';
        touch($path);

        $this->testObj->setPath($path);

        $this->testObj->Delete();
        $this->assertFalse($this->testObj->Exists());
    }

    /**
     * Should not raise exception or error if file doesn't exist
     * 
     * @covers ::Delete
     * @depends testDelete
     */
    public function testDeleteNotExists()
    {
        $path = $this->rootDir . 'foo.txt';

        $this->testObj->setPath($path);

        $this->testObj->Delete();
    }


    /**
     * @covers ::getAge
     * @covers ::<protected>
     * @depends testSetPath
     */
    public function testGetAge()
    {
        $path = $this->rootDir . 'foo.txt';
        touch($path);

        $this->testObj->setPath($path);

        $age = $this->testObj->getAge();
        $this->assertTrue(is_int($age));
    }

    /**
     * @covers ::getAge
     * @covers ::<protected>
     * @depends testGetAge
     */
    public function testGetAgeNotExists()
    {
        $path = $this->rootDir . 'foo.txt';

        $this->testObj->setPath($path);

        $this->setExpectedException('\DomainException');
        $this->testObj->getAge();
    }


    /**
     * @covers ::getMedia
     * @covers ::<protected>
     * @depends testSetPath
     */
    public function testgetMedia()
    {
        $path = $this->rootDir . 'foo.txt';
        touch($path);
        $type = 'text/plain';

        $this->testObj->setPath($path);

        $this->testObj->expects($this->once())
                      ->method('getMimeType')
                      ->with($this->identicalTo($path))
                      ->willReturn($type);

        $this->assertEquals($type, $this->testObj->getMedia());
    }


    /**
     * @covers ::getSize
     * @covers ::<protected>
     * @depends testSetPath
     */
    public function testGetSize()
    {
        $path = $this->rootDir . 'foo.txt';
        touch($path);

        $this->testObj->setPath($path);

        $size = $this->testObj->getSize();
        $this->assertTrue(is_int($size));
    }

    /**
     * @covers ::getSize
     * @covers ::<protected>
     * @depends testGetSize
     */
    public function testGetSizeNotExists()
    {
        $path = $this->rootDir . 'foo.txt';

        $this->testObj->setPath($path);

        $this->setExpectedException('\DomainException');
        $this->testObj->getSize();
    }
}
