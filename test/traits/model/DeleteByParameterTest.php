<?php
namespace rakelley\jhframe\test\traits\model;

use \rakelley\jhframe\test\helpers\PHPUnitUtil as Utility;

/**
 * @coversDefaultClass \rakelley\jhframe\traits\model\DeleteByParameter
 */
class DeleteByParameterTest extends
    \rakelley\jhframe\test\helpers\cases\ModelTrait
{
    protected $testedClass = '\rakelley\jhframe\traits\model\DeleteByParameter';


    public function caseProvider()
    {
        return [
            [//default to primary key
                null, null
            ],
            [//single column arg
                'fooColumn', null
            ],
            [//multiple column arg
                ['fooColumn', 'barColumn'], 'AND'
            ],
        ];
    }

    /**
     * @covers ::deleteByParameter
     * @dataProvider caseProvider
     */
    public function testDeleteByParameter($arg, $expectedOperator)
    {
        $primary = 'bazColumn';
        $parameters = ['foo' => 'bar', 'baz' => 'bat'];
        $table = 'foobar';

        $this->testObj->primary = $primary;
        $this->testObj->parameters = $parameters;
        $this->testObj->table = $table;

        $expectedKeys = ($arg) ?: $primary;

        $this->dbMock->expects($this->once())
                     ->method('newQuery')
                     ->with($this->identicalTo('delete'),
                            $this->identicalTo($table))
                     ->willReturn($this->dbMock);

        $this->whereMock->expects($this->once())
                        ->method('Equals')
                        ->with($this->identicalTo($expectedKeys),
                               $this->identicalTo($expectedOperator))
                        ->willReturn($this->dbMock);

        $this->stmntMock->expects($this->once())
                        ->method('Bind')
                        ->with($this->identicalTo($expectedKeys),
                               $this->identicalTo($parameters))
                        ->willReturn($this->stmntMock);
        $this->stmntMock->expects($this->once())
                        ->method('Execute');

        Utility::callMethod($this->testObj, 'deleteByParameter', [$arg]);
    }
}
