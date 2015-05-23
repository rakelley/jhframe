<?php
namespace rakelley\jhframe\test\classes;

use \rakelley\jhframe\test\helpers\PHPUnitUtil as Utility;

/**
 * @coversDefaultClass \rakelley\jhframe\classes\Router
 */
class RouterTest extends \rakelley\jhframe\test\helpers\cases\Base
{
    protected $controllerMock;
    protected $locatorMock;


    protected function setUp()
    {
        $controllerClass = '\rakelley\jhframe\classes\RouteController';
        $locatorInterface =
            '\rakelley\jhframe\interfaces\services\IServiceLocator';

        $this->controllerMock = $this->getMockBuilder($controllerClass)
                                     ->disableOriginalConstructor()
                                     ->setMethods(['matchRoute', 'fooMethod'])
                                     ->getMockForAbstractClass();

        $this->locatorMock = $this->getMock($locatorInterface);
        $this->locatorMock->method('Make')
                          ->willReturn($this->controllerMock);
    }

    protected function setUpRouter($controllerTakesArg=false)
    {
        $configInterface = '\rakelley\jhframe\interfaces\services\IConfig';
        $testedClass = '\rakelley\jhframe\classes\Router';

        $configMock = $this->getMock($configInterface);
        $configMock->expects($this->once())
                   ->method('Get')
                   ->with($this->identicalTo('APP'), $this->identicalTo('name'))
                   ->willReturn('example');

        $mockedMethods = [
            'getConfig',//trait implemented
            'getLocator',//trait implemented
        ];
        if ($controllerTakesArg) {
            $mockedMethods[] = 'methodTakesArg';
        }
        $this->testObj = $this->getMockBuilder($testedClass)
                              ->disableOriginalConstructor()
                              ->setMethods($mockedMethods)
                              ->getMock();
        $this->testObj->method('getConfig')
                      ->willReturn($configMock);
        $this->testObj->method('getLocator')
                      ->willReturn($this->locatorMock);
        if ($controllerTakesArg) {
            $this->testObj->method('methodTakesArg')
                          ->willReturn(true);
        }
        Utility::callConstructor($this->testObj);
    }


    /**
     * @covers ::__construct
     */
    public function testConstruct()
    {
        $this->setUpRouter();
        $this->assertAttributeNotEmpty('appName', $this->testObj);
    }


    /**
     * @covers ::serveRequest
     * @covers ::<protected>
     * @depends testConstruct
     */
    public function testServeRequestWithOnePart()
    {
        $this->setUpRouter();

        $route = 'foo';
        $type = 'get';
        $uri = '/' . $route;
        $method = 'fooMethod';

        $this->locatorMock->expects($this->once())
                          ->method('Resolve')
                          ->with($this->isType('string'))
                          ->willReturn('valid string');

        $this->controllerMock->expects($this->once())
                             ->method('matchRoute')
                             ->with($this->identicalTo($type),
                                    $this->identicalTo($route))
                             ->willReturn($method);
        $this->controllerMock->expects($this->once())
                             ->method($method);

        $this->testObj->serveRequest($uri, $type);
    }

    /**
     * @covers ::serveRequest
     * @covers ::<protected>
     * @depends testServeRequestWithOnePart
     */
    public function testServeRequestWithTwoParts()
    {
        $this->setUpRouter();

        $route = 'foo';
        $type = 'get';
        $controller = 'example';
        $uri = '/' . $controller . '/' . $route;
        $method = 'fooMethod';

        $this->locatorMock->expects($this->once())
                          ->method('Resolve')
                          ->with($this->stringContains($controller))
                          ->willReturn('valid string');

        $this->controllerMock->expects($this->once())
                             ->method('matchRoute')
                             ->with($this->identicalTo($type),
                                    $this->identicalTo($route))
                             ->willReturn($method);
        $this->controllerMock->expects($this->once())
                             ->method($method);

        $this->testObj->serveRequest($uri, $type);
    }

    /**
     * @covers ::serveRequest
     * @covers ::<protected>
     * @depends testServeRequestWithOnePart
     * @depends testServeRequestWithTwoParts
     */
    public function testServeRequestWithThreeParts()
    {
        $this->setUpRouter();

        $route = 'foo';
        $type = 'get';
        $controller = 'example';
        $uri = '/' . $controller . '/' . $route . '/3';
        $method = 'fooMethod';

        $this->locatorMock->expects($this->once())
                          ->method('Resolve')
                          ->with($this->stringContains($controller))
                          ->willReturn('valid string');

        $this->controllerMock->expects($this->once())
                             ->method('matchRoute')
                             ->with($this->identicalTo($type),
                                    $this->identicalTo($route))
                             ->willReturn($method);
        $this->controllerMock->expects($this->once())
                             ->method($method);

        $this->testObj->serveRequest($uri, $type);
    }

    /**
     * @covers ::serveRequest
     * @depends testServeRequestWithOnePart
     */
    public function testServeRequestWithArgument()
    {
        $this->setUpRouter(true);

        $route = 'foo';
        $type = 'get';
        $controller = 'example';
        $uri = '/' . $controller . '/' . $route;
        $method = 'fooMethod';

        $this->locatorMock->expects($this->once())
                          ->method('Resolve')
                          ->with($this->stringContains($controller))
                          ->willReturn('valid string');

        $this->controllerMock->expects($this->once())
                             ->method('matchRoute')
                             ->with($this->identicalTo($type),
                                    $this->identicalTo($route))
                             ->willReturn($method);
        $this->controllerMock->expects($this->once())
                             ->method($method)
                             ->with($this->identicalTo($route));

        $this->testObj->serveRequest($uri, $type);
    }

    /**
     * @covers ::serveRequest
     * @covers ::parseUri
     * @depends testServeRequestWithOnePart
     */
    public function testServeRequestInvalidUri()
    {
        $this->setUpRouter();

        $type = 'get';
        $uri = '/invalid/uri/too/many/parts';

        $this->setExpectedException('\UnexpectedValueException');
        $this->testObj->serveRequest($uri, $type);
    }

    /**
     * @covers ::serveRequest
     * @depends testServeRequestWithOnePart
     */
    public function testServeRequestInvalidRoute()
    {
        $this->setUpRouter();

        $route = 'foo';
        $type = 'get';
        $controller = 'example';
        $uri = '/' . $controller . '/' . $route;
        $method = 'fooMethod';

        $this->locatorMock->expects($this->once())
                     ->method('Resolve')
                     ->with($this->stringContains($controller))
                     ->willReturn('valid string');

        $exception = new \UnexpectedValueException('example exception');
        $this->controllerMock->expects($this->once())
                             ->method('matchRoute')
                             ->with($this->identicalTo($type),
                                    $this->identicalTo($route))
                             ->will($this->throwException($exception));

        $this->setExpectedException('\UnexpectedValueException');
        $this->testObj->serveRequest($uri, $type);
    }

    /**
     * @covers ::serveRequest
     * @covers ::validateController
     * @covers ::getQualifiedController
     * @depends testServeRequestWithOnePart
     */
    public function testServeRequestInvalidController()
    {
        $this->setUpRouter();

        $this->locatorMock->expects($this->once())
                     ->method('Resolve')
                     ->willReturn(false);

        $this->setExpectedException('\UnexpectedValueException');
        $this->testObj->serveRequest('/any', 'any');
    }
}
