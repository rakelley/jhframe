<?php
namespace rakelley\jhframe\test\classes;

/**
 * @coversDefaultClass \rakelley\jhframe\classes\FormValidator
 */
class FormValidatorTest extends \rakelley\jhframe\test\helpers\cases\Base
{
    protected $viewMock;


    protected function setUp()
    {
        $viewInterface = '\rakelley\jhframe\interfaces\view\IFormView';
        $testedClass = '\rakelley\jhframe\classes\FormValidator';

        $this->viewMock = $this->getMock($viewInterface);

        $mockedMethods = [
            'getInput',//trait implemented
        ];
        $this->testObj = $this->getMock($testedClass, $mockedMethods);
    }


    /**
     * @covers ::Validate
     * @covers ::<protected>
     */
    public function testValidate()
    {
        $fields = [
            'foo' => [ //file case
                'type' => 'file'
            ],
            'bar' => [ //required case
                'required' => true,
                'sanitize' => 'sanitize rule',
            ],
            'baz' => [ //optional case
                'sanitize' => ['array', 'of', 'sanitize', 'rules'],
            ],
            'bat' => [], //ignore case
        ];
        $method = 'get';
        $expected = [
            'bar' => 'barValue',
            'baz' => 'bazValue',
            'foo' => ['file properties'],
        ];

        $this->viewMock->expects($this->once())
                       ->method('getFields')
                       ->willReturn($fields);
        $this->viewMock->expects($this->once())
                       ->method('getMethod')
                       ->willReturn($method);

        $this->testObj->expects($this->at(0))
                      ->method('getInput')
                      ->With($this->identicalTo(
                                ['bar' => $fields['bar']['sanitize']]
                             ),
                             $this->identicalTo($method),
                             $this->identicalTo(false))
                      ->willReturn(['bar' => $expected['bar']]);

        $this->testObj->expects($this->at(1))
                      ->method('getInput')
                      ->With($this->identicalTo(
                                ['baz' => $fields['baz']['sanitize']]
                             ),
                             $this->identicalTo($method),
                             $this->identicalTo(true))
                      ->willReturn(['baz' => $expected['baz']]);

        $this->testObj->expects($this->at(2))
                      ->method('getInput')
                      ->With($this->identicalTo(['foo' => '']),
                             $this->identicalTo('files'),
                             $this->identicalTo(false))
                      ->willReturn(['foo' => $expected['foo']]);

        $this->assertEquals($expected,
                            $this->testObj->Validate($this->viewMock));
    }
}
