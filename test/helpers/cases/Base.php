<?php
namespace rakelley\jhframe\test\helpers\cases;

/**
 * Base testcase for all JHFrame and Jakkedweb tests
 */
class Base extends \PHPUnit_Framework_TestCase
{
    /**
     * Name of class to be tested, for use with default setUp implementation
     * @var string
     */
    protected $testedClass = '';
    /**
     * Instance of class being tested or mock of same, should be set by setUp
     * @var object
     */
    protected $testObj;


    protected function setUp()
    {
        $class = $this->testedClass;
        $this->testObj = new $class;
    }
}
