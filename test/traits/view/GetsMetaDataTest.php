<?php
namespace rakelley\jhframe\test\traits\view;

use \rakelley\jhframe\test\helpers\PHPUnitUtil as Utility;

/**
 * @coversDefaultClass \rakelley\jhframe\traits\view\GetsMetaData
 */
class GetsMetaDataTest extends
    \rakelley\jhframe\test\helpers\cases\Base
{
    protected $serviceMock;


    protected function setUp()
    {
        $serviceInterface = '\rakelley\jhframe\interfaces\repository\IMetaData';
        $locatorInterface =
            '\rakelley\jhframe\interfaces\services\IServiceLocator';
        $testedTrait = '\rakelley\jhframe\traits\view\GetsMetaData';

        $this->serviceMock = $this->getMock($serviceInterface);

        $locatorMock = $this->getMock($locatorInterface);
        $locatorMock->method('Make')
                    ->with($this->identicalTo($serviceInterface))
                    ->willReturn($this->serviceMock);

        $this->testObj = $this->getMockForTrait($testedTrait);
        $this->testObj->method('getLocator')
                      ->willReturn($locatorMock);
    }



    /**
     * Normal case, expected to set metaData property to fetched value
     * 
     * @covers ::fetchMetaData
     */
    public function testFetchMetaData()
    {
        $route = 'foobar';
        $data = ['foo' => 'bar', 'baz' => 'bat'];

        Utility::setProperties(['metaRoute' => $route], $this->testObj);

        $this->serviceMock->expects($this->once())
                          ->method('getPage')
                          ->with($this->identicalTo($route))
                          ->willReturn($data);

        $this->testObj->fetchMetaData();
        $this->assertEquals(
            $data,
            $this->testObj->metaData
        );
    }

    /**
     * Some metadata already exists, expected to merge fetched data
     * 
     * @depends testFetchMetaData
     * @covers ::fetchMetaData
     */
    public function testFetchMetaDataWithExisting()
    {
        $route = 'foobar';
        $data = ['foo' => 'bar', 'baz' => 'bat'];
        $existing = ['lorem' => 'ipsum'];
        $expected = array_merge($existing, $data);

        Utility::setProperties(['metaRoute' => $route], $this->testObj);
        $this->testObj->metaData = $existing;

        $this->serviceMock->expects($this->once())
                          ->method('getPage')
                          ->with($this->identicalTo($route))
                          ->willReturn($data);

        $this->testObj->fetchMetaData();
        $this->assertEquals(
            $expected,
            $this->testObj->metaData
        );
    }

    /**
     * Service returns no data, expected to set metaData to empty array
     * 
     * @covers ::fetchMetaData
     */
    public function testFetchMetaDataEmptyFetch()
    {
        $route = 'foobar';
        $data = null;
        $expected = [];

        Utility::setProperties(['metaRoute' => $route], $this->testObj);

        $this->serviceMock->expects($this->once())
                          ->method('getPage')
                          ->with($this->identicalTo($route))
                          ->willReturn($data);

        $this->testObj->fetchMetaData();
        $this->assertEquals(
            $expected,
            $this->testObj->metaData
        );
    }

    /**
     * Ensure empty fetch case is safe for existing data
     * 
     * @depends testFetchMetaDataEmptyFetch
     * @covers ::fetchMetaData
     */
    public function testFetchMetaDataEmptyFetchWithExisting()
    {
        $route = 'foobar';
        $data = null;
        $existing = ['lorem' => 'ipsum'];

        Utility::setProperties(['metaRoute' => $route], $this->testObj);
        $this->testObj->metaData = $existing;

        $this->serviceMock->expects($this->once())
                          ->method('getPage')
                          ->with($this->identicalTo($route))
                          ->willReturn($data);

        $this->testObj->fetchMetaData();
        $this->assertEquals(
            $existing,
            $this->testObj->metaData
        );
    }

    /**
     * No route set, expected to silently return
     * 
     * @covers ::fetchMetaData
     */
    public function testFetchMetaDataNoRoute()
    {
        $this->serviceMock->expects($this->never())
                          ->method('getPage');

        $this->testObj->fetchMetaData();
    }
}
