<?php
namespace rakelley\jhframe\test\classes;

/**
 * @coversDefaultClass \rakelley\jhframe\classes\SessionAbstractor
 */
class SessionAbstractorTest extends \rakelley\jhframe\test\helpers\cases\Base
{
    protected $testedClass = '\rakelley\jhframe\classes\SessionAbstractor';


    /**
     * @covers ::getId
     */
    public function testGetId()
    {
        $this->assertEquals(session_id(), $this->testObj->getId());
    }


    /**
     * @runInSeparateProcess
     * @covers ::startSession
     * @depends testGetId
     */
    public function testStartSession()
    {
        $this->testObj->startSession();

        $this->assertNotEquals('', session_id());
    }

    /**
     * @runInSeparateProcess
     * @covers ::startSession
     * @depends testStartSession
     */
    public function testStartSessionSafeWithExisting()
    {
        session_start();
        $existing = session_id();

        $this->testObj->startSession();

        $this->assertEquals($existing, session_id());
    }


    /**
     * @runInSeparateProcess
     * @covers ::newSession
     * @depends testStartSession
     */
    public function testNewSession()
    {
        session_start();
        $existing = session_id();

        $this->testObj->newSession();
        $new = session_id();

        $this->assertNotEquals('', $new);
        $this->assertNotEquals($existing, $new);
    }


    /**
     * @runInSeparateProcess
     * @covers ::closeSession
     * @depends testGetId
     */
    public function testCloseSession()
    {
        session_start();
        $this->testObj->closeSession();

        $this->assertEquals('', session_id());
    }
}
