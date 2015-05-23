<?php
namespace rakelley\jhframe\test\classes;

use \org\bovigo\vfs\vfsStream;
use \rakelley\jhframe\test\helpers\PHPUnitUtil as Utility;

/**
 * @coversDefaultClass \rakelley\jhframe\classes\Bootstrapper
 */
class BootstrapperTest extends \rakelley\jhframe\test\helpers\cases\Base
{
    protected $appMock;
    protected $configMock;
    protected $rootDir;


    protected function setUp()
    {
        vfsStream::setup('testDir');
        $this->rootDir = vfsStream::url('testDir') . '/';

        $appClass = '\rakelley\jhframe\classes\App';
        $configInterface = '\rakelley\jhframe\interfaces\services\IConfig';
        $testedClass = '\rakelley\jhframe\classes\Bootstrapper';

        $this->configMock = $this->getMock($configInterface);

        $this->appMock = $this->getMockBuilder($appClass)
                              ->disableOriginalConstructor()
                              ->getMock();
        $this->appMock->config = $this->configMock;

        $mockedMethods = [
            'makeApp',//wrapper for new App
            'getRootConstant',//wrapper for global constant check
            'getFrameworkConfig',//__FILE__ dependent
            'setIniValue',//wrapper for set_ini
            'setTimezone',//wrapper for date_default_timezone_set
        ];
        $this->testObj = $this->getMockBuilder($testedClass)
                              ->disableOriginalConstructor()
                              ->setMethods($mockedMethods)
                              ->getMock();
        Utility::setProperties(['rootDir' => $this->rootDir], $this->testObj);
        $this->testObj->method('makeApp')
                      ->willReturn($this->appMock);

        $this->setUpBaseConfigFile();
    }

    protected function setUpBaseConfigFile()
    {
        $base = $this->rootDir . 'conf/base.php';
        mkdir($this->rootDir . 'conf');

        $content = <<<'PHP'
<?php
return [
    'lorem' => ['ipsum' => 'dolor']
];

PHP;

        file_put_contents($base, $content);
        $this->testObj->method('getFrameworkConfig')
                       ->willReturn($base);
    }

    /**
     * Create test environment config file for app
     * @see \rakelley\jhframe\classes\Boostrapper::getAppConfigDir
     */
    protected function setUpEnvironmentConfigFile($appName, $environment)
    {
        $path = $this->rootDir . 'src/' . $appName . '/conf/' . $environment .
                '.php';
        mkdir($this->rootDir . 'src');
        mkdir($this->rootDir . 'src/' . $appName);
        mkdir($this->rootDir . 'src/' . $appName . '/conf');

        $content = <<<'PHP'
<?php
return [
    'example' => ['foo' => 'bar']
];

PHP;
        file_put_contents($path, $content);
    }


    /**
     * @covers ::__construct
     */
    public function testConstruct()
    {
        Utility::setProperties(['rootDir' => null], $this->testObj);

        $this->testObj->expects($this->once())
                      ->method('getRootConstant')
                      ->willReturn($this->rootDir);

        Utility::callConstructor($this->testObj);
        $this->assertAttributeEquals($this->rootDir, 'rootDir', $this->testObj);
    }

    /**
     * @covers ::__construct
     * @depends testConstruct
     */
    public function testConstructFailure()
    {
        Utility::setProperties(['rootDir' => null], $this->testObj);

        $this->testObj->expects($this->once())
                      ->method('getRootConstant')
                      ->willReturn(null);

        $this->setExpectedException('\RuntimeException');
        Utility::callConstructor($this->testObj);
    }


    /**
     * @covers ::Bootstrap
     * @covers ::<protected>
     */
    public function testBootstrap()
    {
        $name = 'fooapp';
        $this->setUpEnvironmentConfigFile($name, 'development');

        $this->configMock->expects($this->exactly(2))
                         ->method('Merge')
                         ->with($this->isType('array'));

        $this->testObj->method('getRootConstant')
                      ->willReturn($this->rootDir);

        $this->appMock->expects($this->once())
                      ->method('setClassListFromConfig');
        $this->appMock->expects($this->once())
                      ->method('registerExceptionHandler');

        $this->assertEquals(
            $this->appMock,
            $this->testObj->Bootstrap($name)
        );      
    }


    /**
     * Covers detection of environment based on available config file
     * 
     * @covers ::Bootstrap
     * @covers ::<protected>
     * @dataProvider environmentCases
     * @depends testBootstrap
     */
    public function testBootStrapEnvironmentDetectionCases($environment)
    {
        $name = 'fooapp';

        $this->testObj->method('getRootConstant')
                       ->willReturn($this->rootDir);

        if ($environment) {
            $this->setUpEnvironmentConfigFile($name, $environment);
            $this->configMock->expects($this->exactly(2))
                             ->method('Merge')
                             ->with($this->isType('array'));
        } else {
            $this->setExpectedException('\RuntimeException');
        }

        $this->testObj->Bootstrap($name);
    }


    public function environmentCases()
    {
        return [
            ['development'],
            ['testing'],
            ['production'],
            [null],
        ];
    }


    /**
     * @covers ::Bootstrap
     * @covers ::<protected>
     * @depends testBootstrap
     * 
     * Have to use exactly cludge because PHPUnit's at counter increments
     * from previous interal object method calls
     * @link https://github.com/sebastianbergmann/phpunit/issues/674
     */
    public function testBootstrapFullArgs()
    {
        $name = 'fooapp';
        $args = [
            'appClass' => 'fake\app\classname',
            'configClass' => 'fake\config\classname',
            'locatorClass' => 'fake\locator\classname',
            'environment' => 'foobar',
        ];
        $this->setUpEnvironmentConfigFile($name, $args['environment']);

        $this->configMock->expects($this->exactly(2))
                         ->method('Merge')
                         ->with($this->isType('array'));

        $this->testObj->expects($this->once())
                      ->method('makeApp')
                      ->with($this->identicalTo($args['appClass']),
                             $this->identicalTo([$args['configClass'],
                                                $args['locatorClass']]))
                      ->willReturn($this->appMock);

        $this->assertEquals(
            $this->appMock,
            $this->testObj->Bootstrap($name, $args)
        );      
    }


    /**
     * Ensures setIniValue and setTimezone are being called based on stored
     * PHP config.
     * Have to use exactly cludge because PHPUnit's at counter increments
     * from previous interal object method calls
     * @link https://github.com/sebastianbergmann/phpunit/issues/674
     * 
     * @covers ::Bootstrap
     * @covers ::<protected>
     * @depends testBootstrap
     */
    public function testBootstrapPhpConfig()
    {
        $name = 'fooapp';
        $config = [
            'foo' => 'bar',
            'baz' => 'bat',
            'timezone' => 'foobar',
        ];

        $this->setUpEnvironmentConfigFile($name, 'development');

        $this->testObj->method('getRootConstant')
                       ->willReturn($this->rootDir);

        $this->configMock->expects($this->once())
                         ->method('Get')
                         ->With($this->identicalTo('PHP'))
                         ->willReturn($config);

        $this->testObj->expects($this->once())
                      ->method('setTimezone')
                      ->with($this->identicalTo($config['timezone']));

        $this->testObj->expects($this->exactly(2))
                      ->method('setIniValue')
                      ->with($this->logicalOr(
                           $this->identicalTo('foo'),
                            $this->identicalTo('baz')
                        ),
                        $this->logicalOr(
                            $this->identicalTo('bar'),
                            $this->identicalTo('bat')
                        ));

        $this->testObj->Bootstrap($name);
    }
}
