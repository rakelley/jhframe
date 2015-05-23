<?php
namespace rakelley\jhframe\test\classes;

use \rakelley\jhframe\test\helpers\PHPUnitUtil as Utility;

/**
 * @coversDefaultClass \rakelley\jhframe\classes\RouteController
 */
class RouteControllerTest extends \rakelley\jhframe\test\helpers\cases\Base
{
    protected $actionMock;
    protected $viewMock;
    protected $renderableMock;


    protected function setUp()
    {
        $actionInterface =
            '\rakelley\jhframe\interfaces\services\IActionController';
        $viewInterface =
            '\rakelley\jhframe\interfaces\services\IViewController';
        $renderableInterface = '\rakelley\jhframe\interfaces\IRenderable';
        $testedClass = '\rakelley\jhframe\classes\RouteController';

        $this->actionMock = $this->getMock($actionInterface);
        $this->viewMock = $this->getMock($viewInterface);
        $this->renderableMock = $this->getMock($renderableInterface);

        $mockedMethods = [
            'getCalledNamespace',//trait implemented
        ];
        $this->testObj = $this->getMockBuilder($testedClass)
                              ->setConstructorArgs([$this->actionMock,
                                                    $this->viewMock])
                              ->setMethods($mockedMethods)
                              ->getMockForAbstractClass();
    }


    /**
     * @covers ::__construct
     */
    public function testConstruct()
    {
        $this->assertAttributeEquals($this->actionMock, 'actionController',
                                     $this->testObj);
        $this->assertAttributeEquals($this->viewMock, 'viewController',
                                     $this->testObj);
    }


    /**
     * @covers ::matchRoute
     * @dataProvider goodRouteProvider
     */
    public function testMatchRouteSuccess($routes, $type, $route, $expected)
    {
        Utility::setProperties(['routes' => $routes], $this->testObj);

        $this->assertEquals(
            $expected,
            $this->testObj->matchRoute($type, $route)
        );
    }

    /**
     * @covers ::matchRoute
     * @dataProvider badRouteProvider
     */
    public function testMatchRouteFailure($routes, $type, $route)
    {
        Utility::setProperties(['routes' => $routes], $this->testObj);

        $this->setExpectedException('\UnexpectedValueException');
        $this->testObj->matchRoute($type, $route);
    }

    public function goodRouteProvider()
    {
        $routes = [
            'get' => [
                '/foo/' => 'bar',
                '/\d/'  => 'digits',
            ],
            'post' => [
                '/\w/'  => 'words',
            ]
        ];
        return [
            [$routes, 'get', 'foo', 'bar'],
            [$routes, 'get', 123, 'digits'],
            [$routes, 'post', 'asdf', 'words'],
        ];
    }

    public function badRouteProvider()
    {
        $routes = [
            'get' => [
                '/foo/' => 'bar',
                '/\d/'  => 'digits',
            ],
            'post' => [
                '/\w/'  => 'words',
            ]
        ];
        return [
            [$routes, 'get', 'bad'],
            [$routes, 'post', '$&^;'],
            [$routes, 'flarn', 'foo'],
        ];
    }


    /**
     * @covers ::standardView
     * @depends testConstruct
     */
    public function testStandardView()
    {
        $name = '\testns\views\foobar';
        $parameters = ['foo' => 'bar'];
        $cacheable = false;

        $this->viewMock->expects($this->once())
                       ->method('createView')
                       ->with($this->identicalTo($name),
                              $this->identicalTo($parameters),
                              $this->identicalTo($cacheable))
                       ->willReturn($this->renderableMock);
        $this->renderableMock->expects($this->once())
                             ->method('Render');

        Utility::callMethod($this->testObj, 'standardView',
                            [$name, $parameters, $cacheable]);
    }

    /**
     * @covers ::standardView
     * @depends testConstruct
     */
    public function testStandardViewWithDefaults()
    {
        $name = '\testns\views\foobar';
        $expectedDefaultParameters = null;
        $expectedDefaultCacheable = true;

        $this->viewMock->expects($this->once())
                       ->method('createView')
                       ->with($this->identicalTo($name),
                              $this->identicalTo($expectedDefaultParameters),
                              $this->identicalTo($expectedDefaultCacheable))
                       ->willReturn($this->renderableMock);
        $this->renderableMock->expects($this->once())
                             ->method('Render');

        Utility::callMethod($this->testObj, 'standardView', [$name]);
    }

    /**
     * @covers ::standardView
     * @depends testStandardView
     */
    public function testStandardViewWithFetchedNamespace()
    {
        $name = 'foobar';
        $namespace = '\baz\bat\fetched';
        $expected = $namespace . '\views\\' . $name;

        $this->viewMock->expects($this->once())
                       ->method('createView')
                       ->with($this->identicalTo($expected))
                       ->willReturn($this->renderableMock);
        $this->renderableMock->expects($this->once())
                             ->method('Render');

        $this->testObj->expects($this->once())
                      ->method('getCalledNamespace')
                      ->willReturn($namespace);

        Utility::callMethod($this->testObj, 'standardView', [$name]);
    }


    /**
     * @covers ::standardAction
     * @depends testConstruct
     */
    public function testStandardAction()
    {
        $name = '\testns\actions\foobar';
        $parameters = ['foo' => 'bar'];

        $this->actionMock->expects($this->once())
                         ->method('executeAction')
                         ->with($this->identicalTo($name),
                                $this->identicalTo($parameters))
                         ->willReturn($this->renderableMock);
        $this->renderableMock->expects($this->once())
                             ->method('Render');

        Utility::callMethod($this->testObj, 'standardAction',
                            [$name, $parameters]);
    }

    /**
     * @covers ::standardAction
     * @depends testConstruct
     */
    public function testStandardActionWithDefaults()
    {
        $name = '\testns\actions\foobar';
        $expectedDefaultParameters = null;

        $this->actionMock->expects($this->once())
                         ->method('executeAction')
                         ->with($this->identicalTo($name),
                                $this->identicalTo($expectedDefaultParameters))
                         ->willReturn($this->renderableMock);
        $this->renderableMock->expects($this->once())
                             ->method('Render');

        Utility::callMethod($this->testObj, 'standardAction', [$name]);
    }

    /**
     * @covers ::standardAction
     * @depends testStandardAction
     */
    public function testStandardActionWithFetchedNamespace()
    {
        $name = 'foobar';
        $namespace = '\baz\bat\fetched';
        $expected = $namespace . '\actions\\' . $name;

        $this->actionMock->expects($this->once())
                         ->method('executeAction')
                         ->with($this->identicalTo($expected))
                         ->willReturn($this->renderableMock);
        $this->renderableMock->expects($this->once())
                             ->method('Render');

        $this->testObj->expects($this->once())
                      ->method('getCalledNamespace')
                      ->willReturn($namespace);

        Utility::callMethod($this->testObj, 'standardAction', [$name]);
    }
}
