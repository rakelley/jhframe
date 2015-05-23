<?php
namespace rakelley\jhframe\test\classes;

use \rakelley\jhframe\test\helpers\PHPUnitUtil as Utility;

/**
 * @coversDefaultClass \rakelley\jhframe\classes\Action
 */
class ActionTest extends \rakelley\jhframe\test\helpers\cases\Base
{

    protected function setUp()
    {
        $testedClass = '\rakelley\jhframe\classes\Action';

        $this->testObj = $this->getMockForAbstractClass($testedClass);
    }


    /**
     * @covers ::getError
     */
    public function testGetError()
    {
        $error = 'generic test error';
        Utility::setProperties(['error' => $error], $this->testObj);

        $this->assertEquals($error, $this->testObj->getError());
    }


    /**
     * @covers ::touchesData
     */
    public function testTouchesData()
    {
        $default = true;
        $this->assertEquals($default, $this->testObj->touchesData());

        $value = false;
        Utility::setProperties(['touchesData' => $value], $this->testObj);
        $this->assertEquals($value, $this->testObj->touchesData());
    }
}
