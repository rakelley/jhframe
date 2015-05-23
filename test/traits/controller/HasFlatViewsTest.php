<?php
namespace rakelley\jhframe\test\traits\controller;

use \rakelley\jhframe\test\helpers\PHPUnitUtil as Utility;

/**
 * @coversDefaultClass \rakelley\jhframe\traits\controller\HasFlatViews
 */
class HasFlatViewsTest extends
    \rakelley\jhframe\test\helpers\cases\SimpleTrait
{
    protected $testedClass = '\rakelley\jhframe\traits\controller\HasFlatViews';


    /**
     * @covers ::flatView
     * @covers ::serveFlatView
     */
    public function testFlatView()
    {
        $view = 'foobar';
        $namespace = 'baz\bat';
        $expected = ['view' => $view, 'namespace' => $namespace . '\views'];

        $this->testObj->expects($this->once())
                      ->method('getCalledNamespace')
                      ->willReturn($namespace);
        $this->testObj->expects($this->once())
                      ->method('standardView')
                      ->with($this->isType('string'),
                             $this->identicalTo($expected));

        $this->testObj->flatView($view);
    }


    /**
     * @covers ::serveFlatView
     */
    public function testServeFlatViewWithNamespaceArg()
    {
        $view = 'foobar';
        $namespace = 'lorem\ipsum';
        $expected = ['view' => $view, 'namespace' => $namespace];

        $this->testObj->expects($this->once())
                      ->method('standardView')
                      ->with($this->isType('string'),
                             $this->identicalTo($expected));

        Utility::callMethod($this->testObj, 'serveFlatView', [$view, $namespace]);
    }
}
