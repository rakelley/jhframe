<?php
namespace rakelley\jhframe\test\traits\controller;

use \rakelley\jhframe\test\helpers\PHPUnitUtil as Utility;

/**
 * @coversDefaultClass \rakelley\jhframe\traits\controller\AcceptsArguments
 */
class AcceptsArgumentsTest extends
    \rakelley\jhframe\test\helpers\cases\Base
{
    protected $controllerMock;
    protected $resultMock;
    protected $arguments = ['foo' => 'bar', 'baz' => 'bat'];


    protected function setUp()
    {
        $controllerInterface =
            '\rakelley\jhframe\interfaces\services\IActionController';
        $resultClass = '\rakelley\jhframe\classes\resources\ActionResult';
        $testedTrait = '\rakelley\jhframe\traits\controller\AcceptsArguments';

        $this->resultMock = $this->getMockBuilder($resultClass)
                                 ->disableOriginalConstructor()
                                 ->getMock();

        $this->controllerMock = $this->getMock($controllerInterface);
        $this->controllerMock->expects($this->once())
                             ->method('executeAction')
                             ->With($this->isType('string'),
                                    $this->identicalTo($this->arguments))
                             ->willReturn($this->resultMock);

        $this->testObj = $this->getMockForTrait($testedTrait);
        $this->testObj->actionController = $this->controllerMock;
    }


    /**
     * Success case, expected to return result array
     * 
     * @covers ::getArguments
     */
    public function testGetArgumentsSuccess()
    {
        $expected = ['expected array'];

        $this->resultMock->expects($this->once())
                         ->method('getSuccess')
                         ->willReturn(true);
        $this->resultMock->expects($this->once())
                         ->method('getMessage')
                         ->willReturn($expected);

        $this->assertEquals(
            $expected,
            Utility::callMethod($this->testObj, 'getArguments',
                                [$this->arguments])
        );
    }

    /**
     * Failure case when must pass validation, expected to raise exception
     * 
     * @covers ::getArguments
     */
    public function testGetArgumentsFailureWithMustValidate()
    {
        $this->resultMock->expects($this->once())
                         ->method('getSuccess')
                         ->willReturn(false);
        $this->resultMock->expects($this->once())
                         ->method('getError')
                         ->willReturn('test error string');

        $this->setExpectedException('\UnexpectedValueException');
        Utility::callMethod($this->testObj, 'getArguments',
                            [$this->arguments]);
    }

    /**
     * Case when passing validation is optional, expected to return result array
     * regardless of success
     * 
     * @covers ::getArguments
     */
    public function testGetsArgumentsWithoutMustValidate()
    {
        $expected = ['expected array'];

        $this->resultMock->expects($this->never())
                         ->method('getSuccess');
        $this->resultMock->expects($this->once())
                         ->method('getMessage')
                         ->willReturn($expected);

        $this->assertEquals(
            $expected,
            Utility::callMethod($this->testObj, 'getArguments',
                                [$this->arguments, false])
        );
    }
}
