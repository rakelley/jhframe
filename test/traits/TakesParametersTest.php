<?php
namespace rakelley\jhframe\test\traits;

/**
 * @coversDefaultClass \rakelley\jhframe\traits\TakesParameters
 */
class TakesParametersTest extends
    \rakelley\jhframe\test\helpers\cases\SimpleTrait
{
    protected $testedClass = '\rakelley\jhframe\traits\TakesParameters';


    /**
     * @coversNothing
     */
    public function testStartsEmpty()
    {
        $this->assertAttributeEmpty('parameters', $this->testObj);
    }


    /**
     * @covers ::setParameters
     * @depends testStartsEmpty
     * @dataProvider parameterProvider
     */
    public function testSetParameters($parameters)
    {
        $this->testObj->setParameters($parameters);
        $this->assertAttributeEquals($parameters, 'parameters', $this->testObj);
    }


    public function parameterProvider()
    {
        return [
            [['foo' => 'bar', 'baz' => 'bat', 2 => 'flan']],
            [[0,1,2,3,4]],
            [[]],
            [null]
        ];
    }
}
