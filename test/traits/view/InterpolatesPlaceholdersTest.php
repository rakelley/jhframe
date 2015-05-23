<?php
namespace rakelley\jhframe\test\traits\view;

use \rakelley\jhframe\test\helpers\PHPUnitUtil as Utility;

/**
 * @coversDefaultClass \rakelley\jhframe\traits\view\InterpolatesPlaceholders
 */
class InterpolatesPlaceholdersTest extends
    \rakelley\jhframe\test\helpers\cases\Base
{

    protected function setUp()
    {
        $testedTrait = '\rakelley\jhframe\traits\view\InterpolatesPlaceholders';
        $this->testObj = $this->getMockForTrait($testedTrait);
    }


    /**
     * @covers ::interpolatePlaceholders
     * @dataProvider caseProvider
     */
    public function testInterpolatePlaceholders($view, $variables, $expected)
    {
        $this->assertEquals(
            Utility::callMethod($this->testObj, 'interpolatePlaceholders',
                                [$view, $variables]),
            $expected
        );
    }


    public function caseProvider()
    {
        return [
            [ //single occurrence case
                'foo %bar% %baz%',
                ['bar' => 'lorem', 'baz' => 'ipsum'],
                'foo lorem ipsum',
            ],
            [ //multiple occurrence case
                'foo %bar% baz %bar%',
                ['bar' => 'lorem'],
                'foo lorem baz lorem',
            ],
            [ //ensure safety with unused variables
                'foo %bar% baz',
                ['bar' => 'lorem', 'baz' => 'ipsum'],
                'foo lorem baz',
            ],
        ];
    }
}
