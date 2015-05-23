<?php
namespace rakelley\jhframe\test\classes;

use \rakelley\jhframe\interfaces\services\IRenderer,
    \rakelley\jhframe\test\helpers\PHPUnitUtil as Utility;

/**
 * @coversDefaultClass \rakelley\jhframe\classes\Renderer
 */
class RendererTest extends \rakelley\jhframe\test\helpers\cases\Base
{
    protected $locatorMock;
    protected $ioMock;
    protected $templateMock;
    protected $renderableMock;


    protected function setUp()
    {
        $configInterface = '\rakelley\jhframe\interfaces\services\IConfig';
        $locatorInterface =
            '\rakelley\jhframe\interfaces\services\IServiceLocator';
        $ioInterface = '\rakelley\jhframe\interfaces\services\IIo';
        $templateInterface = '\rakelley\jhframe\interfaces\ITemplate';
        $renderableInterface = '\rakelley\jhframe\interfaces\IRenderable';
        $testedClass = '\rakelley\jhframe\classes\Renderer';

        $configMock = $this->getMock($configInterface);
        $configMock->method('Get')
                   ->with($this->identicalTo('ENV'),
                          $this->identicalTo('is_ajax'))
                   ->willReturn(false);

        $this->locatorMock = $this->getMock($locatorInterface);

        $this->ioMock = $this->getMock($ioInterface);

        $this->templateMock = $this->getMock($templateInterface);

        $this->renderableMock = $this->getMock($renderableInterface);

        $mockedMethods = [
            'getConfig',//trait implemented
            'getLocator',//trait implemented
        ];
        $this->testObj = $this->getMockBuilder($testedClass)
                              ->disableOriginalConstructor()
                              ->setMethods($mockedMethods)
                              ->getMock();
        $this->testObj->method('getConfig')
                      ->willReturn($configMock);
        $this->testObj->method('getLocator')
                      ->willReturn($this->locatorMock);
        Utility::callConstructor($this->testObj, [$this->ioMock]);
    }

    protected function setUpRenderable(array $properties)
    {
        if (isset($properties['content'])) {
            $this->renderableMock->method('getContent')
                                 ->willReturn($properties['content']);
        }
        if (isset($properties['type'])) {
            $this->renderableMock->method('getType')
                                 ->willReturn($properties['type']);
        }
        if (isset($properties['meta'])) {
            $this->renderableMock->method('getMetaData')
                                 ->willReturn($properties['meta']);
        }
    }


    /**
     * @covers ::__construct
     */
    public function testConstruct()
    {
        $this->assertAttributeEquals($this->ioMock, 'io', $this->testObj);
        $this->assertNotNull($this->readAttribute($this->testObj, 'isAjax'));
    }


    /**
     * @covers ::Render
     * @covers ::handleType
     * @depends testConstruct
     */
    public function testRenderPlain()
    {
        $renderable = [
            'content' => 'lorem ipsum',
            'type' => IRenderer::TYPE_PLAIN
        ];
        $this->setUpRenderable($renderable);

        $this->ioMock->expects($this->once())
                     ->method('toEcho')
                     ->with($this->identicalTo($renderable['content']));

        $this->testObj->Render($this->renderableMock);
    }

    /**
     * @covers ::Render
     * @covers ::handleType
     * @covers ::isJson
     * @depends testConstruct
     */
    public function testRenderJson($renderable=null)
    {
        if (!$renderable) {
            $renderable = [
                'content' => 'lorem ipsum',
                'type' => IRenderer::TYPE_JSON
            ];
        }
        $this->setUpRenderable($renderable);
        $expectedHeader = 'content-type: application/json';
        $expectedContent = json_encode($renderable['content']);

        $this->ioMock->expects($this->once())
                     ->method('Header')
                     ->with($this->identicalTo($expectedHeader));

        $this->ioMock->expects($this->once())
                     ->method('toEcho')
                     ->with($this->identicalTo($expectedContent));

        $this->testObj->Render($this->renderableMock);
    }

    /**
     * @covers ::Render
     * @covers ::handleType
     * @covers ::isJson
     * @depends testConstruct
     */
    public function testRenderJsonNonStringContent()
    {
        $renderable = [
            'content' => ['lorem', 'ipsum'],
            'type' => IRenderer::TYPE_JSON
        ];
        $this->setUpRenderable($renderable);
        $expectedHeader = 'content-type: application/json';
        $expectedContent = json_encode($renderable['content']);

        $this->ioMock->expects($this->once())
                     ->method('Header')
                     ->with($this->identicalTo($expectedHeader));

        $this->ioMock->expects($this->once())
                     ->method('toEcho')
                     ->with($this->identicalTo($expectedContent));

        $this->testObj->Render($this->renderableMock);
    }

    /**
     * @covers ::Render
     * @covers ::handleType
     * @depends testConstruct
     */
    public function testRenderXml()
    {
        $renderable = [
            'content' => 'lorem ipsum',
            'type' => IRenderer::TYPE_XML
        ];
        $this->setUpRenderable($renderable);
        $expectedHeader = 'content-type: application/xml';

        $this->ioMock->expects($this->once())
                     ->method('Header')
                     ->with($this->identicalTo($expectedHeader));

        $this->ioMock->expects($this->once())
                     ->method('toEcho')
                     ->with($this->identicalTo($renderable['content']));

        $this->testObj->Render($this->renderableMock);
    }

    /**
     * @covers ::Render
     * @covers ::handleType
     * @covers ::makeComposite
     * @depends testConstruct
     */
    public function testRenderPage($renderable=null)
    {
        if (!$renderable) {
            $renderable = [
                'content' => 'lorem ipsum',
                'type' => IRenderer::TYPE_PAGE,
                'meta' => ['foo', 'bar'],
            ];
        }
        $this->setUpRenderable($renderable);

        $this->locatorMock->expects($this->once())
                          ->method('Make')
                          ->willReturn($this->templateMock);

        $this->templateMock->expects($this->once())
                           ->method('makeComposite')
                           ->with($this->identicalTo($renderable['content']),
                                  $this->identicalTo($renderable['meta']))
                           ->will($this->returnArgument(0));

        $this->ioMock->expects($this->once())
                     ->method('toEcho')
                     ->with($this->identicalTo($renderable['content']));

        $this->testObj->Render($this->renderableMock);
    }

    /**
     * @covers ::Render
     * @covers ::handleType
     * @depends testRenderJson
     */
    public function testRenderWithAjax()
    {
        Utility::setProperties(['isAjax' => true], $this->testObj);
        $renderable = [
            'content' => 'lorem ipsum',
            'type' => IRenderer::TYPE_PLAIN
        ];

        $this->testRenderJson($renderable);
    }

    /**
     * @covers ::Render
     * @covers ::handleType
     * @depends testRenderJson
     */
    public function testRenderApi()
    {
        $renderable = [
            'content' => 'lorem ipsum',
            'type' => IRenderer::TYPE_API
        ];

        $this->testRenderJson($renderable);
    }

    /**
     * @covers ::Render
     * @covers ::handleType
     * @depends testRenderPage
     */
    public function testRenderNoType()
    {
        $renderable = [
            'content' => 'lorem ipsum',
            'meta' => ['foo', 'bar'],
        ];

        $this->testRenderPage($renderable);
    }

    /**
     * @covers ::Render
     * @covers ::handleType
     * @depends testRenderPage
     */
    public function testRenderDefault()
    {
        $renderable = [
            'content' => 'lorem ipsum',
            'type' => IRenderer::TYPE_DEFAULT,
            'meta' => ['foo', 'bar'],
        ];

        $this->testRenderPage($renderable);
    }

    /**
     * @covers ::Render
     * @depends testConstruct
     */
    public function testRenderNoContent()
    {
        $renderable = [
            'content' => null,
            'type' => IRenderer::TYPE_PLAIN
        ];
        $this->setUpRenderable($renderable);

        $this->setExpectedException('\DomainException');
        $this->testObj->Render($this->renderableMock);
    }
}
