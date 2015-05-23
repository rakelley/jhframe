<?php
namespace rakelley\jhframe\test\traits;

/**
 * @coversDefaultClass \rakelley\jhframe\traits\GetCalledNamespace
 */
class GetCalledNamespaceTest extends
    \rakelley\jhframe\test\helpers\cases\SimpleTrait
{
    protected $testedClass = '\rakelley\jhframe\traits\GetCalledNamespace';


    /**
     * @covers ::getCalledNamespace
     */
    public function testGetCalledNamespace()
    {
        $reflect = new \ReflectionClass($this->testObj);
        $namespace = $reflect->getNamespaceName();

        $this->assertEquals($namespace, $this->testObj->getCalledNamespace());
    }
}
