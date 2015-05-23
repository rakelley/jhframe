<?php
namespace rakelley\jhframe\test\classes;

/**
 * @coversDefaultClass \rakelley\jhframe\classes\WhereBuilder
 */
class WhereBuilderTest extends \rakelley\jhframe\test\helpers\cases\Base
{
    protected $dbMock;

    
    protected function setUp()
    {
        $dbInterface = '\rakelley\jhframe\interfaces\services\IDatabase';
        $testedClass = '\rakelley\jhframe\classes\WhereBuilder';

        $this->dbMock = $this->getMock($dbInterface);

        $this->testObj = new $testedClass;
    }


    /**
     * @covers ::newWhere
     */
    public function testNewWhere()
    {
        $expected = ' WHERE ';

        $this->assertEquals($this->testObj,
                            $this->testObj->newWhere($this->dbMock));
        $this->assertAttributeEquals($expected, 'where', $this->testObj);
    }

    /**
     * @covers ::newWhere
     * @depends testNewWhere
     */
    public function testNewWhereWithOperator()
    {
        $operator = 'OR';
        $expected = ' OR ';

        $this->assertEquals($this->testObj,
                            $this->testObj->newWhere($this->dbMock, $operator));
        $this->assertAttributeEquals($expected, 'where', $this->testObj);
    }


    /**
     * @covers ::Equals
     * @covers ::<protected>
     * @depends testNewWhere
     */
    public function testEqualsSingle()
    {
        $column = 'foobar';
        $expected = ' WHERE `foobar`=:foobar';

        $this->dbMock->expects($this->once())
                     ->Method('Append')
                     ->With($expected);

        $returnValue = $this->testObj->newWhere($this->dbMock)
                                     ->Equals($column);
        $this->assertEquals($returnValue, $this->dbMock);
    }

    /**
     * @covers ::Equals
     * @covers ::<protected>
     * @depends testNewWhere
     * @depends testNewWhereWithOperator
     * @depends testEqualsSingle
     */
    public function testEqualsMultiple()
    {
        $column = ['foobar', 'bazbat'];
        $operator = 'OR';
        $expected = [' WHERE `foobar`=:foobar OR ', '`bazbat`=:bazbat'];

        $this->dbMock->expects($this->at(0))
                     ->Method('Append')
                     ->With($expected[0]);
        $this->dbMock->expects($this->at(1))
                     ->Method('Append')
                     ->With($expected[1]);

        $returnValue = $this->testObj->newWhere($this->dbMock)
                                     ->Equals($column, $operator);
        $this->assertEquals($returnValue, $this->dbMock);

    }


    /**
     * @covers ::In
     * @covers ::<protected>
     * @depends testNewWhere
     */
    public function testIn()
    {
        $column = 'foobar';
        $values = ['foo', 'bar', 'baz'];
        $expected = ' WHERE `foobar` IN (?,?,?)';

        $this->dbMock->expects($this->once())
                     ->Method('Append')
                     ->With($expected);

        $returnValue = $this->testObj->newWhere($this->dbMock)
                                     ->In($column, $values);
        $this->assertEquals($returnValue, $this->dbMock);
    }


    /**
     * @covers ::Like
     * @covers ::<protected>
     * @depends testNewWhere
     */
    public function testLikeSingle()
    {
        $column = 'foobar';
        $placeholder = 'bazbat';
        $expected = ' WHERE `foobar` LIKE :bazbat';

        $this->dbMock->expects($this->once())
                     ->Method('Append')
                     ->With($expected);

        $returnValue = $this->testObj->newWhere($this->dbMock)
                                     ->Like($column, $placeholder);
        $this->assertEquals($returnValue, $this->dbMock);
    }

    /**
     * @covers ::Like
     * @covers ::<protected>
     * @depends testNewWhere
     * @depends testLikeSingle
     */
    public function testLikeMultiple()
    {
        $column = 'foobar';
        $placeholder = ['bazbat', 'burzum'];
        $operator = 'OR';
        $expected = [' WHERE `foobar` LIKE :bazbat OR ', '`foobar` LIKE :burzum'];

        $this->dbMock->expects($this->at(0))
                     ->Method('Append')
                     ->With($expected[0]);
        $this->dbMock->expects($this->at(1))
                     ->Method('Append')
                     ->With($expected[1]);

        $returnValue = $this->testObj->newWhere($this->dbMock)
                                     ->Like($column, $placeholder, $operator);
        $this->assertEquals($returnValue, $this->dbMock);
    }


    /**
     * @covers ::isNull
     * @covers ::<protected>
     * @depends testNewWhere
     */
    public function testIsNull()
    {
        $column = 'foobar';
        $expected = ' WHERE `foobar` IS NULL';

        $this->dbMock->expects($this->once())
                     ->Method('Append')
                     ->With($expected);

        $returnValue = $this->testObj->newWhere($this->dbMock)
                                     ->isNull($column);
        $this->assertEquals($returnValue, $this->dbMock);
    }


    /**
     * @covers ::isNotNull
     * @covers ::<protected>
     * @depends testNewWhere
     */
    public function testIsNotNull()
    {
        $column = 'foobar';
        $expected = ' WHERE `foobar` IS NOT NULL';

        $this->dbMock->expects($this->once())
                     ->Method('Append')
                     ->With($expected);

        $returnValue = $this->testObj->newWhere($this->dbMock)
                                     ->isNotNull($column);
        $this->assertEquals($returnValue, $this->dbMock);
    }
}
