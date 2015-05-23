<?php
namespace rakelley\jhframe\test\classes;

use \rakelley\jhframe\test\helpers\PHPUnitUtil as Utility;

abstract class ViewControllerTestDummy implements
    \rakelley\jhframe\interfaces\ITakesParameters,
    \rakelley\jhframe\interfaces\view\IView,
    \rakelley\jhframe\interfaces\view\IHasMetaData,
    \rakelley\jhframe\interfaces\view\IHasSubViews,
    \rakelley\jhframe\interfaces\view\IRequiresData
{

}

/**
 * @coversDefaultClass \rakelley\jhframe\classes\ViewController
 */
class ViewControllerTest extends \rakelley\jhframe\test\helpers\cases\Base
{
    protected $cacheMock;
    protected $renderableMock;
    protected $slMock;
    protected $viewMock;

 
    protected function setUp()
    {
        $renderableInterface = '\rakelley\jhframe\interfaces\IRenderable';
        $cacheInterface = '\rakelley\jhframe\interfaces\services\IKeyValCache';
        $slInterface = '\rakelley\jhframe\interfaces\services\IServiceLocator';
        $testedClass = '\rakelley\jhframe\classes\ViewController';

        $this->renderableMock = $this->getMock($renderableInterface);
        $this->renderableMock->method('getNewInstance')
                             ->will($this->returnSelf());
        $this->renderableMock->method('setContent')
                             ->will($this->returnSelf());
        $this->renderableMock->method('setType')
                             ->will($this->returnSelf());
        $this->renderableMock->method('setMetaData')
                             ->will($this->returnSelf());

        $this->cacheMock = $this->getMock($cacheInterface);

        $this->slMock = $this->getMock($slInterface);

        $mockedMethods = [
            'getServerProp',//trait implemented
            'getLocator',//trait implemented
        ];
        $this->testObj = $this->getMockBuilder($testedClass)
                              ->setConstructorArgs([$this->renderableMock,
                                                    $this->cacheMock])
                              ->setMethods($mockedMethods)
                              ->getMock();
        $this->testObj->method('getLocator')
                      ->willReturn($this->slMock);
    }

    protected function setUpViewMock($view, $interfaces=false)
    {
        if ($interfaces) {
            $viewClass = __NAMESPACE__ . '\ViewControllerTestDummy';
            $this->viewMock = $this->getMockForAbstractClass($viewClass);
        } else {
            $viewInterface = '\rakelley\jhframe\interfaces\view\IView';
            $this->viewMock = $this->getMock($viewInterface);
        }

        $this->slMock->method('Make')
                     ->with($this->isType('string'))
                     ->willReturn($this->viewMock);

        $this->viewMock->expects($this->once())
                       ->method('constructView');
        $this->viewMock->expects($this->once())
                       ->method('returnView')
                       ->willReturn($view);
    }


    /**
     * @covers ::__construct
     */
    public function testConstruct()
    {
        $this->assertAttributeEquals($this->renderableMock, 'renderable',
                                     $this->testObj);
        $this->assertAttributeEquals($this->cacheMock, 'cache', $this->testObj);
    }


    /**
     * @covers ::createView
     * @covers ::buildView
     * @covers ::toRenderable
     * @depends testConstruct
     */
    public function testCreateViewSimple()
    {
        $view = ['content' => 'lorem', 'type' => 'string',
                 'meta' => ['ar', 'ray']];
        $viewName = 'any';

        $this->setUpViewMock($view);

        $this->cacheMock->expects($this->never())
                        ->method('Read');
        $this->cacheMock->expects($this->never())
                        ->method('Write');

        $this->renderableMock->expects($this->once())
                             ->method('setContent')
                             ->with($this->identicalTo($view['content']))
                             ->will($this->returnSelf());
        $this->renderableMock->expects($this->once())
                             ->method('setType')
                             ->with($this->identicalTo($view['type']))
                             ->will($this->returnSelf());
        $this->renderableMock->expects($this->once())
                             ->method('setMetaData')
                             ->with($this->identicalTo($view['meta']))
                             ->will($this->returnSelf());

        $this->assertEquals(
            $this->testObj->createView($viewName),
            $this->renderableMock
        );
    }

    /**
     * @covers ::createView
     * @covers ::buildView
     * @depends testCreateViewSimple
     */
    public function testCreateViewAllInterfaces()
    {
        $view = ['content' => 'lorem', 'type' => 'string',
                 'meta' => ['ar', 'ray']];
        $viewName = 'any';
        $parameters = ['foo' => 'bar'];

        $this->setUpViewMock($view, true);

        $this->viewMock->expects($this->once())
                       ->method('setParameters')
                       ->with($this->identicalTo($parameters));
        $this->viewMock->expects($this->once())
                       ->method('fetchData');
        $this->viewMock->expects($this->once())
                       ->method('makeSubViews');
        $this->viewMock->expects($this->once())
                       ->method('fetchMetaData');

        $this->testObj->createView($viewName, $parameters);
    }

    /**
     * @covers ::createView
     * @covers ::getCacheKey
     * @depends testCreateViewAllInterfaces
     */
    public function testCreateViewCacheHit()
    {
        $view = ['content' => 'lorem', 'type' => 'string',
                 'meta' => ['ar', 'ray']];
        $viewName = 'any';
        $parameters = ['foo' => 'bar'];
        $cacheable = true;

        $this->cacheMock->expects($this->atLeastOnce())
                        ->method('Read')
                        ->with($this->stringContains($viewName))
                        ->willReturn($view);
        $this->cacheMock->expects($this->never())
                        ->method('Write');

        $this->slMock->expects($this->never())
                     ->method('Make');

        $this->testObj->createView($viewName, $parameters, $cacheable);
    }

    /**
     * @covers ::createView
     * @covers ::getCacheKey
     * @depends testCreateViewAllInterfaces
     */
    public function testCreateViewCacheMiss()
    {
        $view = ['content' => 'lorem', 'type' => 'string',
                 'meta' => ['ar', 'ray']];
        $viewName = 'any';
        $parameters = ['foo' => 'bar'];
        $cacheable = true;

        $this->setUpViewMock($view);

        $this->cacheMock->expects($this->once())
                        ->method('Read')
                        ->with($this->stringContains($viewName))
                        ->willReturn(false);
        $this->cacheMock->expects($this->once())
                        ->method('Write')
                        ->with($this->identicalTo($view),
                               $this->stringContains($viewName));

        $this->testObj->createView($viewName, $parameters, $cacheable);
    }
}
