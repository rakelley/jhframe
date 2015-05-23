<?php
namespace rakelley\jhframe\test\traits;

use \rakelley\jhframe\test\helpers\PHPUnitUtil as Utility;

/**
 * @coversDefaultClass \rakelley\jhframe\traits\PickRandomArrayElements
 */
class PickRandomArrayElementsTest extends
    \rakelley\jhframe\test\helpers\cases\SimpleTrait
{
    protected $testedClass = '\rakelley\jhframe\traits\PickRandomArrayElements';


    /**
     * Normal Case, expected result is $count number of items randomly picked
     * from $array
     * 
     * @covers ::pickRandomArrayElements
     */
    public function testPickRandomArrayElements()
    {
        $array = ['foo', 'bar', 'baz', 'bat', 'flan'];
        $count = 3;

        $result = Utility::callMethod($this->testObj,
                                      'pickRandomArrayElements',
                                      [$array, $count]);

        $this->assertEquals($count, count($result));
        array_walk(
            $result,
            function($item) use ($array) {
                $this->assertTrue(in_array($item, $array));
            }
        );
    }

    /**
     * $array is smaller than $count, expected result is randomized version
     * of $array
     * 
     * @covers ::pickRandomArrayElements
     */
    public function testPickRandomArrayElementsTooFew()
    {
        $array = ['foo', 'bar', 'baz', 'bat', 'flan'];
        $count = 30;

        $result = Utility::callMethod($this->testObj,
                                      'pickRandomArrayElements',
                                      [$array, $count]);

        $this->assertEquals(count($array), count($result));
        array_walk(
            $array,
            function($item) use ($result) {
                $this->assertTrue(in_array($item, $result));
            }
        );
    }

    /**
     * $array is empty, expected result is null
     * 
     * @covers ::pickRandomArrayElements
     */
    public function testPickRandomArrayElementsEmpty()
    {
        $array = [];
        $count = 3;

        $result = Utility::callMethod($this->testObj,
                                      'pickRandomArrayElements',
                                      [$array, $count]);

        $this->assertEquals(null, $result);
    }
}
