<?php
namespace rakelley\jhframe\test\classes;

/**
 * @coversDefaultClass \rakelley\jhframe\classes\Filter
 */
class FilterTest extends \rakelley\jhframe\test\helpers\cases\Base
{
    protected $tidyMock;


    protected function setUp()
    {
        $testedClass = '\rakelley\jhframe\classes\Filter';
        $tidyClass = '\Tidy';

        $this->tidyMock = $this->getMock($tidyClass);

        $this->testObj = new $testedClass($this->tidyMock);
    }


    /**
     * @covers ::__construct
     */
    public function testConstruct()
    {
        $this->assertAttributeEquals($this->tidyMock, 'tidy', $this->testObj);
    }


    /**
     * @covers ::Date
     * @dataProvider dateProvider
     */
    public function testDate($input, $format, $expected)
    {
        $this->assertEquals($expected, $this->testObj->Date($input, $format));
    }

    public function dateProvider()
    {
        return [
            [// Valid
                '2011-01-11 12:34:56',
                null,
                '2011-01-11 12:34:56'
            ],
            [// Format conversion to default
                '2011-01-11',
                null,
                '2011-01-11 00:00:00'
            ],
            [// Format conversion to custom
                '2011-01-11 12:34:56',
                'm/d/y',
                '01/11/11'
            ],
            [// Invalid
                'test',
                null,
                null
            ],
        ];
    }


    /**
     * @covers ::Email
     * @dataProvider emailProvider
     */
    public function testEmail($input, $expected)
    {
        $this->assertEquals($expected, $this->testObj->Email($input));
    }

    public function emailProvider()
    {
        return [
            [// Valid
                'test@example.com',
                'test@example.com'
            ],
            [// Needs sanitizing
                'tes"\/t@exa\\mple\.com',
                'test@example.com'
            ],
            [// Invalid
                'test',
                null
            ],
        ];
    }


    /**
     * @covers ::Float
     * @dataProvider floatProvider
     */
    public function testFloat($input, $expected)
    {
        $result = $this->testObj->Float($input);
        $this->assertEquals($expected, $result);
        if ($expected !== null) {
            $this->assertInternalType('float', $result);
        }
    }

    public function floatProvider()
    {
        return [
            [// Valid
                10.01,
                10.01
            ],
            [// Needs sanitizing
                '10.$!0',
                10.0
            ],
            [// Type conversion
                10,
                10.0
            ],
            [// Invalid
                'test',
                null
            ],
        ];
    }


    /**
     * @covers ::encodeHtml
     * @dataProvider htmlProvider
     */
    public function testEncodeHtml($input, $expected)
    {
        $this->assertEquals($expected, $this->testObj->encodeHtml($input));
    }

    public function htmlProvider()
    {
        return [
            [// Valid
                'foobar',
                'foobar'
            ],
            [// Needs sanitizing
                ' f"&<>oo_,.$() ',
                'f&quot;&amp;&lt;&gt;oo_,.$()'
            ],
            [// Invalid
                ' ',
                null
            ],
        ];
    }


    /**
     * @covers ::decodeHtml
     * @dataProvider encodedHtmlProvider
     */
    public function testDecodeHtml($input, $expected)
    {
        $this->assertEquals($expected, $this->testObj->decodeHtml($input));
    }

    public function encodedHtmlProvider()
    {
        return [
            [// Valid
                'foobar',
                'foobar'
            ],
            [// Needs sanitizing
                ' f&quot;&amp;&lt;&gt;oo_,.$() ',
                'f"&<>oo_,.$()'
            ],
            [// Invalid
                ' ',
                null
            ],
        ];
    }


    /**
     * @covers ::Int
     * @dataProvider intProvider
     */
    public function testInt($input, $expected)
    {
        $result = $this->testObj->Int($input);
        $this->assertEquals($expected, $result);
        if ($expected !== null) {
            $this->assertInternalType('int', $result);
        }
    }

    public function intProvider()
    {
        return [
            [// Valid
                10,
                10
            ],
            [// Needs sanitizing
                '10,$!0',
                100
            ],
            [// Type conversion
                10.01,
                10
            ],
            [// Invalid
                'test',
                null
            ],
        ];
    }


    /**
     * @covers ::spaceToUnderscore
     * @dataProvider spacedProvider
     */
    public function testSpaceToUnderscore($input, $expected)
    {
        $this->assertEquals($expected,
                            $this->testObj->spaceToUnderscore($input));
    }

    public function spacedProvider()
    {
        return [
            [// Valid
                'foobar',
                'foobar'
            ],
            [// Needs sanitizing
                'foo bar',
                'foo_bar'
            ],
            [// Correct for multiples
                'foo _ bar',
                'foo_bar'
            ],
            [// Invalid
                '',
                null
            ],
        ];
    }


    /**
     * @covers ::underscoreToSpace
     * @dataProvider underscoredProvider
     */
    public function testUnderscoreToSpace($input, $expected)
    {
        $this->assertEquals($expected,
                            $this->testObj->underscoreToSpace($input));
    }

    public function underscoredProvider()
    {
        return [
            [// Valid
                'foobar',
                'foobar'
            ],
            [// Needs sanitizing
                'foo_bar',
                'foo bar'
            ],
            [// Correct for multiples
                'foo _ bar',
                'foo bar'
            ],
            [// Invalid
                '',
                null
            ],
        ];
    }


    /**
     * @covers ::plainText
     * @dataProvider textProvider
     */
    public function testPlainText($input, $expected)
    {
        $this->assertEquals($expected, $this->testObj->plainText($input));
    }

    public function textProvider()
    {
        return [
            [// Valid
                'foo 1234 ! ? . , - _',
                'foo 1234 ! ? . , - _'
            ],
            [// Needs sanitizing
                'f"&<>oo_,.$()',
                'foo_,.'
            ],
            [// Invalid
                '<>\/&=',
                null
            ],
        ];
    }


    /**
     * @covers ::tidyText
     * @depends testConstruct
     */
    public function testTidyText()
    {
        $input = 'foobar';
        $defaultConfig = $this->readAttribute($this->testObj,
                                              'defaultTidyConfig');

        $this->tidyMock->expects($this->once())
                       ->method('repairString')
                       ->with($this->identicalTo($input),
                              $this->identicalTo($defaultConfig));
       
        $this->testObj->tidyText($input);
    }

    /**
     * @covers ::tidyText
     * @depends testTidyText
     */
    public function testTidyTextWithConfig()
    {
        $defaultConfig = $this->readAttribute($this->testObj,
                                              'defaultTidyConfig');
        $input = 'foobar';
        $config = ['foo' => 'bar', 'baz' => 'bat'];
        $expected = array_merge($defaultConfig, $config);

        $this->tidyMock->expects($this->once())
                       ->method('repairString')
                       ->with($this->identicalTo($input),
                              $this->identicalTo($expected));

        $this->testObj->tidyText($input, $config);
    }


    /**
     * @covers ::Url
     * @dataProvider urlProvider
     */
    public function testUrl($input, $expected)
    {
        $this->assertEquals($expected, $this->testObj->Url($input));
    }

    public function urlProvider()
    {
        return [
            [// Valid
                'http://example.com/?$-_.+!*\'(),{}|\\^~[]`<>#%";/?:@&=.',
                'http://example.com/?$-_.+!*\'(),{}|\\^~[]`<>#%";/?:@&=.',
            ],
            [// Needs sanitizing
                'http://€x▲mpl€.c●m/',
                'http://xmpl.cm/',
            ],
            [// Invalid
                '',
                null
            ],
        ];
    }


    /**
     * @covers ::Word
     * @dataProvider wordProvider
     */
    public function testWord($input, $permitted, $expected)
    {
        $this->assertEquals($expected,
                            $this->testObj->Word($input, $permitted));
    }

    public function wordProvider()
    {
        return [
            [// Valid
                'foobar',
                null,
                'foobar'
            ],
            [// Needs sanitizing
                'foo #%bar',
                null,
                'foobar'
            ],
            [// With permitted arg
                'foo #%bar',
                '\s',
                'foo bar'
            ],
            [// Invalid
                '#*%$&!',
                null,
                null
            ],
        ];
    }


    /**
     * @covers ::Filter
     * @covers ::<protected>
     * @depends testWord
     */
    public function testFilterSingleMethod()
    {
        $input = 'foo bar';
        $filters = 'word';
        $expected = $this->testObj->Word($input);

        $this->assertEquals(
            $expected,
            $this->testObj->Filter($input, $filters)
        );
    }

    /**
     * @covers ::Filter
     * @covers ::<protected>
     */
    public function testFilterSingleFunction()
    {
        $input = 'foo bar';
        $filters = 'strtoupper';
        $expected = strtoupper($input);

        $this->assertEquals(
            $expected,
            $this->testObj->Filter($input, $filters)
        );
    }

    /**
     * @covers ::Filter
     * @covers ::<protected>
     * @depends testFilterSingleMethod
     * @depends testFilterSingleFunction
     */
    public function testFilterMultiple()
    {
        $input = 'foo bar';
        $filters = ['strtoupper', 'word'];
        $expected = $this->testObj->Word(strtoupper($input));

        $this->assertEquals(
            $expected,
            $this->testObj->Filter($input, $filters)
        );
    }

    /**
     * @covers ::Filter
     * @covers ::<protected>
     * @depends testFilterMultiple
     * @depends testWord
     */
    public function testFilterWithArg()
    {
        $input = 'foo bar';
        $wordArg = '\s';
        $subArg = 1;
        $filters = [
            'substr' => $subArg,
            'word' => $wordArg
        ];
        $expected = $this->testObj->Word(substr($input, $subArg), $wordArg);

        $this->assertEquals(
            $expected,
            $this->testObj->Filter($input, $filters)
        );
    }

    /**
     * @covers ::Filter
     * @covers ::<protected>
     * @depends testFilterSingleMethod
     * @depends testFilterSingleFunction
     */
    public function testFilterFailure()
    {
        $input = 'foo bar';
        $filter = ['notexists'];

        $this->setExpectedException('\DomainException');
        $this->testObj->Filter($input, $filter);
    }


    /**
     * @covers ::asList
     * @depends testFilterWithArg
     * @depends testWord
     * @dataProvider listProvider
     */
    public function testAsList($input, $expected)
    {
        $args = ['separator' => ',', 'filters' => 'word'];

        $this->assertEquals($expected, $this->testObj->asList($input, $args));
    }

    public function listProvider()
    {
        return [
            [// Valid
                'foo,bar,baz',
                'foo,bar,baz'
            ],
            [// Needs sanitizing
                'foo$,bar!,baz^#$',
                'foo,bar,baz'
            ],
            [// Invalid
                '@*&,@!,**',
                null
            ],
        ];
    }
}
