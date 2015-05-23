<?php
namespace rakelley\jhframe\test\traits\model;

use \rakelley\jhframe\test\helpers\PHPUnitUtil as Utility;

/**
 * @coversDefaultClass \rakelley\jhframe\traits\model\DeleteOnValues
 */
class DeleteOnValuesTest extends
    \rakelley\jhframe\test\helpers\cases\ModelTrait
{
    protected $testedClass = '\rakelley\jhframe\traits\model\DeleteOnValues';


    public function caseProvider()
    {
        return [
            [//default to primary key
                null
            ],
            [//explicit arg
                'fooColumn'
            ],
        ];
    }


    /**
     * @covers ::deleteOnValues
     * @dataProvider caseProvider
     */
    public function testDeleteOnValues($arg)
    {
        $primary = 'bazColumn';
        $table = 'foobar';

        $this->testObj->primary = $primary;
        $this->testObj->table = $table;

        $values = ['foo', 'bar', 'baz'];
        $expectedCol = ($arg) ?: $primary;

        $this->dbMock->expects($this->once())
                     ->method('newQuery')
                     ->with($this->identicalTo('delete'),
                            $this->identicalTo($table))
                     ->willReturn($this->dbMock);

        $this->whereMock->expects($this->once())
                        ->method('In')
                        ->with($this->identicalTo($expectedCol),
                               $this->identicalTo($values))
                        ->willReturn($this->dbMock);
        $this->stmntMock->expects($this->once())
                        ->method('Execute')
                        ->with($this->identicalTo($values));

        Utility::callMethod($this->testObj, 'deleteOnValues', [$values, $arg]);
    }
}
