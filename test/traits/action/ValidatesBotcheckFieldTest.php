<?php
namespace rakelley\jhframe\test\traits\action;

use \rakelley\jhframe\test\helpers\PHPUnitUtil as Utility;

/**
 * @coversDefaultClass \rakelley\jhframe\traits\action\ValidatesBotcheckField
 */
class ValidatesBotcheckFieldTest extends
    \rakelley\jhframe\test\helpers\cases\Base
{
    protected $serviceMock;


    protected function setUp()
    {
        $locatorInterface =
            '\rakelley\jhframe\interfaces\services\IServiceLocator';
        $serviceInterface = '\rakelley\jhframe\interfaces\services\IBotcheck';
        $testedTrait = '\rakelley\jhframe\traits\action\ValidatesBotcheckField';

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
     * @covers ::validateBotCheckField
     */
    public function testValidateBotCheckField()
    {
        $this->serviceMock->expects($this->once())
                          ->method('validateField');

        Utility::callMethod($this->testObj, 'validateBotcheckField');
    }
}
