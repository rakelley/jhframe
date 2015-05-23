<?php
namespace rakelley\jhframe\test\classes;

use \rakelley\jhframe\test\helpers\PHPUnitUtil as Utility;

/**
 * Can't mock \PDO directly so have to use dummy class with empty
 * constructor to get matching signature for testConstruct
 */
class StatementAbstractorTestDummy extends \PDO
{
    function __construct()
    {

    }
}

/**
 * @coversDefaultClass \rakelley\jhframe\classes\StatementAbstractor
 */
class StatementAbstractorTest extends \rakelley\jhframe\test\helpers\cases\Base
{
    protected $statementMock;
    protected $connectionMock;


    protected function setUp()
    {
        $connectionClass = '\stdClass'; //can't mock \PDO directly
        $statementClass = '\stdClass'; //can't mock \PDOStatement directly
        $testedClass = '\rakelley\jhframe\classes\StatementAbstractor';

        $this->testObj = $this->getMockBuilder($testedClass)
                                 ->disableOriginalConstructor()
                                 ->setMethods(null)
                                 ->getMock();

        $this->statementMock = $this->getMockBuilder($statementClass)
                                    ->disableOriginalConstructor()
                                    ->setMethods(['bindValue', 'execute',
                                                  'fetch', 'fetchAll'])
                                    ->getMock();

        $this->connectionMock = $this->getMockBuilder($connectionClass)
                                     ->disableOriginalConstructor()
                                     ->setMethods(['prepare'])
                                     ->getMock();

        $properties = [
            'connection' => $this->connectionMock,
            'stmnt' => $this->statementMock,
        ];
        Utility::setProperties($properties, $this->testObj);
    }


    public function executeArgProvider()
    {
        return [
            [//without arg
                null
            ],
            [//with arg
                ['foo', 'bar', 3]
            ],
        ];
    }


    public function bindCaseProvider()
    {
        return [
            [//string
                'lorem ipsum', \PDO::PARAM_STR
            ],
            [//int
                3, \PDO::PARAM_INT
            ],
            [//null
                null, \PDO::PARAM_INT
            ],
        ];
    }


    /**
     * @covers ::__construct
     *
     * setUp construction bypasses constructor because \PDO mock needs to have
     * mockable methods
     */
    public function testConstruct()
    {
        $pdoMock = new StatementAbstractorTestDummy;

        Utility::callConstructor($this->testObj, [$pdoMock]);
        $this->assertAttributeEquals($pdoMock, 'connection', $this->testObj);
    }


    /**
     * @covers ::makeStatement
     */
    public function testMakeStatement()
    {
        $query = 'SELECT `foo` FROM `bar`';

        $this->connectionMock->expects($this->once())
                             ->method('prepare')
                             ->With($this->identicalTo($query))
                             ->willReturn($this->statementMock);

        $returnValue = $this->testObj->makeStatement($query);

        $this->assertEquals($returnValue, $this->testObj);
        $this->assertAttributeEquals($this->statementMock, 'stmnt',
                                     $this->testObj);
    }


    /**
     * @covers ::returnStatement
     */
    public function testReturnStatement()
    {
        $this->assertEquals($this->testObj->returnStatement(),
                            $this->statementMock);
    }


    /**
     * @covers ::Bind
     * @dataProvider bindCaseProvider
     */
    public function testBindSingle($value, $type)
    {
        $key = 'foobar';
        $placeholder = ':foobar';
        $values = ['barfoo' => 'baz'];
        if ($value) {
            $values[$key] = $value;
        }

        $this->statementMock->expects($this->once())
                            ->method('bindValue')
                            ->With($this->identicalTo($placeholder),
                                   $this->identicalTo($value),
                                   $this->identicalTo($type));

        $this->assertEquals($this->testObj,
                            $this->testObj->Bind($key, $values));
    }


    /**
     * @covers ::Bind
     * @depends testBindSingle
     */
    public function testBindMultiple()
    {
        $keys = ['foobar', 'barfoo', 'barbaz'];
        $placeholders = [':foobar', ':barfoo', ':barbaz'];
        $values = ['foobar' => 3, 'barfoo' => 'baz'];

        $this->statementMock->expects($this->at(0))
                            ->method('bindValue')
                            ->With($this->identicalTo($placeholders[0]),
                                   $this->identicalTo($values[$keys[0]]),
                                   $this->identicalTo(\PDO::PARAM_INT));
        $this->statementMock->expects($this->at(1))
                            ->method('bindValue')
                            ->With($this->identicalTo($placeholders[1]),
                                   $this->identicalTo($values[$keys[1]]),
                                   $this->identicalTo(\PDO::PARAM_STR));
        $this->statementMock->expects($this->at(2))
                            ->method('bindValue')
                            ->With($this->identicalTo($placeholders[2]),
                                   $this->identicalTo(null),
                                   $this->identicalTo(\PDO::PARAM_INT));

        $this->assertEquals($this->testObj,
                            $this->testObj->Bind($keys, $values));
    }


    /**
     * @covers ::Execute
     * @dataProvider executeArgProvider
     */
    public function testExecute($arg)
    {
        $this->statementMock->expects($this->once())
                            ->method('execute')
                            ->With($this->identicalTo($arg));

        $this->assertEquals($this->testObj, $this->testObj->Execute($arg));
    }


    /**
     * @covers ::Fetch
     * @depends testExecute
     * @dataProvider executeArgProvider
     */
    public function testFetch($arg)
    {
        $row = ['foo' => 'bar', 'baz' => 'bat'];
        $this->statementMock->expects($this->once())
                            ->method('execute')
                            ->With($this->identicalTo($arg));
        $this->statementMock->expects($this->once())
                            ->method('fetch')
                            ->willReturn($row);

        $this->assertEquals($row, $this->testObj->Fetch($arg));
    }


    /**
     * @covers ::FetchAll
     * @depends testExecute
     * @dataProvider executeArgProvider
     */
    public function testFetchAll($arg)
    {
        $rows = [
            ['foo' => 'bar', 'baz' => 'bat'],
            ['foo' => 'burzum', 'baz' => 'flarn'],
        ];
        $this->statementMock->expects($this->once())
                            ->method('execute')
                            ->With($this->identicalTo($arg));
        $this->statementMock->expects($this->once())
                            ->method('fetchAll')
                            ->willReturn($rows);

        $this->assertEquals($rows, $this->testObj->FetchAll($arg));
    }
}
