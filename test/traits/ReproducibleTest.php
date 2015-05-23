<?php
namespace rakelley\jhframe\test\traits;

/**
 * @coversDefaultClass \rakelley\jhframe\traits\Reproducible
 */
class ReproducibleTest extends
    \rakelley\jhframe\test\helpers\cases\Base
{
    protected $locatorMock;


    protected function setUp()
    {
        $locatorInterface =
            '\rakelley\jhframe\interfaces\services\IServiceLocator';
        $testedTrait = '\rakelley\jhframe\traits\Reproducible';

        $this->locatorMock = $this->getMock($locatorInterface);

        $this->testObj = $this->getMockForTrait($testedTrait);
        $this->testObj->method('getLocator')
                      ->willReturn($this->locatorMock);
    }


    /**
     * @covers ::getNewInstance
     */
    public function testGetNewInstance()
    {
        $this->locatorMock->expects($this->once())
                          ->method('getNew')
                          ->with($this->identicalTo($this->testObj))
                          ->will($this->returnArgument(0));

        $this->assertEquals($this->testObj, $this->testObj->getNewInstance());
    }
}
