<?php
namespace rakelley\jhframe\test\traits\model;

use \rakelley\jhframe\test\helpers\PHPUnitUtil as Utility;

/**
 * @coversDefaultClass \rakelley\jhframe\traits\model\GetCount
 */
class GetCountTest extends
    \rakelley\jhframe\test\helpers\cases\ModelTrait
{
    protected $testedClass = '\rakelley\jhframe\traits\model\GetCount';


    /**
     * @covers ::getCount
     */
    public function testGetCount()
    {
        $count = 10;
        $table = 'foobar';

        $pdoStmntMock = $this->getMockBuilder('\stdClass')
                             ->setMethods(['fetchColumn'])
                             ->getMock();
        $pdoStmntMock->expects($this->once())
                     ->method('fetchColumn')
                     ->willReturn($count);

        $this->testObj->table = $table;

        $this->dbMock->expects($this->once())
                     ->method('newQuery')
                     ->with($this->identicalTo('select'),
                            $this->identicalTo($table),
                            $this->identicalTo(['select' => 'count(*)']))
                     ->willReturn($this->dbMock);
        $this->dbMock->expects($this->once())
                     ->method('stripTicks')
                     ->willReturn($this->dbMock);

        $this->stmntMock->expects($this->once())
                        ->method('Execute')
                        ->willReturn($this->stmntMock);
        $this->stmntMock->expects($this->once())
                     ->method('returnStatement')
                     ->willReturn($pdoStmntMock);

        $this->assertEquals(
            $count,
            Utility::callMethod($this->testObj, 'getCount')
        );
    }
}
