<?php
namespace rakelley\jhframe\test\traits\view;

use \rakelley\jhframe\test\helpers\PHPUnitUtil as Utility;

/**
 * @coversDefaultClass \rakelley\jhframe\traits\view\HasBotcheckField
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
        $testedTrait = '\rakelley\jhframe\traits\view\HasBotcheckField';

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
     * @covers ::addBotcheckField
     */
    public function testAddBotcheckField()
    {
        $content = 'lorem ipsum';

        $this->serviceMock->expects($this->once())
                          ->method('getField')
                          ->willReturn($content);

        $this->assertEquals($content,
                            Utility::callMethod($this->testObj,
                                                'addBotcheckField'));
    }
}
