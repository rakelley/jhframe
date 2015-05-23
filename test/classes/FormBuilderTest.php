<?php
namespace rakelley\jhframe\test\classes;

use \rakelley\jhframe\test\helpers\PHPUnitUtil as Utility;

/**
 * @coversDefaultClass \rakelley\jhframe\classes\FormBuilder
 */
class FormBuilderTest extends \rakelley\jhframe\test\helpers\cases\Base
{
    protected $testedClass = '\rakelley\jhframe\classes\FormBuilder';


    public function labelProvider()
    {
        return [
            [ //empty case
                [],
                null,
                ''
            ],
            [ //regular case
                ['label' => 'label text'],
                null,
                '<label>label text</label>'
            ],
            [ //for case
                ['label' => 'label text', 'attr' => ['name' => 'foo']],
                null,
                '<label for="foo">label text</label>'
            ],
            [ //with input case
                ['label' => 'label text'],
                '<input />',
                '<label><input />label text</label>'
            ],
        ];
    }


    public function checkboxProvider()
    {
        return [
            [ //no rules case
                [],
                '<input type="checkbox" />',
            ],
            [ //just attributes case
                ['attr' => ['foo' => 'bar']],
                '<input type="checkbox" foo="bar" />',
            ],
            [ //label case
                ['label' => 'label text'],
                '<label><input type="checkbox" />label text</label>',
            ],
        ];
    }


    public function inputProvider()
    {
        return [
            [ //base case
                ['type' => 'text'],
                null,
                '<input type="text" />'
            ],
            [ //attributes case
                ['type' => 'text', 'attr' => ['foo' => 'bar']],
                null,
                '<input foo="bar" type="text" />'
            ],
            [ //non-standard attributes case
                ['type' => 'text', 'required' => true, 'autofocus' => true],
                null,
                '<input type="text" required autofocus />'
            ],
            [ //data case
                ['type' => 'text'],
                'foobar',
                '<input type="text" value="foobar" />'
            ],
            [ //label case
                ['type' => 'text', 'label' => 'label text'],
                null,
                '<label>label text</label><input type="text" />'
            ],
        ];
    }


    public function passwordProvider()
    {
        return [
            [
                ['type' => 'password', 'attr' => ['class' => 'valNewPassword']],
                true
            ],
            [
                ['type' => 'password', 'attr' => ['class' => 'other']],
                false
            ],
        ];
    }


    public function textareaProvider()
    {
        return [
            [ //base case
                [],
                null,
                '<textarea></textarea>'
            ],
            [ //attributes case
                ['attr' => ['foo' => 'bar']],
                null,
                '<textarea foo="bar"></textarea>'
            ],
            [ //non-standard attributes case
                ['required' => true, 'autofocus' => true],
                null,
                '<textarea required autofocus></textarea>'
            ],
            [ //data case
                [],
                'foobar',
                '<textarea>foobar</textarea>'
            ],
            [ //label case
                ['label' => 'label text'],
                null,
                '<label>label text</label><textarea></textarea>'
            ],
        ];
    }


    /**
     * @covers ::combineAttributes
     */
    public function testCombineAttributes()
    {
        $attributes = [
            'foo' => 'bar',
            'baz' => 'bat',
            'lorem' => 'ipsum',
        ];
        $expected = ' foo="bar" baz="bat" lorem="ipsum"';

        $this->assertEquals($expected,
                            $this->testObj->combineAttributes($attributes));
    }


    /**
     * @covers ::constructLabel
     * @dataProvider labelProvider
     */
    public function testConstructLabel($rules, $input, $expected)
    {
        $this->assertEquals($expected,
                            $this->testObj->constructLabel($rules, $input));
    }


    /**
     * @covers ::constructCheckbox
     * @depends testConstructLabel
     * @depends testCombineAttributes
     * @dataProvider checkboxProvider
     */
    public function testConstructCheckbox($rules, $expected)
    {
        $this->assertEquals($expected,
                            $this->testObj->constructCheckbox($rules));
    }


    /**
     * @covers ::constructInput
     * @depends testConstructLabel
     * @depends testCombineAttributes
     * @dataProvider inputProvider
     */
    public function testConstructInput($rules, $data, $expected)
    {
        $this->assertEquals($expected,
                            $this->testObj->constructInput($rules, $data));
    }


    /**
     * @covers ::constructPassword
     * @depends testConstructLabel
     * @depends testCombineAttributes
     * @depends testConstructInput
     * @dataProvider passwordProvider
     */
    public function testConstructPassword($rules, $hasAppendedField)
    {
        $result = $this->testObj->constructPassword($rules);
        $appendange = '<div class="password_strength">';
        if ($hasAppendedField) {
            $this->assertContains($appendange, $result);
        } else {
            $this->assertNotContains($appendange, $result);
        }
    }


    /**
     * @covers ::constructSelect
     * @covers ::<protected>
     * @depends testConstructLabel
     * @depends testCombineAttributes
     */
    public function testConstructSelect()
    {
        $rules = [
            'options' => [
                'foo' => 'bar',
            ],
        ];
        $expected = '<select><option value="foo">bar</option></select>';

        $this->assertEquals($expected, $this->testObj->constructSelect($rules));
    }

    /**
     * @covers ::constructSelect
     * @covers ::<protected>
     * @depends testConstructSelect
     */
    public function testConstructSelectWithLabelAndAttributes()
    {
        $rules = [
            'options' => [
                'foo' => 'bar',
            ],
            'label' => 'label text',
            'attr' => ['baz' => 'bat'],
            'required' => true,
        ];
        $expected = '<label>label text</label><select baz="bat" required>' . 
                    '<option value="foo">bar</option></select>';

        $this->assertEquals($expected, $this->testObj->constructSelect($rules));
    }

    /**
     * @covers ::constructSelect
     * @covers ::<protected>
     * @depends testConstructSelect
     */
    public function testConstructSelectWithJustData()
    {
        $rules = [];
        $data = ['foo' => 'bar'];
        $expected = '<select><option value="foo">bar</option></select>';

        $this->assertEquals($expected,
                            $this->testObj->constructSelect($rules, $data));
    }

    /**
     * @covers ::constructSelect
     * @covers ::<protected>
     * @depends testConstructSelectWithJustData
     */
    public function testConstructSelectWithCombinedData()
    {
        $rules = [
            'options' => [
                'foo' => 'bar',
            ],
        ];
        $data = ['baz' => 'bat'];

        $expected = '<select><option value="foo">bar</option>' .
                    '<option value="baz">bat</option></select>';

        $this->assertEquals($expected,
                            $this->testObj->constructSelect($rules, $data));
    }

    /**
     * @covers ::constructSelect
     * @covers ::<protected>
     * @depends testConstructSelect
     */
    public function testConstructSelectWithNumericKeys()
    {
        $rules = [
            'options' => ['foo', 'bar'],
        ];
        $expected = '<select><option value="foo">foo</option>' .
                    '<option value="bar">bar</option></select>';

        $this->assertEquals($expected, $this->testObj->constructSelect($rules));
    }

    /**
     * @covers ::constructSelect
     * @covers ::<protected>
     * @depends testConstructSelect
     */
    public function testConstructSelectWithEmptyKey()
    {
        $rules = [
            'options' => [
                'empty' => 'bar',
            ],
        ];
        $expected = '<select><option value="">bar</option></select>';

        $this->assertEquals($expected, $this->testObj->constructSelect($rules));
    }

    /**
     * @covers ::constructSelect
     * @covers ::<protected>
     * @depends testConstructSelect
     */
    public function testConstructSelectWithSelected()
    {
        $rules = [
            'options' => [
                'foo' => 'bar',
                'baz' => 'bat',
            ],
        ];
        $data = null;
        $selected = 'foo';

        $expected = '<select><option value="foo" selected>bar</option>' .
                    '<option value="baz">bat</option></select>';

        $this->assertEquals(
            $expected,
            $this->testObj->constructSelect($rules, $data, $selected)
        );
    }


    /**
     * @covers ::constructStatusBlock
     */
    public function testConstructStatusBlock()
    {
        $block = $this->testObj->constructStatusBlock();
        $this->assertInternalType('string', $block);
    }

    /**
     * @covers ::constructStatusBlock
     * @depends testConstructStatusBlock
     */
    public function testConstructStatusBlockWithMessage()
    {
        $message = 'lorem ipsum message';
        $block = $this->testObj->constructStatusBlock($message);
        $this->assertInternalType('string', $block);
        $this->assertContains($message, $block);
    }


    /**
     * @covers ::constructTextarea
     * @depends testConstructLabel
     * @depends testCombineAttributes
     * @dataProvider textareaProvider
     */
    public function testConstructTextarea($rules, $data, $expected)
    {
        $this->assertEquals($expected,
                            $this->testObj->constructTextarea($rules, $data));
    }


    /**
     * @covers ::constructTitle
     */
    public function testConstructTitle()
    {
        $title = 'lorem ipsum title';
        $result = $this->testObj->constructTitle($title);
        $this->assertInternalType('string', $result);
        $this->assertContains($title, $result);
    }

    /**
     * @covers ::constructTitle
     * @depends testConstructTitle
     */
    public function testConstructTitleCompound()
    {
        $title = [
            'title' => 'lorem ipsum title',
            'sub' => 'foo bar subtitle'
        ];
        $result = $this->testObj->constructTitle($title);
        $this->assertInternalType('string', $result);
        $this->assertContains($title['title'], $result);
        $this->assertContains($title['sub'], $result);
    }
}
