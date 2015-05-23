<?php
namespace rakelley\jhframe\test\classes;

use \rakelley\jhframe\test\helpers\PHPUnitUtil as Utility;

/**
 * @coversDefaultClass \rakelley\jhframe\classes\ExceptionHandler
 */
class ExceptionHandlerTest extends \rakelley\jhframe\test\helpers\cases\Base
{
    protected $configMock;
    protected $containerMock;
    protected $fileMock;
    protected $ioMock;
    protected $testedClass = '\rakelley\jhframe\classes\ExceptionHandler';
    protected $defaultException = [
        'code' => 500,
        'message' => 'test exception message',
        'trace' => [
            ['class' => 'Foo', 'function' => 'Bar', 'line' => '42'],
            ['class' => 'Baz', 'function' => 'Bat', 'line' => '64'],
        ],
        'file' => '/home/foo/bar/baz.php',
        'line' => 167,
    ];


    protected function setUp()
    {
        $configInterface = '\rakelley\jhframe\interfaces\services\IConfig';
        $containerClass = '\rakelley\jhframe\classes\resources\ActionResult';
        $fileInterface =
            '\rakelley\jhframe\interfaces\services\IFileSystemAbstractor';
        $ioInterface = '\rakelley\jhframe\interfaces\services\IIo';

        $this->configMock = $this->getMock($configInterface);

        $this->containerMock = $this->getMockBuilder($containerClass)
                                    ->disableOriginalConstructor()
                                    ->getMock();
        $this->containerMock->method('getNewInstance')
                            ->will($this->returnSelf());

        $this->fileMock = $this->getMock($fileInterface);

        $this->ioMock = $this->getMock($ioInterface);

        $mockedMethods = [
            'getConfig',//trait implemented
            'logException',//trait implemented
        ];
        $this->testObj = $this->getMockBuilder($this->testedClass)
                              ->disableOriginalConstructor()
                              ->setMethods($mockedMethods)
                              ->getMock();
        $this->testObj->method('getConfig')
                      ->willReturn($this->configMock);
    }

    /**
     * Set up config store and call test object constructor.  null is a valid
     * value for any arg so need to use array_key_exists instead of isset
     * 
     * @param array|null $args Args to pass to configMock
     */
    protected function setUpWithConfig(array $args=null)
    {
        $isAjax = (array_key_exists('isAjax', $args)) ? $args['isAjax'] : false;
        $envType = (array_key_exists('envType', $args)) ? $args['envType'] : 
                   'production';
        $publicDir = (array_key_exists('publicDir', $args)) ?
                     $args['publicDir'] : 'a string';
        $errorView = (array_key_exists('errorView', $args)) ?
                     $args['errorView'] : 'a string';
        $logLevel = (array_key_exists('logLevel', $args)) ? $args['logLevel'] :
                    null;

        $this->configMock->expects($this->at(0))
                         ->method('Get')
                         ->with($this->identicalTo('ENV'),
                                $this->identicalTo('is_ajax'))
                         ->willReturn($isAjax);
        $this->configMock->expects($this->at(1))
                         ->method('Get')
                         ->with($this->identicalTo('ENV'),
                                $this->identicalTo('type'))
                         ->willReturn($envType);
        $this->configMock->expects($this->at(2))
                         ->method('Get')
                         ->with($this->identicalTo('ENV'),
                                $this->identicalTo('public_dir'))
                         ->willReturn($publicDir);
        $this->configMock->expects($this->at(3))
                         ->method('Get')
                         ->with($this->identicalTo('APP'),
                                $this->identicalTo('error_view'))
                         ->willReturn($errorView);
        $this->configMock->expects($this->at(4))
                         ->method('Get')
                         ->with($this->identicalTo('APP'),
                                $this->identicalTo('exception_log_level'))
                         ->willReturn($logLevel);

        Utility::callConstructor($this->testObj,
                                 [$this->containerMock, $this->fileMock,
                                  $this->ioMock]);
    }


    /**
     * @covers ::__construct
     */
    public function testConstruct()
    {
        $args = [
            'isAjax' => true,
            'envType' => 'production',
            'publicDir' => '/foo/bar/',
            'errorView' => 'lorem.php',
            'logLevel' => 100,
        ];
        $expectedApiCall = $args['isAjax'];
        $expectedDevEnv = false;
        $class = $this->testedClass;
        $expectedErrorView = $args['publicDir'] . $args['errorView'];
        $expectedLogLevel = $args['logLevel'];

        $this->setUpWithConfig($args);

        $this->assertAttributeEquals($expectedApiCall, 'apiCall',
                                     $this->testObj);
        $this->assertAttributeEquals($expectedDevEnv, 'devEnv',
                                     $this->testObj);
        $this->assertAttributeEquals($expectedErrorView, 'errorView',
                                     $this->testObj);
        $this->assertAttributeEquals($expectedLogLevel, 'logLevel',
                                     $this->testObj);

        $this->assertAttributeEquals($this->containerMock, 'resultContainer',
                                     $this->testObj);
        $this->assertAttributeEquals($this->fileMock, 'fileSystem',
                                     $this->testObj);
        $this->assertAttributeEquals($this->ioMock, 'io', $this->testObj);
    }

    /**
     * Case when optional values are not provided by config instance
     * 
     * @covers ::__construct
     * @depends testConstruct
     */
    public function testConstructWithDefaults()
    {
        $args = [
            'isAjax' => null,
            'envType' => null,
            'logLevel' => null,
        ];
        $expectedApiCall = false;
        $expectedDevEnv = false;
        $class = $this->testedClass;
        $expectedLogLevel = $class::LOGGING_ALL;

        $this->setUpWithConfig($args);

        $this->assertAttributeEquals($this->containerMock, 'resultContainer',
                                     $this->testObj);
        $this->assertAttributeEquals($expectedApiCall, 'apiCall',
                                     $this->testObj);
        $this->assertAttributeEquals($expectedDevEnv, 'devEnv',
                                     $this->testObj);
        $this->assertAttributeEquals($expectedLogLevel, 'logLevel',
                                     $this->testObj);
    }


    /**
     * Case with API response in non-dev env with non-user
     * exception code
     * Expected to respond via resultContainer with generic message
     * 
     * @covers ::Handle
     * @covers ::<protected>
     * @depends testConstruct
     */
    public function testHandleWithApi()
    {
        $args = [
            'isAjax' => true,
            'envType' => 'production',
            'logLevel' => 0,
        ];
        $this->setUpWithConfig($args);

        $message = 'test exception message';
        $code = 500;
        $e = new \Exception($message, $code);

        $this->containerMock->expects($this->once())
                            ->method('setSuccess')
                            ->with($this->identicalTo(false))
                            ->will($this->returnSelf());
        $this->containerMock->expects($this->once())
                            ->method('setError')
                            ->with($this->logicalNot(
                                  $this->identicalTo($message)
                              ))
                            ->will($this->returnSelf());
        $this->containerMock->expects($this->once())
                            ->method('Render');

        $this->ioMock->expects($this->once())
                     ->method('toExit');

        $this->testObj->Handle($e);
    }

    /**
     * Case with API response in non-dev env with user-level
     * exception code
     * Expected to respond via resultContainer with exception message
     * 
     * @covers ::Handle
     * @covers ::<protected>
     * @depends testHandleWithApi
     */
    public function testHandleWithApiInternal()
    {
        $args = [
            'isAjax' => true,
            'envType' => 'production',
            'logLevel' => 0,
        ];
        $this->setUpWithConfig($args);

        $message = 'test exception message';
        $code = 403;
        $e = new \Exception($message, $code);

        $this->containerMock->expects($this->once())
                            ->method('setSuccess')
                            ->with($this->identicalTo(false))
                            ->will($this->returnSelf());
        $this->containerMock->expects($this->once())
                            ->method('setError')
                            ->with($this->identicalTo($message))
                            ->will($this->returnSelf());
        $this->containerMock->expects($this->once())
                            ->method('Render');

        $this->ioMock->expects($this->once())
                     ->method('toExit');

        $this->testObj->Handle($e);
    }

    /**
     * Case with API response in dev env with non-user
     * exception code
     * Expected to respond via resultContainer with exception message
     * 
     * @covers ::Handle
     * @covers ::<protected>
     * @depends testHandleWithApi
     */
    public function testHandleWithApiAsDev()
    {
        $args = [
            'isAjax' => true,
            'envType' => 'development',
            'logLevel' => 0,
        ];
        $this->setUpWithConfig($args);

        $message = 'test exception message';
        $code = 500;
        $e = new \Exception($message, $code);

        $this->containerMock->expects($this->once())
                            ->method('setSuccess')
                            ->with($this->identicalTo(false))
                            ->will($this->returnSelf());
        $this->containerMock->expects($this->once())
                            ->method('setError')
                            ->with($this->identicalTo($message))
                            ->will($this->returnSelf());
        $this->containerMock->expects($this->once())
                            ->method('Render');

        $this->ioMock->expects($this->once())
                     ->method('toExit');

        $this->testObj->Handle($e);
    }

    /**
     * Case with not-API in non-dev env
     * Expected to respond with error view
     * 
     * @covers ::Handle
     * @covers ::<protected>
     * @depends testConstruct
     */
    public function testHandleWithView()
    {
        $args = [
            'isAjax' => false,
            'envType' => 'production',
            'logLevel' => 0,
        ];
        $this->setUpWithConfig($args);

        $message = 'test exception message';
        $code = 500;
        $e = new \Exception($message, $code);

        $expectedPath = $this->readAttribute($this->testObj, 'errorView');
        $expectedParameters = ['code' => $code];
        $viewContentStub = 'lorem ipsum dolor sit amet';

        $this->fileMock->expects($this->once())
                       ->method('containeredInclude')
                       ->with($this->identicalTo($expectedPath),
                              $this->identicalTo($expectedParameters))
                       ->willReturn($viewContentStub);

        $this->ioMock->expects($this->once())
                     ->method('httpCode')
                     ->with($this->identicalTo($code));
        $this->ioMock->expects($this->once())
                     ->method('toEcho')
                     ->with($this->identicalTo($viewContentStub));
        $this->ioMock->expects($this->once())
                     ->method('toExit');

        $this->testObj->Handle($e);
    }

    /**
     * Case with not-API in dev env
     * Expected to respond with exception message
     * 
     * @covers ::Handle
     * @covers ::<protected>
     * @depends testHandleWithView
     */
    public function testHandleWithViewAsDev()
    {
        $args = [
            'isAjax' => false,
            'envType' => 'development',
            'logLevel' => 0,
        ];
        $this->setUpWithConfig($args);

        $message = 'test exception message';
        $code = 500;
        $e = new \Exception($message, $code);

        $this->ioMock->expects($this->never())
                     ->method('httpCode');
        $this->ioMock->expects($this->once())
                     ->method('toEcho')
                     ->with($this->identicalTo($message));
        $this->ioMock->expects($this->once())
                     ->method('toExit');

        $this->testObj->Handle($e);
    }

    /**
     * Covers cases with logging
     * 
     * @dataProvider logCaseProvider
     * @covers ::Handle
     * @covers ::<protected>
     * @depends testHandleWithView
     */
    public function testHandleWithLogging($code, $logLevel, $expectedType)
    {
        $args = [
            'logLevel' => $logLevel,
        ];
        $this->setUpWithConfig($args);

        $message = 'test exception message';
        $e = new \Exception($message, $code);

        $this->testObj->expects($this->once())
                      ->method('logException')
                      ->with($this->identicalTo($e),
                             $this->identicalTo($expectedType));

        $this->testObj->Handle($e);
    }

    public function logCaseProvider()
    {
        $class = $this->testedClass;
        $types = '\Psr\Log\LogLevel';

        return [
            [500, $class::LOGGING_SYSTEM, $types::CRITICAL],
            [501, $class::LOGGING_SYSTEM, $types::CRITICAL],
            [500, $class::LOGGING_ALL, $types::CRITICAL],
            [404, $class::LOGGING_ALL, $types::WARNING],
            [403, $class::LOGGING_ALL, $types::WARNING],
            [200, $class::LOGGING_ALL, 'Unknown'],
            [null, $class::LOGGING_ALL, 'Unknown'],
            [200, $class::LOGGING_NONE + 1, 'Unknown'],
            [500, $class::LOGGING_SYSTEM - 1, 'Unknown'],
        ];
    }


    /**
     * Covers cases without logging
     * 
     * @dataProvider noLogCaseProvider
     * @covers ::Handle
     * @covers ::<protected>
     * @depends testHandleWithLogging
     */
    public function testHandleWithoutLogging($code, $logLevel)
    {
        $args = [
            'logLevel' => $logLevel,
        ];
        $this->setUpWithConfig($args);

        $message = 'test exception message';
        $e = new \Exception($message, $code);

        $this->testObj->expects($this->never())
                      ->method('logException');

        $this->testObj->Handle($e);
    }


    public function noLogCaseProvider()
    {
        $class = $this->testedClass;

        return [
            [500, $class::LOGGING_NONE],
            [501, $class::LOGGING_NONE],
            [404, $class::LOGGING_NONE],
            [403, $class::LOGGING_NONE],
            [404, $class::LOGGING_ALL - 1],
            [404, $class::LOGGING_SYSTEM],
            [200, $class::LOGGING_NONE],
            [null, $class::LOGGING_NONE],
        ];
    }
}
