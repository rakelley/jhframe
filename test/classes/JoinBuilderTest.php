<?php
namespace rakelley\jhframe\test\classes;

/**
 * @coversDefaultClass \rakelley\jhframe\classes\JoinBuilder
 */
class JoinBuilderTest extends \rakelley\jhframe\test\helpers\cases\Base
{
    protected $dbMock;

    
    protected function setUp()
    {
        $dbInterface = '\rakelley\jhframe\interfaces\services\IDatabase';
        $testedClass = '\rakelley\jhframe\classes\JoinBuilder';

        $this->dbMock = $this->getMock($dbInterface);

        $this->testObj = new $testedClass;
    }


    /**
     * @covers ::newJoin
     */
    public function testNewJoin()
    {
        $table = 'foobar';
        $expected = ' JOIN `foobar`';

        $this->assertEquals($this->testObj,
                            $this->testObj->newJoin($this->dbMock, $table));
        $this->assertAttributeEquals($expected, 'join', $this->testObj);
    }

    /**
     * @covers ::newJoin
     * @depends testNewJoin
     */
    public function testNewJoinWithType()
    {
        $table = 'foobar';
        $type = 'INNER';
        $expected = ' INNER JOIN `foobar`';

        $this->assertEquals(
            $this->testObj,
            $this->testObj->newJoin($this->dbMock, $table, $type)
        );
        $this->assertAttributeEquals($expected, 'join', $this->testObj);
    }


    /**
     * @covers ::On
     * @depends testNewJoin
     */
    public function testOn()
    {
        $local = 'baz';
        $foreign = 'bat';
        $table = 'foobar';
        $expected = ' JOIN `foobar` ON `baz` = `bat`';

        $this->dbMock->expects($this->once())
                     ->Method('Append')
                     ->With($expected);

        $returnValue = $this->testObj->newJoin($this->dbMock, $table)
                                     ->On($local, $foreign);
        $this->assertEquals($returnValue, $this->dbMock);
    }


    /**
     * @covers ::Using
     * @depends testNewJoin
     */
    public function testUsing()
    {
        $column = 'baz';
        $table = 'foobar';
        $expected = ' JOIN `foobar` USING (`baz`)';

        $this->dbMock->expects($this->once())
                     ->Method('Append')
                     ->With($expected);

        $returnValue = $this->testObj->newJoin($this->dbMock, $table)
                                     ->Using($column);
        $this->assertEquals($returnValue, $this->dbMock);
    }
}
