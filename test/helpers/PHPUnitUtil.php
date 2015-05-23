<?php
namespace rakelley\jhframe\test\helpers;

/**
 * Generic utility functions for test writing
 */
class PHPUnitUtil
{

    /**
     * Call an object's constructor
     * 
     * @param  object $object Class instance to call constructor on
     * @param  array  $args   Args for constructor
     * @return void
     */
    public static function callConstructor($object, $args=[])
    {
        $reflect = new \ReflectionClass($object);
        $constructor = $reflect->getConstructor();

        if ($args) {
            $constructor->invokeArgs($object, $args);
        } else {
            $constructor->invoke($object);
        }
    }


    /**
     * Call private or protected method
     * 
     * @param  mixed  $classOrObj Object or static class which contains method
     * @param  string $name       Method name
     * @param  array  $args       Arguments for method
     * @return mixed
     */
    public static function callMethod($classOrObj, $name, $args=[])
    {
        $class = new \ReflectionClass($classOrObj);
        $method = $class->getMethod($name);
        $method->setAccessible(true);

        return $method->invokeArgs($classOrObj, $args);
    }


    /**
     * Set private, protected, or non-existant properties on object
     * 
     * @param  array  $properties Keys are property names, values are property
     *                            values
     * @param  object $object
     * @return void
     */
    public static function setProperties(array $properties, $object)
    {
        $class = get_class($object);
        array_walk(
            $properties,
            function($value, $key) use ($class, $object) {
                try {
                    $prop = new \ReflectionProperty($class, $key);
                    $prop->setAccessible(true);
                    $prop->setValue($object, $value);
                } catch (\ReflectionException $e) {
                    $object->$key = $value;
                }
            }
        );
    }
}
