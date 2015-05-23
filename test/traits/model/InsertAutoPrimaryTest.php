<?php
namespace rakelley\jhframe\test\traits\model;

use \rakelley\jhframe\test\helpers\PHPUnitUtil as Utility;

/**
 * @coversDefaultClass \rakelley\jhframe\traits\model\InsertAutoPrimary
 */
class InsertAutoPrimaryTest extends
    \rakelley\jhframe\test\helpers\cases\ModelTrait
{
    protected $testedClass = '\rakelley\jhframe\traits\model\InsertAutoPrimary';


    public function caseProvider()
    {
        return [
            [//single primary key
                'foo'
            ],
            [//compound primary key
                ['foo', 'bar']
            ],
        ];
    }

    /**
     * @covers ::insertAutoPrimary
     * @dataProvider caseProvider
     */
    public function testInsertAutoPrimary($primary)
    {
        $columns = ['foo', 'bar', 'baz', 'bat'];
        $expected = array_values(array_diff($columns, (array) $primary));
        $values = ['lorem', 'ipsum'];
        $table = 'foobar';

        $this->testObj->primary = $primary;
        $this->testObj->columns = $columns;
        $this->testObj->table = $table;

        $this->dbMock->expects($this->once())
                     ->method('newQuery')
                     ->with($this->identicalTo('insert'),
                            $this->identicalTo($table),
                            $this->identicalTo(['columns' => $expected]))
                     ->willReturn($this->dbMock);

        $this->stmntMock->expects($this->once())
                        ->method('Bind')
                        ->with($this->identicalTo($expected),
                               $this->identicalTo($values))
                        ->willReturn($this->stmntMock);
        $this->stmntMock->expects($this->once())
                        ->method('Execute');

        Utility::callMethod($this->testObj, 'insertAutoPrimary', [$values]);
    }
}
