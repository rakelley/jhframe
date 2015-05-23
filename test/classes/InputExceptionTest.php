<?php
namespace rakelley\jhframe\test\classes;

/**
 * @coversDefaultClass \rakelley\jhframe\classes\InputException
 */
class InputExceptionTest extends \PHPUnit_Framework_TestCase
{

    /**
     * @covers ::__construct
     */
    public function testConstruct()
    {
        $expectedDefaultCode = 400;
        $class = '\rakelley\jhframe\classes\InputException';

        $testObj = new $class('test message');
        $this->assertEquals($expectedDefaultCode, $testObj->getCode());
    }
}
