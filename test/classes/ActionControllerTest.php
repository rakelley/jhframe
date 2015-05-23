<?php
namespace rakelley\jhframe\test\classes;

use \rakelley\jhframe\test\helpers\PHPUnitUtil as Utility;

abstract class ActionControllerTestDummy implements
    \rakelley\jhframe\interfaces\ITakesParameters,
    \rakelley\jhframe\interfaces\action\IAction,
    \rakelley\jhframe\interfaces\action\IHasResult,
    \rakelley\jhframe\interfaces\action\IRequiresValidation
{

}

/**
 * @coversDefaultClass \rakelley\jhframe\classes\ActionController
 */
class ActionControllerTest extends \rakelley\jhframe\test\helpers\cases\Base
{
    protected $locatorMock;
    protected $cacheMock;
    protected $containerMock;
    protected $actionMock;
 

    protected function setUp()
    {
        $cacheInterface = '\rakelley\jhframe\interfaces\services\IKeyValCache';
        $containerClass = '\rakelley\jhframe\classes\resources\ActionResult';
        $locatorInterface =
            '\rakelley\jhframe\interfaces\services\IServiceLocator';
        $testedClass = '\rakelley\jhframe\classes\ActionController';

        $this->cacheMock = $this->getMock($cacheInterface);

        $this->containerMock = $this->getMockBuilder($containerClass)
                                    ->disableOriginalConstructor()
                                    ->getMock();
        $this->containerMock->method('getNewInstance')
                            ->will($this->returnSelf());

        $this->locatorMock = $this->getMock($locatorInterface);

        $mockedMethods = [
            'logException',//trait implemented
            'getLocator',//trait implemented
        ];
        $this->testObj = $this->getMockBuilder($testedClass)
                                 ->setConstructorArgs([$this->containerMock,
                                                       $this->cacheMock])
                                 ->setMethods($mockedMethods)
                                 ->getMock();
        $this->testObj->method('getLocator')
                      ->willReturn($this->locatorMock);
    }

    protected function setUpActionMock($withInterfaces=false)
    {
        $defaultClass = '\rakelley\jhframe\interfaces\action\IAction';
        $interfaceClass = __NAMESPACE__ . '\ActionControllerTestDummy';
        $class = ($withInterfaces) ? $interfaceClass : $defaultClass;

        $this->actionMock = $this->getMockForAbstractClass($class);

        $this->locatorMock->expects($this->once())
                          ->method('Make')
                          ->willReturn($this->actionMock);
    }


    public function errorCaseProvider()
    {
        return [
            [//error, fails
                'lorem ipsum', false
            ],
            [//no error, passes
                null, true
            ]
        ];
    }


    /**
     * @covers ::__construct
     */
    public function testConstruct()
    {
        $this->assertAttributeEquals($this->containerMock, 'container',
                                     $this->testObj);
        $this->assertAttributeEquals($this->cacheMock, 'cache',
                                     $this->testObj);
    }


    /**
     * Standard success case
     * 
     * @covers ::executeAction
     * @depends testConstruct
     */
    public function testExecuteAction()
    {
        $this->setUpActionMock();

        $actionName = 'any';
        $success = true;

        $this->actionMock->expects($this->once())
                         ->method('Proceed')
                         ->willReturn($success);
        $this->actionMock->expects($this->once())
                         ->method('touchesData')
                         ->willReturn(true);

        $this->cacheMock->expects($this->once())
                        ->method('Purge');

        $this->containerMock->expects($this->once())
                            ->method('setSuccess')
                            ->with($success)
                            ->will($this->returnSelf());

        $this->assertEquals(
            $this->containerMock,
            $this->testObj->executeAction($actionName)
        );
    }


    /**
     * Failure case
     * 
     * @covers ::executeAction
     * @depends testExecuteAction
     */
    public function testExecuteActionFailure()
    {
        $this->setUpActionMock();

        $actionName = 'any';
        $success = false;
        $error = 'an error';

        $this->actionMock->expects($this->once())
                         ->method('Proceed')
                         ->willReturn($success);
        $this->actionMock->expects($this->once())
                         ->method('getError')
                         ->willReturn($error);
        $this->actionMock->expects($this->never())
                         ->method('touchesData');

        $this->containerMock->expects($this->once())
                            ->method('setSuccess')
                            ->with($success)
                            ->will($this->returnSelf());
        $this->containerMock->expects($this->once())
                            ->method('setError')
                            ->with($error)
                            ->will($this->returnSelf());

        $this->assertEquals(
            $this->containerMock,
            $this->testObj->executeAction($actionName)
        );
    }


    /**
     * Expected to check for error to determine success when Proceed has no
     * return value
     * 
     * @covers ::executeAction
     * @depends testExecuteAction
     * @depends testExecuteActionFailure
     * @dataProvider errorCaseProvider
     */
    public function testExecuteActionNoProceedReturnValue($error, $success)
    {
        $this->setUpActionMock();

        $actionName = 'any';

        $this->actionMock->expects($this->once())
                         ->method('Proceed');
        $this->actionMock->expects($this->atLeastOnce())
                         ->method('getError')
                         ->willReturn($error);

        $this->containerMock->expects($this->once())
                            ->method('setSuccess')
                            ->with($success)
                            ->will($this->returnSelf());

        $this->assertEquals(
            $this->containerMock,
            $this->testObj->executeAction($actionName)
        );
    }


    /**
     * Success case with no cache purging
     * 
     * @covers ::executeAction
     * @depends testExecuteAction
     */
    public function testExecuteActionNoCache()
    {
        $this->setUpActionMock();

        $actionName = 'any';
        $success = true;

        $this->actionMock->expects($this->once())
                         ->method('Proceed');
        $this->actionMock->expects($this->once())
                         ->method('touchesData')
                         ->willReturn(false);

        $this->cacheMock->expects($this->never())
                        ->method('Purge');

        $this->containerMock->expects($this->once())
                            ->method('setSuccess')
                            ->with($success)
                            ->will($this->returnSelf());

        $this->assertEquals(
            $this->containerMock,
            $this->testObj->executeAction($actionName)
        );
    }


    /**
     * Success case with optional interfaces
     * 
     * @covers ::executeAction
     * @depends testExecuteAction
     */
    public function testExecuteActionWithInterfaces()
    {
        $this->setUpActionMock(true);

        $actionName = 'any';
        $parameters = ['foo' => 'bar'];
        $success = true;
        $result = 'foobar';

        $this->actionMock->expects($this->once())
                         ->method('setParameters')
                         ->with($this->identicalTo($parameters));
        $this->actionMock->expects($this->once())
                         ->method('Validate')
                         ->willReturn(true);
        $this->actionMock->expects($this->once())
                         ->method('Proceed');
        $this->actionMock->expects($this->once())
                         ->method('getResult')
                         ->willReturn($result);
        $this->actionMock->expects($this->once())
                         ->method('touchesData')
                         ->willReturn(false);

        $this->containerMock->expects($this->once())
                            ->method('setSuccess')
                            ->with($success)
                            ->will($this->returnSelf());
        $this->containerMock->expects($this->once())
                            ->method('setMessage')
                            ->with($result)
                            ->will($this->returnSelf());

        $this->assertEquals(
            $this->containerMock,
            $this->testObj->executeAction($actionName, $parameters)
        );
    }


    /**
     * InputException raised during validation, expected to set error to
     * exception message
     * 
     * @covers ::executeAction
     * @depends testExecuteActionWithInterfaces
     */
    public function testExecuteActionWithInterfacesInvalidInput()
    {
        $this->setUpActionMock(true);

        $actionName = 'any';
        $parameters = ['foo' => 'bar'];
        $success = false;
        $error = 'generic test exception';
        $e = new \rakelley\jhframe\classes\InputException($error);

        $this->actionMock->expects($this->once())
                         ->method('setParameters')
                         ->with($this->identicalTo($parameters));
        $this->actionMock->expects($this->once())
                         ->method('Validate')
                         ->will($this->throwException($e));
        $this->actionMock->expects($this->never())
                         ->method('Proceed');
        $this->actionMock->expects($this->never())
                         ->method('getResult');
        $this->actionMock->expects($this->never())
                         ->method('touchesData');

        $this->containerMock->expects($this->once())
                            ->method('setSuccess')
                            ->with($success)
                            ->will($this->returnSelf());
        $this->containerMock->expects($this->once())
                            ->method('setError')
                            ->with($error)
                            ->will($this->returnSelf());
        $this->containerMock->expects($this->once())
                            ->method('getError')
                            ->willReturn($error);

        $this->testObj->expects($this->once())
                         ->method('logException')
                         ->with($this->identicalTo($e));

        $this->assertEquals(
            $this->containerMock,
            $this->testObj->executeAction($actionName, $parameters)
        );
    }

    /**
     * Exception other than InputException raised during validation, expected
     * to provide generic error message
     * 
     * @covers ::executeAction
     * @depends testExecuteActionWithInterfacesInvalidInput
     */
    public function testExecuteActionWithInterfacesInvalidGeneric()
    {
        $this->setUpActionMock(true);

        $actionName = 'any';
        $parameters = ['foo' => 'bar'];
        $success = false;
        $error = 'generic test exception';
        $e = new \Exception($error);

        $this->actionMock->expects($this->once())
                         ->method('setParameters')
                         ->with($this->identicalTo($parameters));
        $this->actionMock->expects($this->once())
                         ->method('Validate')
                         ->will($this->throwException($e));
        $this->actionMock->expects($this->never())
                         ->method('Proceed');
        $this->actionMock->expects($this->never())
                         ->method('getResult');
        $this->actionMock->expects($this->never())
                         ->method('touchesData');

        $this->containerMock->expects($this->once())
                            ->method('setSuccess')
                            ->with($success)
                            ->will($this->returnSelf());
        $this->containerMock->expects($this->once())
                            ->method('setError')
                            ->with($this->logicalNot($this->identicalTo($error)))
                            ->will($this->returnSelf());
        $this->containerMock->expects($this->once())
                            ->method('getError')
                            ->willReturn('a string');

        $this->testObj->expects($this->once())
                         ->method('logException')
                         ->with($this->identicalTo($e));

        $this->assertEquals(
            $this->containerMock,
            $this->testObj->executeAction($actionName, $parameters)
        );
    }
}
