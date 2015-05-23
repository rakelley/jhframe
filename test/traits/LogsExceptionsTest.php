<?php
namespace rakelley\jhframe\test\traits;

use \rakelley\jhframe\test\helpers\PHPUnitUtil as Utility;

/**
 * @coversDefaultClass \rakelley\jhframe\traits\LogsExceptions
 */
class LogsExceptionsTest extends
    \rakelley\jhframe\test\helpers\cases\Base
{
    protected $loggerMock;
 

    protected function setUp()
    {
        $loggerInterface = '\rakelley\jhframe\interfaces\services\ILogger';
        $locatorInterface =
            '\rakelley\jhframe\interfaces\services\IServiceLocator';
        $testedTrait = '\rakelley\jhframe\traits\LogsExceptions';

        $this->loggerMock = $this->getMock($loggerInterface);

        $locatorMock = $this->getMock($locatorInterface);
        $locatorMock->method('Make')
                    ->with($this->identicalTo($loggerInterface))
                    ->willReturn($this->loggerMock);

        $this->testObj = $this->getMockForTrait($testedTrait);
        $this->testObj->method('getLocator')
                      ->willReturn($locatorMock);
    }


    /**
     * @covers ::logException
     */
    public function testLogException()
    {
        $exception = new \Exception('test exception');
        $dummyMessage = 'lorem ipsum';
        $level = 'example';

        $this->loggerMock->expects($this->once())
                         ->method('exceptionToMessage')
                         ->With($exception)
                         ->willReturn($dummyMessage);

        $this->loggerMock->expects($this->once())
                         ->method('Log')
                         ->With($this->identicalTo($level),
                                $this->identicalTo($dummyMessage));

        Utility::callMethod($this->testObj, 'logException', [$exception, $level]);
    }


    /**
     * @depends testLogException
     * @covers ::logException
     */
    public function testLogExceptionDefaultLevel()
    {
        $exception = new \Exception('test exception');
        $dummyMessage = 'lorem ipsum';

        $this->loggerMock->expects($this->once())
                         ->method('exceptionToMessage')
                         ->With($exception)
                         ->willReturn($dummyMessage);

        $this->loggerMock->expects($this->once())
                         ->method('Log')
                         ->With($this->isType('string'),
                                $this->identicalTo($dummyMessage));

        Utility::callMethod($this->testObj, 'logException', [$exception]);
    }
}
