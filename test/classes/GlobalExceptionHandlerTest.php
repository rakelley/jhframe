<?php
namespace rakelley\jhframe\test\classes;

use \rakelley\jhframe\test\helpers\PHPUnitUtil as Utility;

/**
 * @coversDefaultClass \rakelley\jhframe\classes\GlobalExceptionHandler
 */
class GlobalExceptionHandlerTest extends
    \rakelley\jhframe\test\helpers\cases\Base
{
    protected $handlerMock;
    protected $ioMock;
    

    protected function setUp()
    {
        $handlerInterface =
            '\rakelley\jhframe\interfaces\services\IExceptionHandler';
        $ioInterface = '\rakelley\jhframe\interfaces\services\IIo';
        $testedClass = '\rakelley\jhframe\classes\GlobalExceptionHandler';

        $this->handlerMock = $this->getMock($handlerInterface);

        $this->ioMock = $this->getMock($ioInterface);

        $this->testObj = new $testedClass($this->handlerMock, $this->ioMock);
    }


    /**
     * @covers ::__construct
     */
    public function testConstruct()
    {
        $this->assertAttributeEquals($this->handlerMock, 'handler',
                                     $this->testObj);
    }


    /**
     * @covers ::Initiate
     * @depends testConstruct
     */
    public function testInitiate()
    {
        $e = new \Exception('test message');

        $this->handlerMock->expects($this->once())
                          ->method('Handle')
                          ->with($this->identicalTo($e));

        $this->testObj->Initiate($e);
    }


    /**
     * Case when handler service raises another exception, should fall back to
     * error log and exit
     * 
     * @covers ::Initiate
     * @covers ::handlerFailure
     * @depends testInitiate
     */
    public function testInitiateExceptionRaised()
    {
        $firstMessage = 'test message';
        $secondMessage = 'second message';
        $e = new \Exception($firstMessage);
        $second = new \Exception($secondMessage);

        $this->handlerMock->expects($this->once())
                          ->method('Handle')
                          ->with($this->identicalTo($e))
                          ->will($this->throwException($second));

        $this->ioMock->expects($this->once())
                     ->method('toErrorLog')
                     ->With($this->logicalAnd(
                                $this->stringContains($firstMessage),
                                $this->stringContains($secondMessage)
                            ));
        $this->ioMock->expects($this->once())
                     ->method('toExit')
                     ->With($this->logicalAnd(
                                $this->stringContains($firstMessage),
                                $this->stringContains($secondMessage)
                            ));

        $this->testObj->Initiate($e);
    }


    /**
     * Case when handler is not set, should not be possible but exceptions
     * must not ever leak from Initiate so should be covered anyway
     * 
     * @covers ::Initiate
     * @covers ::handlerFailure
     * @depends testInitiateExceptionRaised
     */
    public function testInitiateNoHandler()
    {
        Utility::setProperties(['handler' => null], $this->testObj);

        $e = new \Exception('test message');

        $this->handlerMock->expects($this->never())
                          ->method('Handle');

        $this->ioMock->expects($this->once())
                     ->method('toErrorLog')
                     ->With($this->isType('string'));
        $this->ioMock->expects($this->once())
                     ->method('toExit')
                     ->With($this->isType('string'));

        $this->testObj->Initiate($e);
    }


    /**
     * @runInSeparateProcess
     * @covers ::registerSelf
     * @depends testConstruct
     */
    public function testRegisterSelf()
    {
        $this->testObj->registerSelf();

        $this->assertEquals([$this->testObj, 'Initiate'],
                            set_exception_handler(null));
    }
}
