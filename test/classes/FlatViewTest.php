<?php
namespace rakelley\jhframe\test\classes;

use \rakelley\jhframe\test\helpers\PHPUnitUtil as Utility;

/**
 * @coversDefaultClass \rakelley\jhframe\classes\FlatView
 */
class FlatViewTest extends \rakelley\jhframe\test\helpers\cases\Base
{
    protected $rootDir = '/example/root/dir';
    protected $systemMock;


    protected function setUp()
    {
        $configInterface = '\rakelley\jhframe\interfaces\services\IConfig';
        $systemInterface =
            '\rakelley\jhframe\interfaces\services\IFileSystemAbstractor';
        $testedClass = '\rakelley\jhframe\classes\FlatView';

        $configMock = $this->getMock($configInterface);
        $configMock->method('Get')
                   ->with($this->identicalTo('ENV'),
                          $this->identicalTo('root_dir'))
                   ->willReturn($this->rootDir);

        $this->systemMock = $this->getMock($systemInterface);

        $mockedMethods = [
            'getConfig',//trait implemented
        ];
        $this->testObj = $this->getMockBuilder($testedClass)
                              ->disableOriginalConstructor()
                              ->setMethods($mockedMethods)
                              ->getMock();
        $this->testObj->method('getConfig')
                      ->willReturn($configMock);
        Utility::callConstructor($this->testObj, [$this->systemMock]);
    }


    /**
     * @covers ::__construct
     */
    public function testConstruct()
    {
        $this->assertAttributeEquals($this->rootDir, 'rootDir', $this->testObj);
        $this->assertAttributeEquals($this->systemMock, 'fileSystem',
                                     $this->testObj);
    }


    /**
     * @covers ::setParameters
     * @covers ::<protected>
     * @depends testConstruct
     */
    public function testSetParameters()
    {
        $parameters = ['view' => 'foobar', 'namespace' => 'baz/bat/'];
        $namespaceExpectation = str_replace('\\', '/', $parameters['namespace']);

        $this->systemMock->expects($this->once())
                         ->method('Exists')
                         ->with($this->logicalAnd(
                                $this->stringContains($this->rootDir),
                                $this->stringContains($namespaceExpectation),
                                $this->stringContains($parameters['view']),
                                $this->stringContains('.html')
                            ))
                         ->willReturn(true);

        $this->testObj->setParameters($parameters);
    }

    /**
     * @covers ::setParameters
     * @covers ::<protected>
     * @depends testSetParameters
     */
    public function testSetParametersViewNotExists()
    {
        $parameters = ['view' => 'foobar', 'namespace' => 'baz/bat/'];

        $this->systemMock->method('Exists')
                         ->willReturn(false);

        $this->setExpectedException('\DomainException');
        $this->testObj->setParameters($parameters);
    }

    /**
     * @covers ::setParameters
     * @covers ::<protected>
     * @depends testSetParameters
     */
    public function testSetParametersNoViewArg()
    {
        $parameters = ['namespace' => 'baz/bat/'];

        $this->setExpectedException('\BadMethodCallException');
        $this->testObj->setParameters($parameters);
    }

    /**
     * @covers ::setParameters
     * @covers ::<protected>
     * @depends testSetParameters
     */
    public function testSetParametersNoNamespaceArg()
    {
        $parameters = ['view' => 'foobar'];

        $this->setExpectedException('\BadMethodCallException');
        $this->testObj->setParameters($parameters);
    }


    /**
     * @covers ::constructView
     * @depends testSetParameters
     */
    public function testConstructView()
    {
        $parameters = ['view' => 'foobar', 'namespace' => 'baz/bat/'];
        $namespaceExpectation = str_replace('\\', '/', $parameters['namespace']);
        $content = 'lorem ipsum view content';

        $this->systemMock->method('Exists')
                         ->willReturn(true);
        $this->systemMock->expects($this->once())
                         ->method('containeredInclude')
                         ->with($this->logicalAnd(
                                $this->stringContains($this->rootDir),
                                $this->stringContains($namespaceExpectation),
                                $this->stringContains($parameters['view']),
                                $this->stringContains('.html')
                            ))
                         ->willReturn($content);

        $this->testObj->setParameters($parameters);
        $this->testObj->constructView();
        $this->assertAttributeEquals($content, 'viewContent', $this->testObj);
    }
}
