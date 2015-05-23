<?php
namespace rakelley\jhframe\test\classes;

use \rakelley\jhframe\classes\InputException,
    \rakelley\jhframe\test\helpers\PHPUnitUtil as Utility;

/**
 * @coversDefaultClass \rakelley\jhframe\classes\ArgumentValidator
 */
class ArgumentValidatorTest extends \rakelley\jhframe\test\helpers\cases\Base
{

    protected function setUp()
    {
        $testedClass = '\rakelley\jhframe\classes\ArgumentValidator';

        $mockedMethods = [
            'getInput',//trait implemented
        ];
        $this->testObj = $this->getMock($testedClass, $mockedMethods);
    }


    /**
     * @covers ::getResult
     */
    public function testGetResult()
    {
        $validated = ['foo' => 'bar', 'baz' => 'bat'];
        Utility::setProperties(['validated' => $validated], $this->testObj);

        $this->assertEquals($validated, $this->testObj->getResult());
    }


    /**
     * @covers ::Proceed
     * @depends testGetResult
     */
    public function testProceedRequired()
    {
        $required = ['foo', 'bar'];
        $method = 'get';
        $result = ['foo' => 'baz', 'bar' => 'bat'];

        $this->testObj->expects($this->once())
                      ->method('getInput')
                      ->With($this->identicalTo($required),
                             $this->identicalTo($method),
                             $this->identicalTo(false))
                      ->willReturn($result);

        $this->testObj->setParameters(['required' => $required,
                                       'method' => $method]);

        $this->assertEquals(true, $this->testObj->Proceed());
        $this->assertEquals($result, $this->testObj->getResult());
    }

    /**
     * @covers ::Proceed
     * @depends testGetResult
     */
    public function testProceedAccepted()
    {
        $accepted = ['foo', 'bar'];
        $method = 'get';
        $result = ['foo' => 'baz', 'bar' => 'bat'];

        $this->testObj->expects($this->once())
                      ->method('getInput')
                      ->With($this->identicalTo($accepted),
                             $this->identicalTo($method),
                             $this->identicalTo(true))
                      ->willReturn($result);

        $this->testObj->setParameters(['accepted' => $accepted,
                                       'method' => $method]);

        $this->assertEquals(true, $this->testObj->Proceed());
        $this->assertEquals($result, $this->testObj->getResult());
    }

    /**
     * @covers ::Proceed
     * @depends testProceedRequired
     * @depends testProceedAccepted
     */
    public function testProceedBoth()
    {
        $required = ['foo'];
        $accepted = ['bar'];
        $method = 'get';
        $result = ['foo' => 'baz', 'bar' => 'bat'];

        $this->testObj->expects($this->at(0))
                      ->method('getInput')
                      ->With($this->identicalTo($required),
                             $this->identicalTo($method),
                             $this->identicalTo(false))
                      ->willReturn(['foo' => 'baz']);
        $this->testObj->expects($this->at(1))
                      ->method('getInput')
                      ->With($this->identicalTo($accepted),
                             $this->identicalTo($method),
                             $this->identicalTo(true))
                      ->willReturn(['bar' => 'bat']);

        $this->testObj->setParameters(['required' => $required,
                                       'accepted' => $accepted,
                                       'method' => $method]);

        $this->assertEquals(true, $this->testObj->Proceed());
        $this->assertEquals($result, $this->testObj->getResult());
    }

    /**
     * @covers ::Proceed
     */
    public function testProceedFailure()
    {
        $required = ['foo', 'bar'];
        $method = 'get';
        $message = 'test exception message';
        $exc = new InputException($message);

        $this->testObj->expects($this->once())
                      ->method('getInput')
                      ->With($this->identicalTo($required),
                             $this->identicalTo($method),
                             $this->identicalTo(false))
                      ->Will($this->throwException($exc));

        $this->testObj->setParameters(['required' => $required,
                                       'method' => $method]);

        $this->assertEquals(false, $this->testObj->Proceed());
        $this->assertEquals($message, $this->testObj->getError());
    }
}
