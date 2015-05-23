<?php
namespace rakelley\jhframe\test\classes;

use \rakelley\jhframe\test\helpers\PHPUnitUtil as Utility;

/**
 * @coversDefaultClass \rakelley\jhframe\classes\Config
 */
class ConfigTest extends \rakelley\jhframe\test\helpers\cases\Base
{
    protected $testedClass = '\rakelley\jhframe\classes\Config';


    protected function setUpStored(array $stored)
    {
        Utility::setProperties(['config' => $stored], $this->testObj);
    }


    /**
     * @covers ::Reset
     */
    public function testReset()
    {
        $stored = ['foo' => ['bar' => 'baz', 'bat' => 'burzum']];

        $this->setUpStored($stored);

        $this->testObj->Reset();

        $this->assertAttributeEmpty('config', $this->testObj);
    }


    /**
     * @covers ::Get
     */
    public function testGetSingle()
    {
        $group = 'foo';
        $key = 'bar';
        $value = 'baz';
        $stored = [$group => [$key => $value]];

        $this->setUpStored($stored);

        $this->assertEquals($value, $this->testObj->Get($group, $key));
    }


    /**
     * @covers ::Get
     */
    public function testGetGroup()
    {
        $group = 'foo';
        $value = ['bar' => 'baz', 'bat' => 'burzum'];
        $stored = [$group => $value];

        $this->setUpStored($stored);

        $this->assertEquals($value, $this->testObj->Get($group));
    }


    /**
     * @covers ::Set
     * @depends testGetSingle
     */
    public function testSetSingle()
    {
        $group = 'foo';
        $key = 'bar';
        $value = 'baz';

        $this->testObj->Set($value, $group, $key);
        $this->assertEquals($value, $this->testObj->Get($group, $key));
    }


    /**
     * @covers ::Set
     * @depends testGetGroup
     */
    public function testSetGroup()
    {
        $group = 'foo';
        $value = ['bar' => 'baz', 'bat' => 'burzum'];
        $expected = [$group => $value];

        $this->testObj->Set($value, $group);
        $this->assertEquals($value, $this->testObj->Get($group));
    }


    /**
     * @covers ::Merge
     * @depends testGetGroup
     */
    public function testMerge()
    {
        $group = 'foo';
        $stored = [$group => ['bar' => 'baz', 'bat' => 'burzum']];
        $toMerge = ['bar' => 'booze', 'frumple' => 'lumpy'];
        $expected = array_merge($stored[$group], $toMerge);

        $this->setUpStored($stored);

        $this->testObj->Merge($toMerge, $group);
        $this->assertEquals($expected, $this->testObj->Get($group));
    }
}
