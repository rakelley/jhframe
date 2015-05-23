<?php
namespace rakelley\jhframe\test\traits\model;

use \rakelley\jhframe\test\helpers\PHPUnitUtil as Utility;

/**
 * @coversDefaultClass \rakelley\jhframe\traits\model\UpdateSingleByPrimaryParameter
 */
class UpdateSingleByPrimaryParameterTest extends
    \rakelley\jhframe\test\helpers\cases\ModelTrait
{
    protected $testedClass =
        '\rakelley\jhframe\traits\model\UpdateSingleByPrimaryParameter';


    /**
     * @covers ::updateSingleByPrimaryParameter
     */
    public function testUpdateSingleByPrimaryParameter()
    {
        $table = 'foobar';
        $primary = 'foo';
        $parameters = ['foo' => 'bar'];
        $this->testObj->table = $table;
        $this->testObj->primary = $primary;
        $this->testObj->parameters = $parameters;

        $columnArg = 'bazColumn';
        $valueArg = 'lorem ipsum';
        $expected = [
            $columnArg => $valueArg,
            $primary => $parameters[$primary]
        ];

        $this->dbMock->expects($this->once())
                     ->method('newQuery')
                     ->with($this->identicalTo('update'),
                            $this->identicalTo($table),
                            $this->identicalTo(['columns' => [$columnArg]]))
                     ->willReturn($this->dbMock);

        $this->whereMock->expects($this->once())
                        ->method('Equals')
                        ->with($this->identicalTo($primary))
                        ->willReturn($this->dbMock);

        $this->stmntMock->expects($this->once())
                        ->method('Bind')
                        ->with($this->identicalTo(array_keys($expected)),
                               $this->identicalTo($expected))
                        ->willReturn($this->stmntMock);

        $this->stmntMock->expects($this->once())
                        ->method('Execute');

        Utility::callMethod($this->testObj, 'updateSingleByPrimaryParameter',
                            [$columnArg, $valueArg]);
    }
}
