<?php
namespace rakelley\jhframe\test\classes;

/**
 * @coversDefaultClass \rakelley\jhframe\classes\Database
 */
class DatabaseTest extends \rakelley\jhframe\test\helpers\cases\Base
{
    protected $joinMock;
    protected $stmntMock;
    protected $whereMock;


    protected function setUp()
    {
        $stmntInterface =
            '\rakelley\jhframe\interfaces\services\IStatementAbstractor';
        $joinInterface =
            '\rakelley\jhframe\interfaces\services\IDatabaseJoinBuilder';
        $whereInterface =
            '\rakelley\jhframe\interfaces\services\IDatabaseWhereBuilder';
        $testedClass = '\rakelley\jhframe\classes\Database';

        $this->stmntMock = $this->getMock($stmntInterface);

        $this->whereMock = $this->getMock($whereInterface);

        $this->joinMock = $this->getMock($joinInterface);

        $this->testObj = new $testedClass($this->joinMock, $this->whereMock,
                                        $this->stmntMock);
    }



    /**
     * @covers ::__construct
     */
    public function testConstruct()
    {
        $this->assertAttributeEquals($this->joinMock, 'joinBuilder',
                                     $this->testObj);
        $this->assertAttributeEquals($this->whereMock, 'whereBuilder',
                                     $this->testObj);
        $this->assertAttributeEquals($this->stmntMock, 'statementService',
                                     $this->testObj);
    }


    /**
     * @covers ::setQuery
     */
    public function testSetQuery()
    {
        $query = 'generic string';

        $this->assertEquals($this->testObj, $this->testObj->setQuery($query));
        $this->assertAttributeEquals($query, 'query', $this->testObj);
    }


    /**
     * @covers ::returnQuery
     * @depends testSetQuery
     */
    public function testReturnQuery()
    {
        $query = 'generic string';

        $this->assertEquals($query,
                            $this->testObj->setQuery($query)->returnQuery());
    }


    /**
     * @covers ::newQuery
     */
    public function testNewQueryFailure()
    {
        $type = 'genericType';
        $table = 'foobar';
        $args = ['foo' => 'bar', 'baz' => 'bat'];

        $this->setExpectedException('\DomainException');
        $this->testObj->newQuery($type, $table, $args);
    }

    /**
     * @covers ::newQuery
     * @covers ::createSelect
     */
    public function testNewQuerySelect()
    {
        $type = 'select';
        $table = 'foo';

        $args = null;
        $expected = 'SELECT * FROM `foo`';
        $this->assertEquals(
            $expected,
            $this->testObj->newQuery($type, $table, $args)->returnQuery()
        );

        $args = ['select' => 'bar'];
        $expected = 'SELECT `bar` FROM `foo`';
        $this->assertEquals(
            $expected,
            $this->testObj->newQuery($type, $table, $args)->returnQuery()
        );

        $args = ['select' => 'bar', 'distinct' => true];
        $expected = 'SELECT DISTINCT `bar` FROM `foo`';
        $this->assertEquals(
            $expected,
            $this->testObj->newQuery($type, $table, $args)->returnQuery()
        );
    }

    /**
     * @covers ::newQuery
     * @covers ::createInsert
     */
    public function testNewQueryInsert()
    {
        $type = 'insert';
        $table = 'foo';

        $args = ['columns' => ['bar', 'baz']];
        $expected = 'INSERT INTO `foo` (`bar`,`baz`) VALUES (:bar,:baz)';
        $this->assertEquals(
            $expected,
            $this->testObj->newQuery($type, $table, $args)->returnQuery()
        );

        $args = ['columns' => ['bar', 'baz'], 'rows' => 3];
        $expected =
            'INSERT INTO `foo` (`bar`,`baz`) VALUES (?,?), (?,?), (?,?)';
        $this->assertEquals(
            $expected,
            $this->testObj->newQuery($type, $table, $args)->returnQuery()
        );
    }

    /**
     * @covers ::newQuery
     * @covers ::createUpdate
     */
    public function testNewQueryUpdate()
    {
        $type = 'update';
        $table = 'foo';

        $args = ['columns' => ['bar', 'baz', 'bat']];
        $expected = 'UPDATE `foo` SET `bar`=:bar, `baz`=:baz, `bat`=:bat';
        $this->assertEquals(
            $expected,
            $this->testObj->newQuery($type, $table, $args)->returnQuery()
        );
    }

    /**
     * @covers ::newQuery
     * @covers ::createDelete
     */
    public function testNewQueryDelete()
    {
        $type = 'delete';
        $table = 'foo';

        $args = null;
        $expected = 'DELETE FROM `foo`';
        $this->assertEquals(
            $expected,
            $this->testObj->newQuery($type, $table, $args)->returnQuery()
        );
    }


    /**
     * @covers ::makeStatement
     * @depends testConstruct
     * @depends testSetQuery
     */
    public function testMakeStatement()
    {
        $query = 'generic string';

        $this->stmntMock->expects($this->once())
                        ->method('makeStatement')
                        ->with($this->identicalTo($query))
                        ->willReturn($this->stmntMock);

        $this->assertEquals($this->stmntMock,
                            $this->testObj->setQuery($query)->makeStatement());
    }


    /**
     * @covers ::addWhere
     * @depends testConstruct
     */
    public function testAddWhere()
    {
        $operator = 'generic string';

        $this->whereMock->expects($this->once())
                        ->method('newWhere')
                        ->with($this->identicalTo($this->testObj),
                               $this->identicalTo($operator))
                        ->willReturn($this->whereMock);

        $this->assertEquals($this->whereMock,
                            $this->testObj->addWhere($operator));
    }


    /**
     * @covers ::addJoin
     * @depends testConstruct
     */
    public function testAddJoin()
    {
        $table = 'generic string';
        $type = 'other string';

        $this->joinMock->expects($this->once())
                       ->method('newJoin')
                       ->with($this->identicalTo($this->testObj),
                              $this->identicalTo($table),
                              $this->identicalTo($type))
                       ->willReturn($this->joinMock);

        $this->assertEquals($this->joinMock,
                            $this->testObj->addJoin($table, $type));
    }


    /**
     * @covers ::addOrder
     * @depends testSetQuery
     * @depends testReturnQuery
     */
    public function testAddOrder()
    {
        $order = ['ASC' => ['foo', 'bar'], 'DESC' => ['baz']];
        $query = 'generic string';
        $expected = 'generic string ORDER BY `foo`,`bar` ASC, `baz` DESC';

        $this->testObj->setQuery($query);
        $this->assertEquals($this->testObj, $this->testObj->addOrder($order));
        $this->assertEquals($expected, $this->testObj->returnQuery());
    }


    /**
     * @covers ::addLimit
     * @depends testSetQuery
     * @depends testReturnQuery
     */
    public function testAddLimit()
    {
        $query = 'generic string';

        $limit = 5;
        $expected = 'generic string LIMIT 5';
        $this->testObj->setQuery($query);
        $this->assertEquals($this->testObj, $this->testObj->addLimit($limit));
        $this->assertEquals($expected, $this->testObj->returnQuery());

        $limit = [5,10];
        $expected = 'generic string LIMIT 5,10';
        $this->testObj->setQuery($query);
        $this->assertEquals($this->testObj, $this->testObj->addLimit($limit));
        $this->assertEquals($expected, $this->testObj->returnQuery());
    }


    /**
     * @covers ::stripTicks
     * @depends testSetQuery
     * @depends testReturnQuery
     */
    public function testStripTicks()
    {
        $query = 'SELECT `foo` FROM `bar`';
        $expected = 'SELECT foo FROM bar';

        $this->testObj->setQuery($query);
        $this->assertEquals($this->testObj, $this->testObj->stripTicks());
        $this->assertEquals($expected, $this->testObj->returnQuery());
    }


    /**
     * @covers ::Append
     * @depends testSetQuery
     * @depends testReturnQuery
     */
    public function testAppend()
    {
        $query = 'generic string';
        $part = 'other string';
        $expected = $query . $part;

        $this->testObj->setQuery($query);
        $this->assertEquals($this->testObj, $this->testObj->Append($part));
        $this->assertEquals($expected, $this->testObj->returnQuery());
    }


    /**
     * @covers ::Prepend
     * @depends testSetQuery
     * @depends testReturnQuery
     */
    public function testPrepend()
    {
        $query = 'generic string';
        $part = 'other string';
        $expected = $part . $query;

        $this->testObj->setQuery($query);
        $this->assertEquals($this->testObj, $this->testObj->Prepend($part));
        $this->assertEquals($expected, $this->testObj->returnQuery());
    }
}
