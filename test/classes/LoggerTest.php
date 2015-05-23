<?php
namespace rakelley\jhframe\test\classes;

use \Psr\Log\LogLevel,
    \rakelley\jhframe\test\helpers\PHPUnitUtil as Utility;

/**
 * @coversDefaultClass \rakelley\jhframe\classes\Logger
 */
class LoggerTest extends \rakelley\jhframe\test\helpers\cases\Base
{
    protected $configMock;
    protected $fileMock;
    protected $systemMock;


    protected function setUp()
    {
        $configInterface = '\rakelley\jhframe\interfaces\services\IConfig';
        $fileInterface = '\rakelley\jhframe\interfaces\IFile';
        $systemInterface =
            '\rakelley\jhframe\interfaces\services\IFileSystemAbstractor';

        $this->configMock = $this->getMock($configInterface);
        $this->fileMock = $this->getMock($fileInterface);
        $this->systemMock = $this->getMock($systemInterface);
    }

    /**
     * Test methods need to explictly call this method because we need to delay
     * testObj construction until after config has been set up
     */
    protected function setUpLogger($args=null)
    {
        $this->setUpConfig($args);

        $testedClass = '\rakelley\jhframe\classes\Logger';
        $mockedMethods = [
            'getConfig',//trait implemented
            'getServerProp',//trait implemented
        ];
        $this->testObj = $this->getMockBuilder($testedClass)
                              ->disableOriginalConstructor()
                              ->setMethods($mockedMethods)
                              ->getMock();
        $this->testObj->method('getConfig')
                      ->willReturn($this->configMock);
        Utility::callConstructor($this->testObj, [$this->systemMock]);
    }

    protected function setUpConfig($args)
    {
        $args = ($args) ?: [];
        $logDir = (array_key_exists('logDir', $args)) ? $args['logDir'] :
                  '/any/path';
        $default = (array_key_exists('default', $args)) ? $args['default'] :
                   'anystring';
        $critical = (array_key_exists('critical', $args)) ? $args['critical'] :
                    null;
        $user = (array_key_exists('user', $args)) ? $args['user'] : null;
        $info = (array_key_exists('info', $args)) ? $args['info'] : null;

        $this->configMock->expects($this->at(0))
                         ->method('Get')
                         ->With($this->identicalTo('ENV'),
                                $this->identicalTo('log_dir'))
                         ->willReturn($logDir);
        $this->configMock->expects($this->at(1))
                         ->method('Get')
                         ->With($this->identicalTo('ENV'),
                                $this->identicalTo('log_default'))
                         ->willReturn($default);
        $this->configMock->expects($this->at(2))
                         ->method('Get')
                         ->With($this->identicalTo('ENV'),
                                $this->identicalTo('log_critical'))
                         ->willReturn($critical);
        $this->configMock->expects($this->at(3))
                         ->method('Get')
                         ->With($this->identicalTo('ENV'),
                                $this->identicalTo('log_user'))
                         ->willReturn($user);
        $this->configMock->expects($this->at(4))
                         ->method('Get')
                         ->With($this->identicalTo('ENV'),
                                $this->identicalTo('log_info'))
                         ->willReturn($info);
    }


    /**
     * @covers ::__construct
     */
    public function testConstruct()
    {
        $config = [
            'logDir' => '/foo/bar',
            'default' => 'default.txt',
            'critical' => 'critical.txt',
            'user' => 'user.txt',
            'info' => 'info.txt',
        ];
        $this->setUpLogger($config);

        $expected = [
            'defaultLog' => $config['logDir'] . $config['default'],
            'criticalLog' => $config['logDir'] . $config['critical'],
            'userLog' => $config['logDir'] . $config['user'],
            'infoLog' => $config['logDir'] . $config['info'],
        ];

        array_walk(
            $expected,
            function($val, $prop) {
                $this->assertAttributeEquals($val, $prop, $this->testObj);
            }
        );
    }

    /**
     * @covers ::__construct
     * @depends testConstruct
     */
    public function testConstructWithDefaults()
    {
        $config = [
            'logDir' => '/foo/bar',
            'default' => 'default.txt',
        ];
        $this->setUpLogger($config);

        $default = $config['logDir'] . $config['default'];
        $properties = ['defaultLog', 'criticalLog', 'userLog', 'infoLog'];

        array_walk(
            $properties,
            function($prop) use ($default) {
                $this->assertAttributeEquals($default, $prop, $this->testObj);
            }
        );
    }


    /**
     * @covers ::exceptionToMessage
     * @covers ::getRoute
     * @depends testConstruct
     */
    public function testExceptionToMessage()
    {
        $this->setUpLogger();
        $message = 'test message';
        $exc = new \Exception($message);
        try {
            throw $exc;
        } catch (\Exception $e) {
            $exc = $e;
        }

        $entry = $this->testObj->exceptionToMessage($exc);
        $this->assertInternalType('string', $entry);
        $this->assertContains($message, $entry);
        $this->assertContains('Exception', $entry);
    }

    /**
     * @covers ::exceptionToMessage
     * @covers ::getRoute
     * @depends testExceptionToMessage
     */
    public function testExceptionToMessageWithRoute()
    {
        $this->setUpLogger();
        $message = 'test message';
        $exc = new \Exception($message);
        try {
            throw $exc;
        } catch (\Exception $e) {
            $exc = $e;
        }

        $this->testObj->expects($this->atLeastOnce())
                      ->method('getServerProp')
                      ->willReturn('a string');

        $entry = $this->testObj->exceptionToMessage($exc);
    }


    /**
     * @covers ::Log
     * @covers ::writeTo
     * @depends testConstruct
     * @dataProvider logCaseProvider
     */
    public function testLogCases($level, $expectedLevel, $logProp)
    {
        $config = [
            'logDir' => '/foo/bar',
            'default' => 'default.txt',
            'critical' => 'critical.txt',
            'user' => 'user.txt',
            'info' => 'info.txt',
        ];
        $this->setUpLogger($config);
        $message = 'lorem ipsum';
        $expectedLog = $this->readAttribute($this->testObj, $logProp);

        $this->systemMock->expects($this->once())
                         ->method('getFileWithPath')
                         ->with($this->identicalTo($expectedLog))
                         ->willReturn($this->fileMock);

        $this->fileMock->expects($this->once())
                       ->method('Append')
                       ->with($this->logicalAnd(
                            $this->stringContains($message),
                            $this->stringContains($expectedLevel)
                        ));

        $this->testObj->Log($level, $message);
    }

    public function logCaseProvider()
    {
        return [
            [LogLevel::EMERGENCY, LogLevel::EMERGENCY, 'criticalLog'],
            [LogLevel::ALERT, LogLevel::ALERT, 'criticalLog'],
            [LogLevel::CRITICAL, LogLevel::CRITICAL, 'criticalLog'],
            [LogLevel::ERROR, LogLevel::ERROR, 'criticalLog'],
            [LogLevel::WARNING, LogLevel::WARNING, 'userLog'],
            [LogLevel::NOTICE, LogLevel::NOTICE, 'infoLog'],
            [LogLevel::INFO, LogLevel::INFO, 'infoLog'],
            [LogLevel::DEBUG, LogLevel::DEBUG, 'infoLog'],
            ['unknown', 'unknown', 'defaultLog'],
            ['any other string', 'unknown', 'defaultLog'],
        ];
    }


    /**
     * @covers ::Log
     * @covers ::writeTo
     * @covers ::interpolateMessage
     * @depends testLogCases
     */
    public function testLogWithInterpolation()
    {
        $this->setUpLogger();

        $level = 'any';
        $message = 'lorem ipsum {foobar} sit {bazbat}';
        $context = ['foobar' => 'dolor', 'bazbat' => 'amet'];
        $expectedMessage = 'lorem ipsum dolor sit amet';

        $this->systemMock->method('getFileWithPath')
                         ->willReturn($this->fileMock);

        $this->fileMock->expects($this->once())
                       ->method('Append')
                       ->with($this->stringContains($expectedMessage));

        $this->testObj->Log($level, $message, $context);
    }
}
