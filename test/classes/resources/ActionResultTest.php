<?php
namespace rakelley\jhframe\test\classes\resources;

use \rakelley\jhframe\test\helpers\PHPUnitUtil as Utility;

/**
 * @coversDefaultClass \rakelley\jhframe\classes\resources\ActionResult
 */
class ActionResultTest extends \rakelley\jhframe\test\helpers\cases\Base
{

    protected function setUp()
    {
        $testedClass = '\rakelley\jhframe\classes\resources\ActionResult';

        $this->testObj = $this->getMockBuilder($testedClass)
                              ->disableOriginalConstructor()
                              ->setMethods(null)
                              ->getMock();
    }


    /**
     * @covers ::setSuccess
     */
    public function testSetSuccess()
    {
        $success = true;

        $this->assertEquals($this->testObj,
                            $this->testObj->setSuccess($success));
        $this->assertAttributeEquals($success, 'success', $this->testObj);
    }

    /**
     * @covers ::getSuccess
     * @depends testSetSuccess
     */
    public function testGetSuccess()
    {
        $this->testSetSuccess();

        $this->assertEquals($this->readAttribute($this->testObj, 'success'),
                            $this->testObj->getSuccess());
    }


    /**
     * @covers ::setError
     */
    public function testSetError()
    {
        $error = 'lorem ipsum';

        $this->assertEquals($this->testObj,
                            $this->testObj->setError($error));
        $this->assertAttributeEquals($error, 'error', $this->testObj);
    }

    /**
     * @covers ::getError
     * @depends testSetError
     */
    public function testGetError()
    {
        $this->testSetError();

        $this->assertEquals($this->readAttribute($this->testObj, 'error'),
                            $this->testObj->getError());
    }


    /**
     * @covers ::setMessage
     */
    public function testSetMessage()
    {
        $message = 'lorem ipsum';

        $this->assertEquals($this->testObj,
                            $this->testObj->setMessage($message));
        $this->assertAttributeEquals($message, 'message', $this->testObj);
    }

    /**
     * @covers ::getMessage
     * @depends testSetMessage
     */
    public function testGetMessage()
    {
        $this->testSetMessage();

        $this->assertEquals($this->readAttribute($this->testObj, 'message'),
                            $this->testObj->getMessage());
    }


    /**
     * @covers ::getContent
     * @dataProvider contentProvider
     * @depends testGetSuccess
     * @depends testGetError
     * @depends testGetMessage
     */
    public function testGetContent($properties, $expected)
    {
        if (isset($properties['content'])) {
            Utility::setProperties(['content' => $properties['content']],
                                   $this->testObj);
        }
        if (isset($properties['success'])) {
            $this->testObj->setSuccess($properties['success']);
        }
        if (isset($properties['error'])) {
            $this->testObj->setError($properties['error']);
        }
        if (isset($properties['message'])) {
            $this->testObj->setMessage($properties['message']);
        }

        $this->assertEquals($expected, $this->testObj->getContent());
    }

    public function contentProvider()
    {
        return [
            [//content override set
                ['content' => 'foobar', 'success' => true,
                 'error' => 'anything', 'message' => 'anything'],
                'foobar',
            ],
            [//success set
                ['success' => true],
                ['success' => true],
            ],
            [//with error
                ['success' => false, 'error' => 'lorem'],
                ['success' => false, 'error' => 'lorem'],
            ],
            [//with message
                ['success' => true, 'message' => 'lorem'],
                ['success' => true, 'message' => 'lorem'],
            ],
        ];
    }


    /**
     * @covers ::setType
     */
    public function testSetType()
    {
        $type = 'anything';
        $this->setExpectedException('\BadMethodCallException');
        $this->testObj->setType($type);
    }


    /**
     * @covers ::setMetaData
     */
    public function testSetMetaData()
    {
        $metaData = ['any', 'thing'];
        $this->setExpectedException('\BadMethodCallException');
        $this->testObj->setMetaData($metaData);
    }
}
