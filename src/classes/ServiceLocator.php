<?php
/**
 * @package jhframe
 * @license http://opensource.org/licenses/MIT The MIT License
 * @author Ryan Kelley
 * @copyright 2011-2015 Jakked Hardcore Gym
 */

namespace rakelley\jhframe\classes;

/**
 * Default implementation of IServiceLocator
 */
class ServiceLocator implements
    \rakelley\jhframe\interfaces\services\IServiceLocator
{
    /**
     * Object storage container
     * @var array
     */
    protected $container = [];
    /**
     * Interface for special-case Factory classes
     * @var string
     */
    protected $factoryInterface = '\rakelley\jhframe\interfaces\IFactory';
    /**
     * Interface for special-case Singleton classes
     * @var string
     */
    protected $singletonInterface = '\rakelley\jhframe\interfaces\ISingleton';
    /**
     * Class resolution table, keys and values are both string class names.
     * All dependencies on key will resolve to value.
     * @var array
     */
    protected $resolved = [];


    /**
     * @see \rakelley\jhframe\interfaces\IServiceLocator::Make
     */
    public function Make($key)
    {
        if (!isset($this->container[$key])) {
            $class = $this->Resolve($key);
            if (!$class) {
                throw new \RuntimeException(
                    'Unable to Resolve Class for Key: ' . $key,
                    500
                );
            }
            $this->Store([$key => $this->getClassInstance($class)]);
        }

        return $this->container[$key];
    }


    /**
     * @see \rakelley\jhframe\interfaces\IServiceLocator::Resolve
     */
    public function Resolve($key)
    {
        //strip leading slashes
        if (strpos($key, '\\') === 0) {
            $key = substr($key, 1);
        }

        if (!isset($this->resolved[$key])) {
            if (class_exists($key)) {
                $this->resolved[$key] = $key;
            } else {
                $this->resolved[$key] = false;
            }
        }

        return $this->resolved[$key];
    }


    /**
     * @see \rakelley\jhframe\interfaces\IServiceLocator::Override
     */
    public function Override(array $overrides)
    {
        $this->resolved = array_merge($this->resolved, $overrides);

        if ($this->container) {
            array_walk(
                $overrides,
                function($value, $key) {
                    unset($this->container[$key]);
                }
            );
        }
    }


    /**
     * @see \rakelley\jhframe\interfaces\IServiceLocator::Reset
     */
    public function Reset()
    {
        $this->resolved = [];
        $this->container = [];
    }


    /**
     * Stores class objects and associates key with them
     * 
     * @param  array $objects Keys are class or interface key to associate with
     *                        class instance, value is class object
     * @return void
     */
    public function Store(array $objects)
    {
        $this->container = array_merge($this->container, $objects);
    }


    /**
     * @see \rakelley\jhframe\interfaces\IServiceLocator::getNew
     */
    public function getNew($existing)
    {
        if (is_object($existing)) {
            $class = get_class($existing);
        } elseif (is_string($existing)) {
            $class = $this->Resolve($existing);
            if (!$class) {
                throw new \RuntimeException(
                    'Unable to Resolve Class for Key: ' . $existing,
                    500
                );
            }
        }

        return $this->getClassInstance($class);
    }


    /**
     * Creates class object as appropriate for class type, injecting
     * dependencies into class constructor if appropriate.
     * 
     * @param  string $class Qualified name of class
     * @return object
     * @throws \ReflectionException if $class doesn't exist
     */
    protected function getClassInstance($class)
    {
        $reflect = new \ReflectionClass($class);

        if ($reflect->implementsInterface($this->singletonInterface)) {
            //Singletons
            $object = $class::getInstance();
        } elseif ($reflect->isInstantiable() && $reflect->getConstructor()) {
            //Classes with constructor dependencies
            $args = array_map(
                function($param) {
                    return $this->Make($param->getClass()->name);
                },
                $reflect->getConstructor()->getParameters()
            );
            $object = $reflect->newInstanceArgs($args);
        } else {
            //Classes without constructor dependencies
            $object = new $class;
        }

        // If class is a factory, get produced object instead
        if ($reflect->implementsInterface($this->factoryInterface)) {
            $object = $object->getProduct();
        }

        return $object;
    }
}
