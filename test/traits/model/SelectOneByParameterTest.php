<?php
namespace rakelley\jhframe\test\traits\model;

use \rakelley\jhframe\test\helpers\PHPUnitUtil as Utility;

/**
 * @coversDefaultClass \rakelley\jhframe\traits\model\SelectOneByParameter
 */
class SelectOneByParameterTest extends
    \rakelley\jhframe\test\helpers\cases\ModelTrait
{
    protected $testedClass =
        '\rakelley\jhframe\traits\model\SelectOneByParameter';


    public function caseProvider()
    {
        return [
            [//no arg
                null, null, ['bar', 'baz']
            ],
            [//string arg
                'bazColumn', null, ['bar', 'baz']
            ],
            [//array arg
                ['bazColumn', 'burzColumn', 'flanColumn'], 'AND', ['bar', 'baz']
            ],
            [//no result
                null, null, []
            ],
        ];
    }

    /**
     * @covers ::selectOneByParameter
     * @dataProvider caseProvider
     */
    public function testSelectOneByParameter($methodArg, $expectedOperator,
                                             $fetched)
    {
        $primary = 'bazColumn';
        $parameters = ['foo' => 'bar', 'baz' => 'bat'];
        $table = 'foobar';
        $this->testObj->primary = $primary;
        $this->testObj->parameters = $parameters;
        $this->testObj->table = $table;

        $expectedKeys = ($methodArg) ?: $primary;
        $expectedResult = ($fetched) ?: null;


        $this->dbMock->expects($this->once())
                     ->method('newQuery')
                     ->with($this->identicalTo('select'),
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
                        ->method('Fetch')
                        ->willReturn($fetched);

        $this->assertEquals(
            $expectedResult,
            Utility::callMethod($this->testObj, 'selectOneByParameter',
                                [$methodArg])
        );
    }
}
