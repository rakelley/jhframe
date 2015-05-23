<?php
namespace rakelley\jhframe\test\traits;

class SingletonTestDummy
{
    use \rakelley\jhframe\traits\Singleton;

    public $parameters = null;
}


/**
 * @coversDefaultClass \rakelley\jhframe\traits\Singleton
 */
class SingletonTest extends \PHPUnit_Framework_TestCase
{
    protected $dummyClass = '\rakelley\jhframe\test\traits\SingletonTestDummy';


    /**
     * @coversNothing
     */
    public function testInstanceStartsNull()
    {
        $this->assertAttributeEmpty('instance', $this->dummyClass);
    }


    /**
     * @covers ::getInstance
     */
    public function testGetInstance()
    {
        $class = $this->dummyClass;

        $instance = $class::getInstance();
        $this->assertTrue($instance instanceof $class);
        $this->assertAttributeEquals($instance, 'instance', $this->dummyClass);
    }

    /**
     * @covers ::getInstance
     */
    public function testGetInstanceIsSame()
    {
        $class = $this->dummyClass;
        $parameters = ['foo' => 'bar', 'baz' => 'bat'];

        $instance = $class::getInstance();
        $instance->parameters = $parameters;

        $secondInstance = $class::getInstance();
        $this->assertEquals($instance, $secondInstance);
    }
}
