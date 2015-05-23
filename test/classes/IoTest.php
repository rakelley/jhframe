<?php
namespace rakelley\jhframe\test\classes;

use \org\bovigo\vfs\vfsStream;

/**
 * @coversDefaultClass \rakelley\jhframe\classes\Io
 */
class IoTest extends \rakelley\jhframe\test\helpers\cases\Base
{
    protected $testedClass = '\rakelley\jhframe\classes\Io';


    public function tableTypeProvider()
    {
        return [
            ['get', $_GET],
            ['post', $_POST],
            ['files', $_FILES],
            ['cookie', $_COOKIE],
            ['unsupported', null],
        ];
    }


    /**
     * @covers ::getInputTable
     * @dataProvider tableTypeProvider
     */
    public function testGetInputTable($type, $expected)
    {
        if (isset($expected)) {
            $this->assertEquals($expected,
                                $this->testObj->getInputTable($type));
        } else {
            $this->setExpectedException('\DomainException');
            $this->testObj->getInputTable($type);
        }
    }


    /**
     * @covers ::Header
     * @runInSeparateProcess
     */
    public function testHeader()
    {
        $header = 'a test header';

        ob_start();
        $this->assertEquals($this->testObj, $this->testObj->Header($header));
        $headers = xdebug_get_headers();
        ob_end_clean();

        $this->assertContains($header, $headers);
    }


    /**
     * @covers ::httpCode
     */
    public function testHttpCode()
    {
        $code = 500;

        $this->assertEquals($this->testObj, $this->testObj->httpCode($code));
        $this->assertEquals($code, http_response_code());
    }


    /**
     * @covers ::toEcho
     */
    public function testToEcho()
    {
        $content = 'lorem ipsum dolor';

        ob_start();
        $this->testObj->toEcho($content);
        $result = ob_get_contents();
        ob_end_clean();

        $this->assertEquals($result, $content);
    }


    /**
     * @covers ::toErrorLog
     */
    public function testToErrorLog()
    {
        vfsStream::setup('testDir');
        $logFile = vfsStream::url('testDir') . '/log.txt';
        touch($logFile);

        $message = 'lorem ipsum dolor';
        $this->testObj->toErrorLog($message, 3, $logFile);
        $this->assertContains($message, file_get_contents($logFile));
    }
}
