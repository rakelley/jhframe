<?php
namespace rakelley\jhframe\test\traits;

use \rakelley\jhframe\test\helpers\PHPUnitUtil as Utility;

/**
 * @coversDefaultClass \rakelley\jhframe\traits\GetsInput
 */
class GetsInputTest extends \rakelley\jhframe\test\helpers\cases\Base
{
    protected $inputMock;
 

    protected function setUp()
    {
        $inputInterface = '\rakelley\jhframe\interfaces\services\IInput';
        $locatorInterface =
            '\rakelley\jhframe\interfaces\services\IServiceLocator';
        $testedTrait = '\rakelley\jhframe\traits\GetsInput';

        $this->inputMock = $this->getMock($inputInterface);

        $locatorMock = $this->getMock($locatorInterface);
        $locatorMock->method('Make')
                    ->with($this->identicalTo($inputInterface))
                    ->willReturn($this->inputMock);

        $this->testObj = $this->getMockForTrait($testedTrait);
        $this->testObj->method('getLocator')
                      ->willReturn($locatorMock);
    }


    /**
     * @covers ::getInput
     */
    public function testGetInput()
    {
        $args = [
            'list' => ['array'],
            'method' => 'string',
            'optional' => true,
        ];
        $values = ['foo' => 'bar'];

        $this->inputMock->expects($this->once())
                        ->method('getList')
                        ->with($this->identicalTo($args['list']),
                               $this->identicalTo($args['method']),
                               $this->identicalTo($args['optional']))
                        ->willReturn($values);

        $this->assertEquals(
            Utility::callMethod($this->testObj, 'getInput', $args),
            $values
        );
    }

    /**
     * @covers ::getInput
     */
    public function testGetInputDefaultOptionalIsFalse()
    {
        $args = [
            'list' => ['array'],
            'method' => 'string',
        ];
        $expectedOptionalValue = false;
        $values = ['foo' => 'bar'];

        $this->inputMock->expects($this->once())
                        ->method('getList')
                        ->with($this->identicalTo($args['list']),
                               $this->identicalTo($args['method']),
                               $this->identicalTo($expectedOptionalValue))
                        ->willReturn($values);

        $this->assertEquals(
            Utility::callMethod($this->testObj, 'getInput', $args),
            $values
        );
    }

    /**
     * @covers ::getInput
     */
    public function testGetInputWithJustClassParameters()
    {
        $args = [
            'list' => ['foo' => 'bar', 'baz' => 'bat'],
            'method' => 'string',
        ];
        $parameters = ['foo' => 'foo', 'baz' => 'baz'];

        $this->testObj->parameters = $parameters;

        $this->inputMock->expects($this->never())
                        ->method('getList');

        $this->assertEquals(
            Utility::callMethod($this->testObj, 'getInput', $args),
            $parameters
        );
    }

    /**
     * @covers ::getInput
     */
    public function testGetInputWithMixedClassParameters()
    {
        $args = [
            'list' => ['foo' => 'bar', 'baz' => 'bat'],
            'method' => 'string',
        ];
        $parameters = ['foo' => 'foo'];
        $expectedList = ['baz' => 'bat'];
        $values = ['baz' => 'baz'];
        $expectedResult = array_merge($parameters, $values);

        $this->testObj->parameters = $parameters;

        $this->inputMock->expects($this->once())
                        ->method('getList')
                        ->with($this->identicalTo($expectedList),
                               $this->identicalTo($args['method']))
                        ->willReturn($values);

        $this->assertEquals(
            Utility::callMethod($this->testObj, 'getInput', $args),
            $expectedResult
        );
    }
}
