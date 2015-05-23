<?php
namespace rakelley\jhframe\test\classes;

use \rakelley\jhframe\test\helpers\PHPUnitUtil as Utility;

/**
 * App has to build own dependencies with new since in a catch-22 with 
 * ServiceLocator, so we test constructor seperately and side-load mocks for all
 * other tests
 * 
 * @coversDefaultClass \rakelley\jhframe\classes\App
 */
class AppTest extends \rakelley\jhframe\test\helpers\cases\Base
{
    protected $testedClass = '\rakelley\jhframe\classes\App';
    protected $configMock;
    protected $locatorMock;


    protected function setUp()
    {
        $class = $this->testedClass;

        $configInterface = $class::INTERFACE_CONFIG;
        $this->configMock = $this->getMock($configInterface);

        $locatorInterface = $class::INTERFACE_LOCATOR;
        $this->locatorMock = $this->getMock($locatorInterface);

        $mockedMethods = [
            'getServerProp',//trait implemented
        ];
        $this->testObj = $this->getMockBuilder($this->testedClass)
                              ->disableOriginalConstructor()
                              ->setMethods($mockedMethods)
                              ->getMock();
        $props = [
            'config' => $this->configMock,
            'locator' => $this->locatorMock,
            'instance' => $this->testObj,
        ];
        Utility::setProperties($props, $this->testObj);
    }

    /**
     * Prepare mocks and expectations for tests covering ::passToRouter
     */
    protected function setUpServicesForMain()
    {
        $services = $this->readAttribute($this->testObj, 'services');

        $sessionMock = $this->getMock('\\' . $services['session']);
        $sessionMock->expects($this->once())
                    ->method('startSession');

        $routerMock = $this->getMock('\\' . $services['router']);
        $routerMock->expects($this->once())
                   ->method('serveRequest');

        $this->locatorMock->expects($this->at(0))
                          ->method('Make')
                          ->With($this->identicalTo($services['session']))
                          ->willReturn($sessionMock);
        $this->locatorMock->expects($this->at(1))
                          ->method('Make')
                          ->With($this->identicalTo($services['router']))
                          ->willReturn($routerMock);
    }


    /**
     * @covers ::getConfig
     */
    public function testGetConfig()
    {
        $this->assertEquals(
            $this->readAttribute($this->testObj, 'config'),
            $this->testObj->getConfig()
        );
    }


    /**
     * @covers ::getLocator
     */
    public function testGetLocator()
    {
        $this->assertEquals(
            $this->readAttribute($this->testObj, 'locator'),
            $this->testObj->getLocator()
        );
    }


    /**
     * @covers ::__construct
     * @covers ::registerAlias
     * @depends testGetConfig
     * @depends testGetLocator
     */
    public function testConstructWithArgs()
    {
        $configMockClass = get_class($this->configMock);
        $locatorMockClass = get_class($this->locatorMock);
        $class = $this->testedClass;

        $customtestObj = new $class($configMockClass, $locatorMockClass);
        $this->assertTrue(
            $this->readAttribute($customtestObj, 'instance') instanceof $class
        );
        $this->assertTrue(
            $customtestObj->getConfig() instanceof $configMockClass
        );
        $this->assertEquals($customtestObj->getConfig(),
                            \App::getConfig());
        $this->assertTrue(
            $customtestObj->getLocator() instanceof $locatorMockClass
        );
        $this->assertEquals($customtestObj->getLocator(),
                            \App::getLocator());
    }

    /**
     * @covers ::__construct
     * @covers ::registerAlias
     * @depends testConstructWithArgs
     */
    public function testConstructWithDefaults()
    {
        $class = $this->testedClass;
        $configInterface = $class::INTERFACE_CONFIG;
        $slInterface = $class::INTERFACE_LOCATOR;
        $customtestObj = new $class();

        $this->assertTrue(
            $customtestObj->getConfig() instanceof $configInterface
        );
        $this->assertTrue(
            $customtestObj->getLocator() instanceof $slInterface
        );
    }


    /**
     * @covers ::setClassListFromConfig
     */
    public function testSetClassListFromConfig()
    {
        $classes = ['foo' => 'bar', 'baz' => 'bat'];

        $this->configMock->expects($this->once())
                         ->method('GET')
                         ->with($this->identicalTo('CLASSES'))
                         ->willReturn($classes);
        $this->locatorMock->expects($this->once())
                          ->method('Override')
                          ->with($this->identicalTo($classes));

        $this->testObj->setClassListFromConfig();
    }


    /**
     * @covers ::registerExceptionHandler
     */
    public function testRegisterExceptionHandler()
    {
        $services = $this->readAttribute($this->testObj, 'services');
        $globalInterface = $services['globalhandler'];
        $globalMock = $this->getMock('\\' . $globalInterface);

        $this->locatorMock->expects($this->once())
                          ->method('Make')
                          ->With($this->identicalTo($globalInterface))
                          ->WillReturn($globalMock);

        $globalMock->expects($this->once())
                   ->method('registerSelf');

        $this->testObj->registerExceptionHandler();
    }

    /**
     * @covers ::registerExceptionHandler
     * @depends testRegisterExceptionHandler
     */
    public function testRegisterExceptionHandlerFailure()
    {
        $services = $this->readAttribute($this->testObj, 'services');
        $globalInterface = $services['globalhandler'];
        $globalMock = $this->getMock('\\' . $globalInterface);
        $ioInterface = $services['io'];
        $ioMock = $this->getMock('\\' . $ioInterface);

        $e = new \Exception('test exception');

        $this->locatorMock->expects($this->at(0))
                          ->method('Make')
                          ->With($this->identicalTo($globalInterface))
                          ->WillReturn($globalMock);
        $this->locatorMock->expects($this->at(1))
                          ->method('Make')
                          ->With($this->identicalTo($ioInterface))
                          ->WillReturn($ioMock);

        $globalMock->expects($this->once())
                   ->method('registerSelf')
                   ->will($this->throwException($e));

        $ioMock->expects($this->once())
               ->method('toExit')
               ->with($this->stringContains($e->getMessage()));

        $this->testObj->registerExceptionHandler();
    }


    /**
     * @covers ::serveRequest
     * @covers ::passToRouter
     */
    public function testServeRequest()
    {
        $this->setUpServicesForMain();

        $this->configMock->expects($this->once())
                         ->method('Get')
                         ->With($this->identicalTo('APP'),
                                $this->identicalTo('force_https'))
                         ->willReturn(false);

        $this->testObj->serveRequest();
    }


    /**
     * @covers ::serveRequest
     * @covers ::redirectProtocol
     * @depends testServeRequest
     */
    public function testServeRequestWithHttpsRedirect()
    {
        $services = $this->readAttribute($this->testObj, 'services');

        $serverString = 'any string/';
        $expectedHeader = 'Location: https://' . $serverString . $serverString;

        $ioMock = $this->getMock('\\' . $services['io']);
        $ioMock->expects($this->once())
               ->method('Header')
               ->with($this->identicalTo($expectedHeader))
               ->will($this->returnSelf());
        $ioMock->expects($this->once())
               ->method('toExit');

        $this->locatorMock->expects($this->once())
                          ->method('Make')
                          ->With($this->identicalTo($services['io']))
                          ->willReturn($ioMock);

        $this->configMock->expects($this->once())
                         ->method('Get')
                         ->With($this->identicalTo('APP'),
                                $this->identicalTo('force_https'))
                         ->willReturn(true);

        $this->testObj->method('getServerProp')
                      ->willReturn($serverString);

        $this->testObj->serveRequest();
    }

    /**
     * @covers ::serveRequest
     * @covers ::redirectProtocol
     * @covers ::passToRouter
     * @depends testServeRequest
     * @depends testServeRequestWithHttpsRedirect
     */
    public function testServeRequestWithIgnoredRedirect()
    {
        $this->setUpServicesForMain();

        $this->configMock->expects($this->once())
                         ->method('Get')
                         ->With($this->identicalTo('APP'),
                                $this->identicalTo('force_https'))
                         ->willReturn(true);

        $this->testObj->method('getServerProp')
                      ->willReturn('on');
        $this->testObj->serveRequest();
    }
}
