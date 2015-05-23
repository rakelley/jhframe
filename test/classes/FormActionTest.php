<?php
namespace rakelley\jhframe\test\classes;

use \rakelley\jhframe\test\helpers\PHPUnitUtil as Utility;

/**
 * @coversDefaultClass \rakelley\jhframe\classes\FormAction
 */
class FormActionTest extends \rakelley\jhframe\test\helpers\cases\Base
{
    protected $viewMock;
    protected $validatorMock;


    protected function setUp()
    {
        $viewInterface = '\rakelley\jhframe\interfaces\view\IFormView';
        $validatorInterface =
            '\rakelley\jhframe\interfaces\services\IFormValidator';
        $testedClass = '\rakelley\jhframe\classes\FormAction';

        $this->viewMock = $this->getMock($viewInterface);

        $this->validatorMock = $this->getMock($validatorInterface);

        $mockedMethods = [
            'Proceed',//abstract
        ];
        $this->testObj = $this->getMockBuilder($testedClass)
                              ->setConstructorArgs([$this->validatorMock,
                                                    $this->viewMock])
                              ->setMethods($mockedMethods)
                              ->getMock();
    }


    /**
     * @covers ::__construct
     */
    public function testConstruct()
    {
        $this->assertAttributeEquals($this->validatorMock, 'formValidator',
                                     $this->testObj);
        $this->assertAttributeEquals($this->viewMock, 'view', $this->testObj);
    }


    /**
     * @covers ::Validate
     * @covers ::<protected>
     * @depends testConstruct
     */
    public function testValidate()
    {
        $input = ['foo' => 'bar', 'baz' => 'bat'];

        $this->assertAttributeEquals(null, 'input', $this->testObj);

        $this->validatorMock->expects($this->once())
                            ->method('Validate')
                            ->with($this->identicalTo($this->viewMock))
                            ->willReturn($input);

        $this->assertTrue($this->testObj->Validate());
        $this->assertAttributeEquals($input, 'input', $this->testObj);
    }


    /**
     * Failure case, expected to allow exception to bubble
     * 
     * @covers ::Validate
     * @covers ::<protected>
     * @depends testValidate
     */
    public function testValidateFailure()
    {
        $e = new \Exception('dummy test exception');

        $this->validatorMock->expects($this->once())
                            ->method('Validate')
                            ->with($this->identicalTo($this->viewMock))
                            ->will($this->throwException($e));

        $this->testObj->expects($this->never())
                      ->method('validateInput');

        $this->setExpectedException('\Exception');
        $this->testObj->Validate();
    }
}
