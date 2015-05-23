<?php
namespace rakelley\jhframe\test\traits\model;

use \rakelley\jhframe\test\helpers\PHPUnitUtil as Utility;

/**
 * @coversDefaultClass \rakelley\jhframe\traits\model\SelectAll
 */
class SelectAllTest extends
    \rakelley\jhframe\test\helpers\cases\ModelTrait
{
    protected $testedClass = '\rakelley\jhframe\traits\model\SelectAll';


    public function resultProvider()
    {
        return [
            [//results case
                ['foo', 'bar', 'baz']
            ],
            [//no results case
                []
            ],
        ];
    }


    /**
     * @covers ::selectAll
     * @dataProvider resultProvider
     */
    public function testSelectAll($fetched)
    {
        $expected = ($fetched) ?: null;

        $table = 'foobar';
        $this->testObj->table = $table;
        $this->dbMock->expects($this->once())
                     ->method('newQuery')
                     ->with($this->identicalTo('select'),
                            $this->identicalTo($table))
                     ->willReturn($this->dbMock);

        $this->stmntMock->expects($this->once())
                        ->method('FetchAll')
                        ->willReturn($fetched);

        $this->assertEquals(
            $expected,
            Utility::callMethod($this->testObj, 'selectAll')
        );
    }
}
