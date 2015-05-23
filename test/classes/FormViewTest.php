<?php
namespace rakelley\jhframe\test\classes;

use \rakelley\jhframe\test\helpers\PHPUnitUtil as Utility;

/**
 * @coversDefaultClass \rakelley\jhframe\classes\FormView
 */
class FormViewTest extends \rakelley\jhframe\test\helpers\cases\Base
{
    protected $builderMock;


    protected function setUp()
    {
        $builderInterface =
            '\rakelley\jhframe\interfaces\services\IFormBuilder';
        $this->builderMock = $this->getMock($builderInterface);

        $this->setUpMockedView(null);
    }

    protected function setUpMockedView(array $methods=null)
    {
        $testedClass = '\rakelley\jhframe\classes\FormView';

        $this->testObj = $this->getMockBuilder($testedClass)
                              ->setConstructorArgs([$this->builderMock])
                              ->setMethods($methods)
                              ->getMockForAbstractClass();
    }


    /**
     * @covers ::__construct
     */
    public function testConstruct()
    {
        $this->assertAttributeEquals($this->builderMock, 'builder',
                                     $this->testObj);
    }


    /**
     * @covers ::getFields
     */
    public function testGetFields()
    {
        $fields = ['foo' => 'bar', 'baz' => 'bat'];
        Utility::setProperties(['fields' => $fields], $this->testObj);

        $this->assertEquals($fields, $this->testObj->getFields());
    }


    /**
     * @covers ::getMethod
     */
    public function testGetMethod()
    {
        $attributes = ['foo' => 'bar', 'baz' => 'bat', 'method' => 'get'];
        Utility::setProperties(['attributes' => $attributes],
                               $this->testObj);

        $this->assertEquals($attributes['method'], $this->testObj->getMethod());
    }


    /**
     * @covers ::constructView
     * @covers ::standardConstructor
     * @covers ::constructField
     * @depends testConstruct
     */
    public function testConstructView()
    {
        $fields = [
            'footextarea' => [
                'type' => 'textarea',
                'lorem' => 'ipsum',
            ],
            'barpassword' => [
                'type' => 'password',
                'lorem' => 'ipsum',
            ],
            'bazselect' => [
                'type' => 'select',
                'lorem' => 'ipsum',
            ],
            'batcheckbox' => [
                'type' => 'checkbox',
                'lorem' => 'ipsum',
            ],
            'burzuminput' => [
                'type' => 'anythingelse',
                'lorem' => 'ipsum',
            ],
        ];
        $properties = [
            'attributes' => ['any', 'array'],
            'data' => [],
            'fields' => $fields,
            'title' => null,
        ];
        Utility::setProperties($properties, $this->testObj);

        $this->builderMock->expects($this->once())
                          ->method('combineAttributes')
                          ->with($this->identicalTo($properties['attributes']))
                          ->willReturn('attribute_string');
        $this->builderMock->expects($this->once())
                          ->method('constructStatusBlock')
                          ->with($this->identicalTo(null))
                          ->willReturn('statusblock_string');
        $this->builderMock->expects($this->never())
                          ->method('constructTitle');
        $this->builderMock->expects($this->once())
                          ->method('constructTextarea')
                          ->with($this->identicalTo($fields['footextarea']))
                          ->willReturn('textarea_string');
        $this->builderMock->expects($this->once())
                          ->method('constructPassword')
                          ->with($this->identicalTo($fields['barpassword']))
                          ->willReturn('password_string');
        $this->builderMock->expects($this->once())
                          ->method('constructSelect')
                          ->with($this->identicalTo($fields['bazselect']))
                          ->willReturn('select_string');
        $this->builderMock->expects($this->once())
                          ->method('constructCheckbox')
                          ->with($this->identicalTo($fields['batcheckbox']))
                          ->willReturn('checkbox_string');
        $this->builderMock->expects($this->once())
                          ->method('constructInput')
                          ->with($this->identicalTo($fields['burzuminput']))
                          ->willReturn('input_string');

        $this->testObj->constructView();

        $expected = ['attribute_string', 'statusblock_string',
                     'textarea_string', 'password_string', 'select_string',
                     'checkbox_string', 'input_string', '<form', '</form>',
                     '<fieldset>', '</fieldset>'];
        $content = $this->readAttribute($this->testObj, 'viewContent');
        array_walk(
            $expected,
            function($v) use ($content) {
                $this->assertContains($v, $content);
            }
        );
    }


    /**
     * @covers ::constructView
     * @covers ::<protected>
     * @depends testConstructView
     */
    public function testConstructViewWithData()
    {
        $fields = [
            'footextarea' => [
                'type' => 'textarea',
                'data-binding' => 'foo',
            ],
            'barpassword' => [
                'type' => 'password',
                'data-binding' => '[bar][multilevel]',
            ],
            'bazselect' => [
                'type' => 'select',
                'data-binding' => '[baz][three][levels]',
            ],
            'batcheckbox' => [
                'type' => 'checkbox',
                'data-binding' => 'batparameter',
            ],
            'burzuminput' => [
                'type' => 'anythingelse',
                'data-binding' => 'burzum',
            ],
        ];
        $data = [
            'foo' => 'foo',
            'bar' => ['multilevel' => 'bar'],
            'baz' => ['three' => ['levels' => 'baz']],
            'burzum' => 'burzum',
        ];
        $parameters = ['batparameter' => 'bat'];
        $properties = [
            'attributes' => ['any', 'array'],
            'data' => $data,
            'fields' => $fields,
            'parameters' => $parameters,
            'title' => null,
        ];
        Utility::setProperties($properties, $this->testObj);

        $this->builderMock->method('combineAttributes')
                          ->willReturn('attribute_string');
        $this->builderMock->method('constructStatusBlock')
                          ->willReturn('statusblock_string');
        $this->builderMock->expects($this->once())
                          ->method('constructTextarea')
                          ->with($this->identicalTo($fields['footextarea']),
                                 $this->identicalTo($data['foo']))
                          ->willReturn('textarea_string');
        $this->builderMock->expects($this->once())
                          ->method('constructPassword')
                          ->with($this->identicalTo($fields['barpassword']),
                                 $this->identicalTo($data['bar']['multilevel']))
                          ->willReturn('password_string');
        $this->builderMock->expects($this->once())
                          ->method('constructSelect')
                          ->with($this->identicalTo($fields['bazselect']),
                                 $this->identicalTo(
                                    $data['baz']['three']['levels']
                                )
                            )
                          ->willReturn('select_string');
        $this->builderMock->expects($this->once())
                          ->method('constructCheckbox')
                          ->with($this->identicalTo($fields['batcheckbox']),
                                 $this->identicalTo($parameters['batparameter'])
                            )
                          ->willReturn('checkbox_string');
        $this->builderMock->expects($this->once())
                          ->method('constructInput')
                          ->with($this->identicalTo($fields['burzuminput']),
                                 $this->identicalTo($data['burzum']))
                          ->willReturn('input_string');

        $this->testObj->constructView();
    }

    /**
     * @covers ::constructView
     * @covers ::<protected>
     * @depends testConstructViewWithData
     */
    public function testConstructViewWithDataFailure()
    {
        $fields = [
            'footextarea' => [
                'type' => 'textarea',
                'data-binding' => 'foo',
            ],
        ];
        $data = [];
        $properties = [
            'attributes' => ['any', 'array'],
            'data' => $data,
            'fields' => $fields,
            'title' => null,
        ];
        Utility::setProperties($properties, $this->testObj);

        $this->builderMock->method('combineAttributes')
                          ->willReturn('attribute_string');
        $this->builderMock->method('constructStatusBlock')
                          ->willReturn('statusblock_string');

        $this->setExpectedException('\RuntimeException');
        $this->testObj->constructView();
    }

    /**
     * @covers ::constructView
     * @covers ::standardConstructor
     * @depends testConstructView
     */
    public function testConstructViewWithTitle()
    {
        $fields = [
            'footextarea' => [
                'type' => 'textarea',
                'lorem' => 'ipsum',
            ],
        ];
        $title = 'foobar';
        $properties = [
            'attributes' => ['any', 'array'],
            'data' => [],
            'fields' => $fields,
            'title' => $title,
        ];
        Utility::setProperties($properties, $this->testObj);

        $this->builderMock->method('combineAttributes')
                          ->willReturn('attribute_string');
        $this->builderMock->method('constructStatusBlock')
                          ->willReturn('statusblock_string');
        $this->builderMock->method('constructTextarea')
                          ->willReturn('textarea_string');
        $this->builderMock->expects($this->once())
                          ->method('constructTitle')
                          ->with($this->identicalTo($title))
                          ->willReturn('title_string');

        $this->testObj->constructView();
        $content = $this->readAttribute($this->testObj, 'viewContent');
        $this->assertContains('title_string', $content);
    }

    /**
     * @covers ::constructView
     * @covers ::constructField
     * @depends testConstructView
     */
    public function testConstructViewWithSelectValue()
    {
        $fields = [
            'foo' => [
                'type' => 'select',
                'selected' => 'selectvalue',
            ],
        ];
        $properties = [
            'attributes' => ['any', 'array'],
            'data' => [],
            'fields' => $fields,
            'title' => null,
        ];
        Utility::setProperties($properties, $this->testObj);

        $this->builderMock->method('combineAttributes')
                          ->willReturn('attribute_string');
        $this->builderMock->method('constructStatusBlock')
                          ->willReturn('statusblock_string');
        $this->builderMock->expects($this->once())
                          ->method('constructSelect')
                          ->with($this->identicalTo($fields['foo']),
                                 $this->identicalTo(null),
                                 $this->identicalTo($fields['foo']['selected']))
                          ->willReturn('select_string');

        $this->testObj->constructView();
    }

    /**
     * @covers ::constructView
     * @covers ::constructField
     * @depends testConstructView
     */
    public function testConstructViewWithSelectData()
    {
        $fields = [
            'foo' => [
                'type' => 'select',
                'selected-data' => 'bar',
            ],
        ];
        $data = ['bar' => 'bat'];
        $properties = [
            'attributes' => ['any', 'array'],
            'data' => $data,
            'fields' => $fields,
            'title' => null,
        ];
        Utility::setProperties($properties, $this->testObj);

        $this->builderMock->method('combineAttributes')
                          ->willReturn('attribute_string');
        $this->builderMock->method('constructStatusBlock')
                          ->willReturn('statusblock_string');
        $this->builderMock->expects($this->once())
                          ->method('constructSelect')
                          ->with($this->identicalTo($fields['foo']),
                                 $this->identicalTo(null),
                                 $this->identicalTo(
                                    $data[$fields['foo']['selected-data']]
                                 )
                            )
                          ->willReturn('select_string');

        $this->testObj->constructView();
    }

    /**
     * @covers ::constructView
     * @covers ::constructField
     * @depends testConstructView
     */
    public function testConstructViewWithCustomMethod()
    {
        $this->setUpMockedView(['customFieldMethod']);
        $fields = [
            'foo' => [
                'method' => 'customFieldMethod'
            ],
        ];
        $properties = [
            'attributes' => ['any', 'array'],
            'data' => [],
            'fields' => $fields,
            'title' => null,
        ];
        Utility::setProperties($properties, $this->testObj);

        $this->builderMock->method('combineAttributes')
                          ->willReturn('attribute_string');
        $this->builderMock->method('constructStatusBlock')
                          ->willReturn('statusblock_string');

        $this->testObj->expects($this->once())
                      ->method('customFieldMethod')
                      ->willReturn('customfield_string');

        $this->testObj->constructView();
        $content = $this->readAttribute($this->testObj, 'viewContent');
        $this->assertContains('customfield_string', $content);
    }


    /**
     * @covers ::standardConstructor
     * @depends testConstructView
     */
    public function testStandardConstructorWithMessage()
    {
        $message = 'success message';
        $fields = [
            'footextarea' => [
                'type' => 'textarea',
                'lorem' => 'ipsum',
            ],
        ];
        $properties = [
            'attributes' => ['any', 'array'],
            'data' => [],
            'fields' => $fields,
            'title' => null,
        ];
        Utility::setProperties($properties, $this->testObj);

        $this->builderMock->method('combineAttributes')
                          ->willReturn('attribute_string');
        $this->builderMock->method('constructTextarea')
                          ->willReturn('textarea_string');
        $this->builderMock->expects($this->once())
                          ->method('constructStatusBlock')
                          ->with($this->identicalTo($message))
                          ->willReturn('statusblock_string');

        $result = Utility::callMethod($this->testObj, 'standardConstructor',
                                      [$message]);
        $this->assertTrue(strlen($result) > 1);
    }
}
