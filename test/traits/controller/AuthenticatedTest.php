<?php
namespace rakelley\jhframe\test\traits\controller;

use \rakelley\jhframe\test\helpers\PHPUnitUtil as Utility;

/**
 * @coversDefaultClass \rakelley\jhframe\traits\controller\Authenticated
 */
class AuthenticatedTest extends
    \rakelley\jhframe\test\helpers\cases\Base
{
    protected $authMock;


    protected function setUp()
    {
        $authInterface = '\rakelley\jhframe\interfaces\services\IAuthService';
        $locatorInterface = '\rakelley\jhframe\interfaces\services\IServiceLocator';
        $testedTrait = '\rakelley\jhframe\traits\controller\Authenticated';

        $this->authMock = $this->getMock($authInterface);

        $locatorMock = $this->getMock($locatorInterface);
        $locatorMock->method('Make')
                    ->with($this->identicalTo($authInterface))
                    ->willReturn($this->authMock);

        $this->testObj = $this->getMockForTrait($testedTrait);
        $this->testObj->method('getLocator')
                      ->willReturn($locatorMock);
    }


    /**
     * Ensure set property is used
     * 
     * @covers ::routeAuth
     */
    public function testRouteAuthWithPermissionProperty()
    {
        $expected = 'foobar';
        $this->testObj->permission = $expected;

        $this->authMock->expects($this->once())
                       ->method('checkPermission')
                       ->with($this->identicalTo($expected))
                       ->willReturn(true);

        Utility::callMethod($this->testObj, 'routeAuth');
    }

    /**
     * Ensure passed arg is used
     * 
     * @covers ::routeAuth
     */
    public function testRouteAuthWithPermissionArg()
    {
        $expected = 'bazbat';

        $this->authMock->expects($this->once())
                       ->method('checkPermission')
                       ->with($this->identicalTo($expected))
                       ->willReturn(true);

        Utility::callMethod($this->testObj, 'routeAuth', [$expected]);
    }

    /**
     * Ensure passed arg is used even if property set
     * 
     * @covers ::routeAuth
     */
    public function testRouteAuthArgOverridesProperty()
    {
        $property = 'foobar';
        $expected = 'bazbat';
        $this->testObj->permission = $property;

        $this->authMock->expects($this->once())
                       ->method('checkPermission')
                       ->with($this->identicalTo($expected))
                       ->willReturn(true);

        Utility::callMethod($this->testObj, 'routeAuth', [$expected]);
    }

    /**
     * Expected failure if no arg and no property provided
     * 
     * @covers ::routeAuth
     */
    public function testRouteAuthWithNoPermissionProvided()
    {
        $this->authMock->expects($this->never())
                       ->method('checkPermission');

        $this->setExpectedException('\InvalidArgumentException');
        Utility::callMethod($this->testObj, 'routeAuth');     
    }

    /**
     * Expected failure if authentication check comes back false
     * 
     * @covers ::routeAuth
     * @depends testRouteAuthWithPermissionProperty
     */
    public function testRouteAuthFailedAuthentication()
    {
        $expected = 'foobar';
        $this->testObj->permission = $expected;

        $this->authMock->expects($this->once())
                       ->method('checkPermission')
                       ->with($this->identicalTo($expected))
                       ->willReturn(false);

        $this->setExpectedException('\RuntimeException');
        Utility::callMethod($this->testObj, 'routeAuth');
    }
}
