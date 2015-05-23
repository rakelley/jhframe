<?php
namespace rakelley\jhframe\test\traits;

use \rakelley\jhframe\test\helpers\PHPUnitUtil as Utility;

/**
 * @coversDefaultClass \rakelley\jhframe\traits\GetsServerProperty
 */
class GetsServerPropertyTest extends
    \rakelley\jhframe\test\helpers\cases\SimpleTrait
{
    protected $testedClass = '\rakelley\jhframe\traits\GetsServerProperty';


    /**
     * @covers ::getServerProp
     */
    public function testGetServerProp()
    {
        $_SERVER = ['foo' => 'bar', 'baz' => ''];

        $this->assertEquals(
            $_SERVER['foo'],
            Utility::callMethod($this->testObj, 'getServerProp', ['foo'])
        );
        $this->assertEquals(
            $_SERVER['baz'],
            Utility::callMethod($this->testObj, 'getServerProp', ['baz'])
        );
        $this->assertEquals(
            null,
            Utility::callMethod($this->testObj, 'getServerProp', ['other'])
        );
    }
}
