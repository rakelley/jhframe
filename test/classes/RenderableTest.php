<?php
namespace rakelley\jhframe\test\classes;

/**
 * @coversDefaultClass \rakelley\jhframe\classes\Renderable
 */
class RenderableTest extends \rakelley\jhframe\test\helpers\cases\Base
{
    protected $rendererMock;


    protected function setUp()
    {
        $rendererInterface = '\rakelley\jhframe\interfaces\services\IRenderer';
        $testedClass = '\rakelley\jhframe\classes\Renderable';

        $this->rendererMock = $this->getMock($rendererInterface);

        $this->testObj = new $testedClass($this->rendererMock);
    }


    /**
     * @covers ::__construct
     */
    public function testConstruct()
    {
        $this->assertAttributeEquals($this->rendererMock, 'renderer',
                                     $this->testObj);
    }


    /**
     * @covers ::setContent
     */
    public function testSetContent()
    {
        $content = 'lorem ipsum';

        $this->assertEquals($this->testObj,
                            $this->testObj->setContent($content));
        $this->assertAttributeEquals($content, 'content', $this->testObj);
    }


    /**
     * @covers ::getContent
     * @depends testSetContent
     */
    public function testGetContent()
    {
        $this->testSetContent();
        $this->assertEquals(
            $this->readAttribute($this->testObj, 'content'),
            $this->testObj->getContent()
        );
    }


    /**
     * @covers ::setMetaData
     */
    public function testSetMetaData()
    {
        $metaData = ['lorem', 'ipsum'];

        $this->assertEquals($this->testObj,
                            $this->testObj->setMetaData($metaData));
        $this->assertAttributeEquals($metaData, 'meta', $this->testObj);
    }


    /**
     * @covers ::getMetaData
     * @depends testSetMetaData
     */
    public function testGetMetaData()
    {
        $this->testSetMetaData();
        $this->assertEquals(
            $this->readAttribute($this->testObj, 'meta'),
            $this->testObj->getMetaData()
        );
    }


    /**
     * @covers ::setType
     */
    public function testSetType()
    {
        $type = 'lorem ipsum';

        $this->assertEquals($this->testObj,
                            $this->testObj->setType($type));
        $this->assertAttributeEquals($type, 'type', $this->testObj);
    }


    /**
     * @covers ::getType
     * @depends testSetType
     */
    public function testGetType()
    {
        $this->testSetType();
        $this->assertEquals(
            $this->readAttribute($this->testObj, 'type'),
            $this->testObj->getType()
        );
    }


    /**
     * @covers ::Render
     * @depends testConstruct
     */
    public function testRender()
    {
        $this->rendererMock->expects($this->once())
                           ->method('Render')
                           ->with($this->identicalTo($this->testObj));

        $this->testObj->Render();
    }
}
