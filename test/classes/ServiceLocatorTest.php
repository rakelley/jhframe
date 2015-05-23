<?php
namespace rakelley\jhframe\test\classes;

use \rakelley\jhframe\test\helpers\PHPUnitUtil as Utility;

class ServiceLocatorTestDummy
{

}

class ServiceLocatorTestDummyWithDep
{
    protected $injectedService;

    function __construct(
        \rakelley\jhframe\test\classes\ServiceLocatorTestDummy $dummy
    ) {
        $this->injectedService = $dummy;
    }

    public function getInjectedService()
    {
        return $this->injectedService;
    }
}

class ServiceLocatorTestDummySingleton implements
    \rakelley\jhframe\interfaces\ISingleton
{
    protected static $instance = null;

    public static function getInstance()
    {
        static::$instance = new static;
        return static::$instance;
    }
}

class ServiceLocatorTestDummyFactory implements
    \rakelley\jhframe\interfaces\IFactory
{
    public function getProduct()
    {
        return new ServiceLocatorTestDummy;
    }
}

/**
 * @coversDefaultClass \rakelley\jhframe\classes\ServiceLocator
 */
class ServiceLocatorTest extends \rakelley\jhframe\test\helpers\cases\Base
{
    protected $testedClass = '\rakelley\jhframe\classes\ServiceLocator';


    /**
     * @covers ::Resolve
     */
    public function testResolve()
    {
        $class = get_class($this);

        $this->assertEquals($class, $this->testObj->Resolve($class));
    }

    /**
     * @covers ::Resolve
     * @depends testResolve
     */
    public function testResolveNotExists()
    {
        $class = '\ThisClassDoesNotExist';

        $this->assertEquals(false, $this->testObj->Resolve($class));
    }


    /**
     * @covers ::Override
     * @depends testResolve
     * @depends testResolveNotExists
     */
    public function testOverride()
    {
        $keyOne = get_class($this);
        $valueOne = 'foo';
        $keyTwo = 'baz';
        $valueTwo = 'bat';
        $overrides = [$keyOne => $valueOne, $keyTwo => $valueTwo];

        $this->testObj->Override($overrides);

        $this->assertEquals($valueOne, $this->testObj->Resolve($keyOne));
        $this->assertEquals($valueTwo, $this->testObj->Resolve($keyTwo));
    }


    /**
     * @covers ::Make
     * @covers ::getClassInstance
     * @depends testResolve
     */
    public function testMake()
    {
        $class = __NAMESPACE__ . '\ServiceLocatorTestDummy';

        $this->assertTrue($this->testObj->Make($class) instanceof $class);
    }

    /**
     * Case of repeated Make calls, expected to return same object
     * 
     * @covers ::Make
     * @depends testMake
     */
    public function testMakeRepeated()
    {
        $class = __NAMESPACE__ . '\ServiceLocatorTestDummy';
        $resultOne = $this->testObj->Make($class);
        $resultTwo = $this->testObj->Make($class);

        $this->assertTrue($resultOne === $resultTwo);
    }

    /**
     * Covers Make with an Overriden resolution
     *
     * @covers ::Make
     * @covers ::getClassInstance
     * @depends testOverride
     * @depends testMake
     */
    public function testMakeInjectedResolution()
    {
        $key = 'foobar';
        $class = __NAMESPACE__ . '\ServiceLocatorTestDummy';

        $this->testObj->Override([$key => $class]);

        $result = $this->testObj->Make($key);
        $this->assertTrue($result instanceof $class);
    }

    /**
     * Case of Make with invalid key
     * 
     * @covers ::Make
     * @depends testMake
     * @depends testResolveNotExists
     */
    public function testMakeInvalid()
    {
        $class = '\ThisClassDoesNotExist';

        $this->setExpectedException('\RuntimeException');

        $this->testObj->Make($class);
    }

    /**
     * Case of Make with class with constructor depenendencies to inject
     *
     * @covers ::Make
     * @covers ::getClassInstance
     * @depends testMake
     */
    public function testMakeWithConstructorDependencies()
    {
        $class = __NAMESPACE__ . '\ServiceLocatorTestDummyWithDep';
        $serviceClass = __NAMESPACE__ . '\ServiceLocatorTestDummy';

        $result = $this->testObj->Make($class);
        $service = $result->getInjectedService();
        $this->assertTrue($result instanceof $class);
        $this->assertTrue($service instanceof $serviceClass);
    }

    /**
     * Case of Make with an ISingleton class
     *
     * @covers ::make
     * @covers ::getClassInstance
     * @depends testMake
     */
    public function testMakeSingleton()
    {
        $class = __NAMESPACE__ . '\ServiceLocatorTestDummySingleton';

        $result = $this->testObj->Make($class);
        $this->assertTrue($result instanceof $class);
        $this->assertAttributeEquals($result, 'instance', $result);
    }

    /**
     * Case of Make with an IFactory class
     *
     * @covers ::make
     * @covers ::getClassInstance
     * @depends testMake
     */
    public function testMakeFactory()
    {
        $class = __NAMESPACE__ . '\ServiceLocatorTestDummyFactory';

        $result = $this->testObj->Make($class);
        $this->assertTrue(is_object($result));
        $this->assertFalse($result instanceof $class);
    }


    /**
     * Case of Make call on stored key, Expected to return stored object
     * 
     * @covers ::Store
     * @depends testMakeRepeated
     */
    public function testStore()
    {
        $keyOne = 'foobar';
        $objectOne = new ServiceLocatorTestDummy;
        $keyTwo = 'bazbat';
        $objectTwo = new ServiceLocatorTestDummy;
        $stores = [$keyOne => $objectOne, $keyTwo => $objectTwo];

        $this->testObj->Store($stores);
        $this->assertEquals($objectOne, $this->testObj->Make($keyOne));
        $this->assertEquals($objectTwo, $this->testObj->Make($keyTwo));


        $objectThree = new ServiceLocatorTestDummy;

        $this->testObj->Store([$keyOne => $objectThree]);
        $this->assertEquals($objectThree, $this->testObj->Make($keyOne));
    }


    /**
     * Ensures Override is unsetting stored objects when their resolved key
     * changes
     * 
     * @covers ::Override
     * @depends testOverride
     * @depends testStore
     */
    public function testOverrideWithStore()
    {
        $keyOne = 'foobar';
        $objectOne = new ServiceLocatorTestDummy;
        $keyTwo = 'bazbat';
        $objectTwo = new ServiceLocatorTestDummy;
        $stores = [$keyOne => $objectOne, $keyTwo => $objectTwo];
        $overrides = [
            $keyOne => __NAMESPACE__ . '\ServiceLocatorTestDummyWithDep'
        ];

        $this->testObj->Store($stores);
        $this->testObj->Override($overrides);

        $this->assertNotEquals($objectOne, $this->testObj->Make($keyOne));
        $this->assertEquals($objectTwo, $this->testObj->Make($keyTwo));
    }


    /**
     * @covers ::Reset
     * @depends testOverride
     * @depends testStore
     * @depends testResolveNotExists
     * @depends testMakeInvalid
     */
    public function testReset()
    {
        $this->testObj->Override(['foo' => 'bar']);
        $this->testObj->Store(['baz' => new ServiceLocatorTestDummy]);

        $this->testObj->Reset();

        $this->assertFalse($this->testObj->Resolve('foo'));
        $this->setExpectedException('\RuntimeException');
        $this->testObj->Make('baz');
    }


    /**
     * @covers ::getNew
     * @covers ::getClassInstance
     * @depends testMake
     */
    public function testGetNew()
    {
        $class = '\stdClass';
        $existing = $this->testObj->Make($class);
        $existing->genericProperty = 'foobar';

        $cloneByObject = $this->testObj->getNew($existing);
        $this->assertTrue($cloneByObject instanceof $class);
        $this->assertNotEquals($existing, $cloneByObject);

        $cloneByObject->genericProperty = 'bazbat';       
        $cloneByClass = $this->testObj->getNew($class);
        $this->assertTrue($cloneByClass instanceof $class);
        $this->assertNotEquals($existing, $cloneByClass);
        $this->assertNotEquals($cloneByObject, $cloneByClass);
    }

    /**
     * @covers ::getNew
     * @depends testGetNew
     * @depends testResolveNotExists
     * @depends testMakeInvalid
     */
    public function testGetNewInvalid()
    {
        $class = '\ThisClassDoesNotExist';

        $this->setExpectedException('\RuntimeException');
        $this->testObj->getNew($class);
    }
}
