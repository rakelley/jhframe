<?php
namespace rakelley\jhframe\test\classes;

/**
 * @coversDefaultClass \rakelley\jhframe\classes\Crypto
 */
class CryptoTest extends \rakelley\jhframe\test\helpers\cases\Base
{
    protected $testedClass = '\rakelley\jhframe\classes\Crypto';


    /**
     * @covers ::hashString
     */
    public function testHashString()
    {
        $string = 'foobar';
        $regex = '/\$\w+[\$\w]+/';

        $hash = $this->testObj->hashString($string);
        $this->assertEquals(1, preg_match($regex, $hash));
    }


    /**
     * @covers ::compareHash
     * @depends testHashString
     */
    public function testCompareHash()
    {
        $case = 'foobar';
        $hashA = $this->testObj->hashString($case);
        $hashB = $this->testObj->hashString('something else');


        $this->assertTrue($this->testObj->compareHash($case, $hashA));
        $this->assertFalse($this->testObj->compareHash($case, $hashB));
    }


    /**
     * @covers ::hashNeedsUpdating
     * @depends testHashString
     */
    public function testHashNeedsUpdating()
    {
        $string = 'foobar';
        $good = $this->testObj->hashString($string);
        $bad = md5($string);

        $this->assertFalse($this->testObj->hashNeedsUpdating($good));
        $this->assertTrue($this->testObj->hashNeedsUpdating($bad));
    }


    /**
     * @covers ::createRandomString
     */
    public function testCreateRandomString()
    {
        $stringDefault = $this->testObj->createRandomString();
        $lengthArg = 100;
        $stringArg = $this->testObj->createRandomString($lengthArg);

        $this->assertTrue(strlen($stringDefault) > 1);
        $this->assertTrue(strlen($stringArg) === $lengthArg);
    }
}
