<?php
namespace rakelley\jhframe\test\classes;

/**
 * @coversDefaultClass \rakelley\jhframe\classes\Model
 */
class ModelTest extends \rakelley\jhframe\test\helpers\cases\Base
{
    protected $dbMock;


    protected function setUp()
    {
        $dbInterface = '\rakelley\jhframe\interfaces\services\IDatabase';
        $testedClass = '\rakelley\jhframe\classes\Model';

        $this->dbMock = $this->getMock($dbInterface);

        $this->testObj = $this->getMockBuilder($testedClass)
                              ->setConstructorArgs([$this->dbMock])
                              ->setMethods(null)
                              ->getMockForAbstractClass();
    }


    /**
     * @covers ::__construct
     */
    public function testConstruct()
    {
        $this->assertAttributeEquals($this->dbMock, 'db', $this->testObj);
    }
}
