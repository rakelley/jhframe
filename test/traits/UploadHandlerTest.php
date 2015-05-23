<?php
namespace rakelley\jhframe\test\traits;

/**
 * @coversDefaultClass \rakelley\jhframe\traits\UploadHandler
 */
class UploadHandlerTest extends
    \rakelley\jhframe\test\helpers\cases\Base
{
    protected $systemMock;


    protected function setUp()
    {
        $systemInterface =
            '\rakelley\jhframe\interfaces\services\IFileSystemAbstractor';
        $testedTrait = '\rakelley\jhframe\traits\UploadHandler';

        $this->systemMock = $this->getMock($systemInterface);

        $this->testObj = $this->getMockForTrait($testedTrait);
        $this->testObj->fileSystem = $this->systemMock;
        $this->testObj->validTypes = [];
        $this->testObj->directory = '';
        $this->testObj->maxFileSize = 100000;
    }


    /**
     * @covers ::Validate
     */
    public function testValidate()
    {
        $file = [
            'error' => 0,
            'tmp_name' => 'foobar',
            'size' => $this->testObj->maxFileSize / 10,
        ];
        $mime = 'foo/bar';

        $this->testObj->validTypes = [$mime];

        $this->testObj->expects($this->once())
                      ->method('getMimeType')
                      ->With($this->identicalTo($file['tmp_name']))
                      ->willReturn($mime);

        $this->assertTrue($this->testObj->Validate($file));
    }

    /**
     * @covers ::Validate
     * @depends testValidate
     */
    public function testValidateWithError()
    {
        $file = [
            'error' => 3,
        ];

        $this->setExpectedException('\rakelley\jhframe\classes\InputException');
        $this->testObj->Validate($file);
    }

    /**
     * @covers ::Validate
     * @depends testValidate
     */
    public function testValidateWithInvalidMime()
    {
        $file = [
            'error' => 0,
            'tmp_name' => 'foobar',
            'size' => $this->testObj->maxFileSize / 10,
        ];
        $mime = 'foo/bar';

        $this->testObj->validTypes = ['other/other'];

        $this->testObj->expects($this->once())
                      ->method('getMimeType')
                      ->With($this->identicalTo($file['tmp_name']))
                      ->willReturn($mime);

        $this->setExpectedException('\rakelley\jhframe\classes\InputException');
        $this->testObj->Validate($file);
    }

    /**
     * @covers ::Validate
     * @depends testValidate
     */
    public function testValidateTooLarge()
    {
        $file = [
            'error' => 0,
            'tmp_name' => 'foobar',
            'size' => $this->testObj->maxFileSize * 10,
        ];
        $mime = 'foo/bar';

        $this->testObj->validTypes = [$mime];

        $this->testObj->expects($this->once())
                      ->method('getMimeType')
                      ->With($this->identicalTo($file['tmp_name']))
                      ->willReturn($mime);

        $this->setExpectedException('\rakelley\jhframe\classes\InputException');
        $this->testObj->Validate($file);    
    }


    /**
     * @covers ::Write
     * @covers ::getExtension
     */
    public function testWrite()
    {
        $key = 'anykey';
        $file = [
            'tmp_name' => 'foobar',
        ];
        $directory = '/lorem/ipsum/dolor/';
        $extension = 'baz';
        $mime = 'image/' . $extension;
        $expectedPath = $directory . $key . '.' . $extension;

        $this->testObj->directory = $directory;

        $this->testObj->expects($this->once())
                      ->method('Delete')
                      ->With($this->identicalTo($key));
        $this->testObj->expects($this->once())
                      ->method('getMimeType')
                      ->With($this->identicalTo($file['tmp_name']))
                      ->willReturn($mime);

        $this->systemMock->expects($this->once())
                         ->method('writeUploaded')
                         ->With($this->identicalTo($file['tmp_name']),
                                $this->identicalTo($expectedPath));

        $this->testObj->Write($key, $file);
    }
}
