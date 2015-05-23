<?php
namespace rakelley\jhframe\test\helpers\cases;

/**
 * Test Case for traits which don't require complex setup
 */
class SimpleTrait extends Base
{

    protected function setUp()
    {
        $this->testObj = $this->getMockForTrait($this->testedClass);
    }
}
