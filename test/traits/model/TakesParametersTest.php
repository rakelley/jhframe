<?php
namespace rakelley\jhframe\test\traits\model;

use \rakelley\jhframe\test\helpers\PHPUnitUtil as Utility;

/**
 * @coversDefaultClass \rakelley\jhframe\traits\model\TakesParameters
 */
class TakesParametersTest extends
    \rakelley\jhframe\test\helpers\cases\ModelTrait
{
    protected $testedClass = '\rakelley\jhframe\traits\model\TakesParameters';


    /**
     * @covers ::setParameters
     * @dataProvider parameterProvider
     */
    public function testSetParameters($parameters)
    {
        if ($parameters !== []) {
            $this->testObj->expects($this->once())
                           ->method('resetProperties');
        }

        $this->testObj->setParameters($parameters);
        $this->assertAttributeEquals($parameters, 'parameters', $this->testObj);
    }

    public function parameterProvider()
    {
        return [
            [['foo' => 'bar', 'baz' => 'bat', 2 => 'flan']],
            [[0,1,2,3,4]],
            [[]],
            [null],
        ];
    }

    /**
     * Expected to not reset properties when setParameters called with identical
     * parameters
     *
     * @covers ::setParameters
     * @depends testSetParameters
     */
    public function testSetParametersNoChange()
    {
        $parameters = ['baz' => 'bat', 'burz' => 'um'];
        Utility::setProperties(['parameters' => $parameters], $this->testObj);

        $this->testObj->expects($this->never())
                      ->method('resetProperties');

        $this->testObj->setParameters($parameters);
    }
}
