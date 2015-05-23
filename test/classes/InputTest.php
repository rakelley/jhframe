<?php
namespace rakelley\jhframe\test\classes;

use \rakelley\jhframe\test\helpers\PHPUnitUtil as Utility;

/**
 * @coversDefaultClass \rakelley\jhframe\classes\Input
 */
class InputTest extends \rakelley\jhframe\test\helpers\cases\Base
{
    protected $filterMock;
    protected $ioMock;


    protected function setUp()
    {
        $filterInterface = '\rakelley\jhframe\interfaces\services\IFilter';
        $ioInterface = '\rakelley\jhframe\interfaces\services\IIo';

        $this->filterMock = $this->getMock($filterInterface);
        $this->ioMock = $this->getMock($ioInterface);
    }

    protected function setUpInput($rules=null)
    {
        $configInterface = '\rakelley\jhframe\interfaces\services\IConfig';
        $testedClass = '\rakelley\jhframe\classes\Input';

        $configMock = $this->getMock($configInterface);
        $configMock->method('Get')
                   ->with($this->identicalTo('APP'),
                          $this->identicalTo('input_rules'))
                   ->willReturn($rules);

        $mockedMethods = [
            'getConfig',//trait implemented
        ];
        $this->testObj = $this->getMockBuilder($testedClass)
                              ->disableOriginalConstructor()
                              ->setMethods($mockedMethods)
                              ->getMock();
        $this->testObj->method('getConfig')
                      ->willReturn($configMock);
        Utility::callConstructor($this->testObj, [$this->filterMock,
                                                  $this->ioMock]);
    }

    protected function setUpTable($table, array $value)
    {
        $this->ioMock->method('getInputTable')
                     ->with($this->identicalTo($table))
                     ->willReturn($value);
    }



    /**
     * @covers ::__construct
     */
    public function testConstruct()
    {
        $rules = ['foo' => ['bar', 'baz'], 'bat' => ['burzum']];
        $this->setUpInput($rules);

        $this->assertAttributeEquals($rules, 'defaultRules', $this->testObj);
        $this->assertAttributeEquals($this->filterMock, 'filter',
                                     $this->testObj);
        $this->assertAttributeEquals($this->ioMock, 'io', $this->testObj);
    }

    /**
     * @covers ::__construct
     * @depends testConstruct
     */
    public function testConstructNoRules()
    {
        $this->setUpInput();
        $this->assertAttributeEmpty('defaultRules', $this->testObj);
    }


    /**
     * @covers ::getList
     * @covers ::getValue
     * @depends testConstruct
     */
    public function testGetListSimple()
    {
        $this->setUpInput();

        $method = 'get';
        $table = ['foo' => 'foovalue', 'bar' => 'barvalue', 'baz' => 'bazvalue'];
        $this->setUpTable($method, $table);

        $list = ['foo' => '', 'bar' => []];
        $optional = false;
        $expected = ['foo' => 'foovalue', 'bar' => 'barvalue'];

        $this->assertEquals(
            $expected,
            $this->testObj->getList($list, $method, $optional)
        );
    }


    /**
     * @covers ::getList
     * @covers ::<protected>
     * @depends testGetListSimple
     */
    public function testGetListWithDefaultValueRule()
    {
        $this->setUpInput();

        $method = 'get';
        $table = ['foo' => 'foovalue', 'baz' => 'bazvalue'];
        $this->setUpTable($method, $table);

        $list = [
            'foo' => [],
            'bar' => ['defaultvalue' => 'defaultbar'],
        ];
        $optional = false;
        $expected = ['foo' => 'foovalue', 'bar' => 'defaultbar'];

        $this->assertEquals($expected,
                            $this->testObj->getList($list, $method, $optional));
    }

    /**
     * @covers ::getList
     * @covers ::<protected>
     * @depends testGetListSimple
     */
    public function testGetListWithFilterRules()
    {
        $this->setUpInput();

        $method = 'get';
        $table = ['foo' => 'foovalue', 'bar' => 'barvalue', 'baz' => 'bazvalue'];
        $this->setUpTable($method, $table);

        $list = [
            'foo' => ['filters' => 'foo'],
            'bar' => ['filters' => ['bar', 'baz']],
        ];
        $optional = false;
        $expected = ['foo' => 'foovalue', 'bar' => 'barvalue'];

        $this->filterMock->expects($this->at(0))
                         ->method('Filter')
                         ->With($this->identicalTo($table['foo']),
                                $this->identicalTo($list['foo']['filters']))
                         ->will($this->returnArgument(0));
        $this->filterMock->expects($this->at(1))
                         ->method('Filter')
                         ->With($this->identicalTo($table['bar']),
                                $this->identicalTo($list['bar']['filters']))
                         ->will($this->returnArgument(0));

        $this->assertEquals($expected,
                            $this->testObj->getList($list, $method, $optional));
    }

    /**
     * @covers ::getList
     * @covers ::<protected>
     * @depends testGetListWithFilterRules
     */
    public function testGetListWithFilterRuleFailure()
    {
        $this->setUpInput();

        $method = 'get';
        $table = ['foo' => 'foovalue', 'bar' => 'barvalue', 'baz' => 'bazvalue'];
        $this->setUpTable($method, $table);

        $list = [
            'foo' => ['filters' => 'foo'],
            'bar' => ['filters' => ['bar', 'baz']],
        ];
        $optional = false;

        $this->filterMock->expects($this->once())
                         ->method('Filter')
                         ->With($this->identicalTo($table['foo']),
                                $this->identicalTo($list['foo']['filters']))
                         ->willReturn(null);

        $this->setExpectedException('\rakelley\jhframe\classes\InputException');
        $this->testObj->getList($list, $method, $optional);
    }

    /**
     * @covers ::getList
     * @covers ::<protected>
     * @depends testGetListSimple
     */
    public function testGetListWithEqualToRule()
    {
        $this->setUpInput();

        $method = 'get';
        $table = ['foo' => 'foovalue', 'bar' => 'foovalue'];
        $this->setUpTable($method, $table);

        $list = [
            'foo' => [],
            'bar' => ['equalto' => 'foo'],
        ];
        $optional = false;
        $expected = ['foo' => 'foovalue', 'bar' => 'foovalue'];

        $this->assertEquals($expected,
                            $this->testObj->getList($list, $method, $optional));
    }

    /**
     * @covers ::getList
     * @covers ::<protected>
     * @depends testGetListWithEqualToRule
     */
    public function testGetListWithEqualToRuleFailure()
    {
        $this->setUpInput();

        $method = 'get';
        $table = ['foo' => 'foovalue', 'bar' => 'barvalue'];
        $this->setUpTable($method, $table);

        $list = [
            'foo' => [],
            'bar' => ['equalto' => 'foo'],
        ];
        $optional = false;

        $this->setExpectedException('\rakelley\jhframe\classes\InputException');
        $this->testObj->getList($list, $method, $optional);
    }

    /**
     * @covers ::getList
     * @covers ::<protected>
     * @depends testGetListSimple
     */
    public function testGetListWithMinLengthRule()
    {
        $this->setUpInput();

        $method = 'get';
        $table = ['foo' => 'foovalue', 'bar' => 'barvalue', 'baz' => 'bazvalue'];
        $this->setUpTable($method, $table);

        $list = [
            'foo' => ['minlength' => 2],
        ];
        $optional = false;
        $expected = ['foo' => 'foovalue'];

        $this->assertEquals($expected,
                            $this->testObj->getList($list, $method, $optional));
    }

    /**
     * @covers ::getList
     * @covers ::<protected>
     * @depends testGetListWithMinLengthRule
     */
    public function testGetListWithMinLengthRuleFailure()
    {
        $this->setUpInput();

        $method = 'get';
        $table = ['foo' => 'foovalue', 'bar' => 'barvalue', 'baz' => 'bazvalue'];
        $this->setUpTable($method, $table);

        $list = [
            'foo' => ['minlength' => 20],
        ];
        $optional = false;

        $this->setExpectedException('\rakelley\jhframe\classes\InputException');
        $this->testObj->getList($list, $method, $optional);
    }

    /**
     * @covers ::getList
     * @covers ::<protected>
     * @depends testGetListSimple
     */
    public function testGetListWithMaxLengthRule()
    {
        $this->setUpInput();

        $method = 'get';
        $table = ['foo' => 'foovalue', 'bar' => 'barvalue', 'baz' => 'bazvalue'];
        $this->setUpTable($method, $table);

        $list = [
            'foo' => ['maxlength' => 20],
        ];
        $optional = false;
        $expected = ['foo' => 'foovalue'];

        $this->assertEquals($expected,
                            $this->testObj->getList($list, $method, $optional));
    }

    /**
     * @covers ::getList
     * @covers ::<protected>
     * @depends testGetListWithMaxLengthRule
     */
    public function testGetListWithMaxLengthRuleFailure()
    {
        $this->setUpInput();

        $method = 'get';
        $table = ['foo' => 'foovalue', 'bar' => 'barvalue', 'baz' => 'bazvalue'];
        $this->setUpTable($method, $table);

        $list = [
            'foo' => ['maxlength' => 2],
        ];
        $optional = false;

        $this->setExpectedException('\rakelley\jhframe\classes\InputException');
        $this->testObj->getList($list, $method, $optional);
    }

    /**
     * @covers ::getList
     * @covers ::<protected>
     * @depends testGetListSimple
     */
    public function testGetListWithDefaultRules()
    {
        $rules = [
            'foo' => ['filters' => 'foo'],
            'bar' => ['filters' => ['bar', 'baz']],
        ];
        $this->setUpInput($rules);

        $method = 'get';
        $table = ['foo' => 'foovalue', 'bar' => 'barvalue', 'baz' => 'bazvalue'];
        $this->setUpTable($method, $table);

        $list = [
            'foo' => 'default',
            'bar' => 'default',
        ];
        $optional = false;
        $expected = ['foo' => 'foovalue', 'bar' => 'barvalue'];

        $this->filterMock->expects($this->at(0))
                         ->method('Filter')
                         ->With($this->identicalTo($table['foo']),
                                $this->identicalTo($rules['foo']['filters']))
                         ->will($this->returnArgument(0));
        $this->filterMock->expects($this->at(1))
                         ->method('Filter')
                         ->With($this->identicalTo($table['bar']),
                                $this->identicalTo($rules['bar']['filters']))
                         ->will($this->returnArgument(0));

        $this->assertEquals($expected,
                            $this->testObj->getList($list, $method, $optional));
    }

    /**
     * @covers ::getList
     * @covers ::<protected>
     * @depends testGetListWithDefaultRules
     */
    public function testGetListWithDefaultRulesMissing()
    {
        $this->setUpInput();

        $method = 'get';
        $table = ['foo' => 'foovalue', 'bar' => 'barvalue', 'baz' => 'bazvalue'];
        $this->setUpTable($method, $table);

        $list = [
            'foo' => 'default',
        ];
        $optional = false;

        $this->setExpectedException('\RuntimeException');
        $this->testObj->getList($list, $method, $optional);
    }

    /**
     * @covers ::getList
     * @covers ::getValue
     * @depends testGetListSimple
     */
    public function testGetListMissingRequiredValue()
    {
        $this->setUpInput();

        $method = 'get';
        $table = [];
        $this->setUpTable($method, $table);

        $list = ['foo' => [], 'bar' => []];
        $optional = false;

        $this->setExpectedException('\rakelley\jhframe\classes\InputException');
        $this->testObj->getList($list, $method, $optional);
    }

    /**
     * @covers ::getList
     * @covers ::getValue
     * @depends testGetListSimple
     */
    public function testGetListMissingOptionalValue()
    {
        $this->setUpInput();

        $method = 'get';
        $table = ['foo' => 'foovalue', 'baz' => 'bazvalue'];
        $this->setUpTable($method, $table);

        $list = ['foo' => [], 'bar' => []];
        $optional = true;
        $expected = ['foo' => 'foovalue'];

        $this->assertEquals($expected,
                            $this->testObj->getList($list, $method, $optional));
    }


    /**
     * @covers ::searchKeys
     * @depends testConstruct
     */
    public function testSearchKeysSimple()
    {
        $this->setUpInput();

        $method = 'get';
        $table = ['foo1' => 'foovalue', 'foo2' => 'barvalue', 'baz3' => 'bazvalue'];
        $this->setUpTable($method, $table);

        $pattern = '/foo\d+/';
        $rules = null;
        $expected = ['foo1' => 'foovalue', 'foo2' => 'barvalue'];

        $this->assertEquals($expected,
                            $this->testObj->searchKeys($pattern, $method,
                                                       $rules));

        $pattern = '/bar/';
        $this->assertEquals(null,
                            $this->testObj->searchKeys($pattern, $method,
                                                       $rules));
    }

    /**
     * @covers ::searchKeys
     * @covers ::applyRules
     * @depends testSearchKeysSimple
     */
    public function testSearchKeysWithRules()
    {
        $this->setUpInput();

        $method = 'get';
        $table = ['foo1' => 'foovalue', 'foo2' => 'barvalue', 'baz3' => 'bazvalue'];
        $this->setUpTable($method, $table);

        $pattern = '/foo\d+/';
        $rules = ['filters' => ['filter', 'rules']];
        $expected = ['foo1' => 'foovalue', 'foo2' => 'barvalue'];

        $this->filterMock->expects($this->at(0))
                         ->method('Filter')
                         ->With($this->identicalTo($table['foo1']),
                                $this->identicalTo($rules['filters']))
                         ->will($this->returnArgument(0));
        $this->filterMock->expects($this->at(1))
                         ->method('Filter')
                         ->With($this->identicalTo($table['foo2']),
                                $this->identicalTo($rules['filters']))
                         ->will($this->returnArgument(0));

        $this->assertEquals($expected,
                            $this->testObj->searchKeys($pattern, $method,
                                                       $rules));
    }

    /**
     * @covers ::searchKeys
     * @covers ::applyRules
     * @depends testSearchKeysWithRules
     */
    public function testSearchKeysWithRulesFailure()
    {
        $this->setUpInput();

        $method = 'get';
        $table = ['foo1' => 'foovalue', 'foo2' => 'barvalue', 'baz3' => 'bazvalue'];
        $this->setUpTable($method, $table);

        $pattern = '/foo\d+/';
        $rules = ['minlength' => 20];

        $this->setExpectedException('\rakelley\jhframe\classes\InputException');
        $this->testObj->searchKeys($pattern, $method, $rules);
    }


    /**
     * @covers ::searchValues
     * @depends testConstruct
     */
    public function testSearchValuesSimple()
    {
        $this->setUpInput();

        $method = 'get';
        $table = ['foo1' => 'foovalue', 'foo2' => 'barvalue', 'baz3' => 'bazvalue'];
        $this->setUpTable($method, $table);

        $pattern = '/bar/';
        $rules = null;
        $expected = ['foo2' => 'barvalue'];

        $this->assertEquals($expected,
                            $this->testObj->searchValues($pattern, $method,
                                                         $rules));

        $pattern = '/bat/';
        $this->assertEquals(null,
                            $this->testObj->searchValues($pattern, $method,
                                                         $rules));
    }

    /**
     * @covers ::searchValues
     * @covers ::applyRules
     * @depends testSearchValuesSimple
     */
    public function testSearchValuesWithRules()
    {
        $this->setUpInput();

        $method = 'get';
        $table = ['foo1' => 'foovalue', 'foo2' => 'barvalue', 'baz3' => 'bazvalue'];
        $this->setUpTable($method, $table);

        $pattern = '/bar/';
        $rules = ['filters' => ['filter', 'rules']];
        $expected = ['foo2' => 'barvalue'];

        $this->filterMock->expects($this->once())
                         ->method('Filter')
                         ->With($this->identicalTo($table['foo2']),
                                $this->identicalTo($rules['filters']))
                         ->will($this->returnArgument(0));

        $this->assertEquals($expected,
                            $this->testObj->searchValues($pattern, $method,
                                                         $rules));
    }

    /**
     * @covers ::searchValues
     * @covers ::applyRules
     * @depends testSearchValuesWithRules
     */
    public function testSearchValuesWithRulesFailure()
    {
        $this->setUpInput();

        $method = 'get';
        $table = ['foo1' => 'foovalue', 'foo2' => 'barvalue', 'baz3' => 'bazvalue'];
        $this->setUpTable($method, $table);

        $pattern = '/bar/';
        $rules = ['minlength' => 20];

        $this->setExpectedException('\rakelley\jhframe\classes\InputException');
        $this->testObj->searchValues($pattern, $method, $rules);
    }
}
