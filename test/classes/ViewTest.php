<?php
namespace rakelley\jhframe\test\classes;

use \rakelley\jhframe\test\helpers\PHPUnitUtil as Utility;

/**
 * @coversDefaultClass \rakelley\jhframe\classes\View
 */
class ViewTest extends \rakelley\jhframe\test\helpers\cases\Base
{

    protected function setUp()
    {
        $testedClass = '\rakelley\jhframe\classes\View';

        $this->testObj = $this->getMockForAbstractClass($testedClass);
    }


    /**
     * @covers ::returnView
     */
    public function testReturnView()
    {
        $content = 'foobar';
        $type = 'foo';
        $meta = ['foo' => 'bar'];
        $expected = [
            'content' => $content,
            'type' => $type,
            'meta' => $meta,
        ];
        $properties = [
            'viewContent' => $content,
            'contentType' => $type,
            'metaData' => $meta,
        ];
        Utility::setProperties($properties, $this->testObj);

        $this->assertEquals($expected, $this->testObj->returnView());
    }

    /**
     * @covers ::returnView
     */
    public function testReturnViewInvalid()
    {
        $this->setExpectedException('\BadMethodCallException');
        $this->testObj->returnView();
    }
}
