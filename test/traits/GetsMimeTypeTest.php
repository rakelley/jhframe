<?php
namespace rakelley\jhframe\test\traits;

use \org\bovigo\vfs\vfsStream;
use \rakelley\jhframe\test\helpers\PHPUnitUtil as Utility;

/**
 * @coversDefaultClass \rakelley\jhframe\traits\GetsMimeType
 */
class GetsMimeTypeTest extends \rakelley\jhframe\test\helpers\cases\Base
{
    protected $rootDir;


    protected function setUp()
    {
        $testedTrait = '\rakelley\jhframe\traits\GetsMimeType';

        $this->testObj = $this->getMockForTrait($testedTrait);

        vfsStream::setup('testDir');
        $this->rootDir = vfsStream::url('testDir') . '/';
    }


    /**
     * @covers ::getMimeType
     */
    public function testGetMimeType()
    {
        $file = $this->rootDir . 'foo.txt';
        file_put_contents($file, 'lorem ipsum dolor');

        $expected = 'text/plain';
        $this->assertEquals(
            $expected,
            Utility::callMethod($this->testObj, 'getMimeType', [$file])
        );
    }
}
