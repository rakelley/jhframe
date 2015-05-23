<?php
namespace rakelley\jhframe\test\traits;

use \rakelley\jhframe\test\helpers\PHPUnitUtil as Utility;

/**
 * @coversDefaultClass \rakelley\jhframe\traits\MetaProperties
 */
class MetaPropertiesTest extends
    \rakelley\jhframe\test\helpers\cases\Base
{

    protected function setUp()
    {
        $testedTrait = '\rakelley\jhframe\traits\MetaProperties';

        $this->testObj = $this->getMockBuilder($testedTrait)
                              ->setMethods(['getFoo', 'setFoo', 'unsetFoo'])
                              ->getMockForTrait();
    }


    protected function setUpProperties(array $properties)
    {
        Utility::setProperties(['properties' => $properties], $this->testObj);
    }


    /**
     * @covers ::__get
     */
    public function testGetStored()
    {
        $properties = ['foo' => 'bar'];
        $this->setUpProperties($properties);

        $this->assertEquals($properties['foo'], $this->testObj->foo);
    }

    /**
     * @covers ::__get
     * @depends testGetStored
     */
    public function testGetByMethod()
    {
        $fooValue = 'baz';

        $this->testObj->expects($this->once())
                      ->method('getFoo')
                      ->willReturn($fooValue);

        $this->assertEquals($fooValue, $this->testObj->foo);
        // second call to make sure repeated gets do not call getter method
        $this->assertEquals($fooValue, $this->testObj->foo);
    }

    /**
     * @covers ::__get
     * @depends testGetStored
     * @depends testGetByMethod
     */
    public function testGetFailure()
    {
        $this->setExpectedException('\BadMethodCallException');
        $bar = $this->testObj->notFoo;
    }


    /**
     * @covers ::__set
     * @covers ::resetProperties
     */
    public function testSet()
    {
        $this->setUpProperties(['baz' => 'bat']);

        $fooValue = 'bar';

        $this->testObj->expects($this->once())
                      ->method('setFoo')
                      ->with($this->identicalTo($fooValue));

        $this->testObj->foo = $fooValue;
        // ensure state has been reset
        $this->assertAttributeEmpty('properties', $this->testObj);
    }

    /**
     * @covers ::__set
     * @depends testSet
     */
    public function testSetFailure()
    {
        $this->setExpectedException('\BadMethodCallException');
        $this->testObj->notFoo = 'bar';
    }


    /**
     * @covers ::__isset
     * @depends testGetStored
     * @depends testGetByMethod
     */
    public function testIsset()
    {
        $this->setUpProperties(['foo' => 'bar']);
        $this->assertEquals(true, isset($this->testObj->foo));

        $this->setUpProperties([]);
        $this->testObj->method('getFoo')
                      ->willReturn(null);
        $this->assertEquals(false, isset($this->testObj->foo));
    }

    /**
     * @covers ::__isset
     * @depends testIsset
     * @depends testGetFailure
     */
    public function testIssetFailure()
    {
        $this->setExpectedException('\BadMethodCallException');
        $value = isset($this->testObj->notFoo);
    }


    /**
     * @covers ::__unset
     * @covers ::resetProperties
     */
    public function testUnset()
    {
        $this->setUpProperties(['foo' => 'bar']);

        $this->testObj->expects($this->once())
                      ->method('unsetFoo');

        unset($this->testObj->foo);
        // ensure state has been reset
        $this->assertAttributeEmpty('properties', $this->testObj);
    }

    /**
     * @covers ::__unset
     * @depends testUnset
     */
    public function testUnsetFailure()
    {
        $this->setExpectedException('\BadMethodCallException');
        unset($this->testObj->notFoo);
    }
}
