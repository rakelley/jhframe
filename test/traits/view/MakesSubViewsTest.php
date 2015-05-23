<?php
namespace rakelley\jhframe\test\traits\view;

/**
 * @coversDefaultClass \rakelley\jhframe\traits\view\MakesSubViews
 */
class MakesSubViewsTest extends \rakelley\jhframe\test\helpers\cases\Base
{
    protected $renderableMock;
    protected $vcMock;


    protected function setUp()
    {
        $renderableInterface = '\rakelley\jhframe\interfaces\IRenderable';
        $vcInterface = '\rakelley\jhframe\interfaces\services\IViewController';
        $locatorInterface =
            '\rakelley\jhframe\interfaces\services\IServiceLocator';
        $testedTrait = '\rakelley\jhframe\traits\view\MakesSubViews';

        $this->renderableMock = $this->getMock($renderableInterface);

        $this->vcMock = $this->getMock($vcInterface);

        $locatorMock = $this->getMock($locatorInterface);
        $locatorMock->method('Make')
                    ->with($this->identicalTo($vcInterface))
                    ->willReturn($this->vcMock);

        $this->testObj = $this->getMockForTrait($testedTrait);
        $this->testObj->method('getLocator')
                      ->willReturn($locatorMock);
    }


    /**
     * @covers ::makeSubViews
     */
    public function testMakeSubViews()
    {
        $list = [
            'foo' => '\example\ns\Foo', //qualified case
            'bar' => 'Bar', //simple-name case
        ];
        $namespace = '\other\ns';
        $fooContent = 'lorem ipsum';
        $barContent = 'dolor sit amet';
        $expected = ['foo' => $fooContent, 'bar' => $barContent];

        $this->testObj->expects($this->once())
                      ->method('getSubViewList')
                      ->willReturn($list);
        $this->testObj->expects($this->once())
                      ->method('getCalledNamespace')
                      ->willReturn($namespace);

        $this->vcMock->expects($this->at(0))
                     ->method('createView')
                     ->with($this->identicalTo($list['foo']))
                     ->willReturn($this->renderableMock);
        $this->renderableMock->expects($this->at(0))
                             ->method('getContent')
                             ->willReturn($fooContent);
        $this->vcMock->expects($this->at(1))
                     ->method('createView')
                     ->with($this->identicalTo($namespace . '\\' . $list['bar']))
                     ->willReturn($this->renderableMock);
        $this->renderableMock->expects($this->at(1))
                             ->method('getContent')
                             ->willReturn($barContent);

        $this->testObj->makeSubViews();
        $this->assertAttributeEquals($expected, 'subViews', $this->testObj);
    }

    /**
     * @covers ::makeSubViews
     * @depends testMakeSubViews
     */
    public function testMakeSubViewsWithParameters()
    {
        $list = [
            'foo' => '\example\ns\Foo',
            'bar' => '\example\ns\Bar',
        ];
        $parameters = ['foobar' => 'bazbat'];

        $this->testObj->parameters = $parameters;
        $this->testObj->expects($this->once())
                      ->method('getSubViewList')
                      ->willReturn($list);

        $this->vcMock->expects($this->at(0))
                     ->method('createView')
                     ->with($this->identicalTo($list['foo']),
                            $this->identicalTo($parameters))
                     ->willReturn($this->renderableMock);
        $this->renderableMock->expects($this->at(0))
                             ->method('getContent');
        $this->vcMock->expects($this->at(1))
                     ->method('createView')
                     ->with($this->identicalTo($list['bar']),
                            $this->identicalTo($parameters))
                     ->willReturn($this->renderableMock);
        $this->renderableMock->expects($this->at(1))
                             ->method('getContent');

        $this->testObj->makeSubViews();
    }
}
