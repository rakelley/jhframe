<?php
namespace rakelley\jhframe\test\helpers\traits;

/**
 * Helper trait for tests which need to mock the Database service
 */
trait MockDatabaseService
{
    protected $dbMock;
    protected $stmntMock;
    protected $whereMock;
    protected $joinMock;


    protected function setUpDbMock()
    {
        $dbInterface = '\rakelley\jhframe\interfaces\services\IDatabase';
        $stmntInterface =
            '\rakelley\jhframe\interfaces\services\IStatementAbstractor';
        $whereInterface =
            '\rakelley\jhframe\interfaces\services\IDatabaseWhereBuilder';
        $joinInterface =
            '\rakelley\jhframe\interfaces\services\IDatabaseJoinBuilder';

        $this->stmntMock = $this->getMock($stmntInterface);

        $this->whereMock = $this->getMock($whereInterface);

        $this->joinMock = $this->getMock($joinInterface);

        $this->dbMock = $this->getMock($dbInterface);
        $this->dbMock->method('addWhere')
                     ->willReturn($this->whereMock);
        $this->dbMock->method('addJoin')
                     ->willReturn($this->joinMock);
        $this->dbMock->method('makeStatement')
                     ->willReturn($this->stmntMock);
    }
}
