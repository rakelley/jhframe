<?php
namespace rakelley\jhframe\test\traits;

use \rakelley\jhframe\test\helpers\PHPUnitUtil as Utility;

/**
 * @coversDefaultClass \rakelley\jhframe\traits\CreatesImageThumbnails
 */
class CreatesImageThumbnailsTest extends
    \rakelley\jhframe\test\helpers\cases\Base
{
    protected $thumbMock;


    protected function setUp()
    {
        $thumbClass = '\rakelley\jhframe\classes\ThumbnailCreator';
        $locatorInterface =
            '\rakelley\jhframe\interfaces\services\IServiceLocator';
        $testedTrait = '\rakelley\jhframe\traits\CreatesImageThumbnails';

        $this->thumbMock = $this->getMock($thumbClass);

        $locatorMock = $this->getMock($locatorInterface);
        $locatorMock->method('Make')
                    ->with($this->identicalTo($thumbClass))
                    ->willReturn($this->thumbMock);

        $this->testObj = $this->getMockForTrait($testedTrait);
        $this->testObj->method('getLocator')
                      ->willReturn($locatorMock);
    }


    public function argProvider()
    {
        return [
            ['any string', null],//no args
            ['any string', ['foo' => 'foovalue', 'bar' => 'barvalue']],//args
        ];
    }


    /**
     * @covers ::createThumbnail
     * @dataProvider argProvider
     */
    public function testCreateThumbnail($originalPath, $args)
    {
        $thumb = '/foo/bar/baz.jpg';

        $this->thumbMock->expects($this->once())
                        ->method('createThumbnail')
                        ->with($this->identicalTo($originalPath),
                               $this->identicalTo($thumb),
                               $this->identicalTo($args));

        $this->testObj->expects($this->once())
                      ->method('getThumbPath')
                      ->with($this->identicalTo($originalPath))
                      ->willReturn($thumb);

        Utility::callMethod($this->testObj, 'createThumbnail',
                            [$originalPath, $args]);
    }
}
