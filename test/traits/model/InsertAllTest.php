<?php
namespace rakelley\jhframe\test\traits\model;

use \rakelley\jhframe\test\helpers\PHPUnitUtil as Utility;

/**
 * @coversDefaultClass \rakelley\jhframe\traits\model\InsertAll
 */
class InsertAllTest extends
    \rakelley\jhframe\test\helpers\cases\ModelTrait
{
    protected $testedClass = '\rakelley\jhframe\traits\model\InsertAll';


    /**
     * @covers ::insertAll
     */
    public function testInsertAll()
    {
        $table = 'foobar';
        $columns = ['foobar', 'bazbat'];
        $values = ['foo', 'bar', 'baz'];

        $this->testObj->table = $table;
        $this->testObj->columns = $columns;

        $this->dbMock->expects($this->once())
                     ->method('newQuery')
                     ->with($this->identicalTo('insert'),
                            $this->identicalTo($table),
                            $this->identicalTo(['columns' => $columns]))
                     ->willReturn($this->dbMock);

        $this->stmntMock->expects($this->once())
                        ->method('Bind')
                        ->with($this->identicalTo($columns),
                               $this->identicalTo($values))
                        ->willReturn($this->stmntMock);
        $this->stmntMock->expects($this->once())
                        ->method('Execute');

        Utility::callMethod($this->testObj, 'insertAll', [$values]);
    }
}
