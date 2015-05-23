<?php
namespace rakelley\jhframe\test\traits\model;

use \rakelley\jhframe\test\helpers\PHPUnitUtil as Utility;

/**
 * @coversDefaultClass \rakelley\jhframe\traits\model\UpdateByPrimary
 */
class UpdateByPrimaryTest extends
    \rakelley\jhframe\test\helpers\cases\ModelTrait
{
    protected $testedClass = '\rakelley\jhframe\traits\model\UpdateByPrimary';


    public function caseProvider()
    {
        return [
            [//single primary key
                'foo', null
            ],
            [//compound primary key
                ['foo', 'bar'], 'AND'
            ],
        ];
    }


    /**
     * @covers ::updateByPrimary
     * @dataProvider caseProvider
     */
    public function testUpdateByPrimary($primary, $expectedOperator)
    {
        $columns = ['foo', 'bar', 'baz', 'bat'];
        $table = 'foobar';
        $expectedCols = array_values(array_diff($columns, (array) $primary));
        $values = ['lorem', 'ipsum', 'dolor'];

        $this->testObj->primary = $primary;
        $this->testObj->columns = $columns;
        $this->testObj->table = $table;

        $this->dbMock->expects($this->once())
                     ->method('newQuery')
                     ->with($this->identicalTo('update'),
                            $this->identicalTo($table),
                            $this->identicalTo(['columns' => $expectedCols]))
                     ->willReturn($this->dbMock);

        $this->whereMock->expects($this->once())
                        ->method('Equals')
                        ->with($this->identicalTo($primary),
                               $this->identicalTo($expectedOperator))
                        ->willReturn($this->dbMock);

        $this->stmntMock->expects($this->once())
                        ->method('Bind')
                        ->with($this->identicalTo($columns),
                               $this->identicalTo($values))
                        ->willReturn($this->stmntMock);
        $this->stmntMock->expects($this->once())
                        ->method('Execute');

        Utility::callMethod($this->testObj, 'updateByPrimary', [$values]);
    }
}
