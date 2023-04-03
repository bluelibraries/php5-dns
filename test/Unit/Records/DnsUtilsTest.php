<?php

namespace BlueLibraries\PHP5\Dns\Test\Unit\Records;

use BlueLibraries\PHP5\Dns\Records\DnsUtils;
use PHPUnit\Framework\TestCase;

class DnsUtilsTest extends TestCase
{

    /**
     * @return array[]
     */
    public static function isValidDomainOrSubdomainDataProvider()
    {
        return [
            ['', false],
            ['a', false],
            ['a.r', false],
            ['a.ro', true],
            ['a.com', true],
            ['mamaomida.test.com', true],
            ['other.mamaomida.test.com', true],
            ['another.other.mamaomida.test.com', true],
        ];
    }

    /**
     * @param $domain
     * @param $expected
     * @dataProvider isValidDomainOrSubdomainDataProvider
     * @return void
     */
    public function testIsValidDomainOrSubdomain($domain, $expected)
    {
        static::assertSame(DnsUtils::isValidDomainOrSubdomain($domain), $expected);
    }

    /**
     * @return array[]
     */
    public static function ipV6ShortenerDataProvider()
    {
        return [
            ['', ''],
            ['::ffff:93.113.174.110', '::ffff:93.113.174.110'],
            ['2041:0000:0000::875b:0', '2041:0000:0000::875b:0'],
            ['2041:0000:140F::875B:131B', '2041:0:140F::875B:131B'],
            ['2041:0001:140f::875b:131b', '2041:1:140f::875b:131b'],
            ['2041:22:140f::875b:131b', '2041:22:140f::875b:131b'],
        ];
    }

    /**
     * @param $ipv6
     * @param $expected
     * @dataProvider ipV6ShortenerDataProvider
     * @return void
     */
    public static function testIpV6Shortener($ipv6, $expected)
    {
        static::assertSame(DnsUtils::ipV6Shortener($ipv6), $expected);
    }

    /**
     * @return array[]
     */
    public static function sanitizeRecordTxtDataProvider()
    {
        return [
            ['', ''],
            ['ana are mere', 'ana are mere'],
            ['"ana are mere', '\"ana are mere'],
            ['mama @564"23434"cs\'\'=', 'mama @564\"23434\"cs\'\'=']
        ];
    }

    /**
     * @dataProvider sanitizeRecordTxtDataProvider
     * @return void
     */
    public function testSanitizeRecordTxt($value, $expected)
    {
        static::assertSame(DnsUtils::sanitizeRecordTxt($value), $expected);
    }

    /**
     * @return array[]
     */
    public static function getBitsFromStringDataProvider()
    {
        return [
            ['', []],
            ['A', ['0', '1', '0', '0', '0', '0', '0', '1']],
            [chr(1), ['0', '0', '0', '0', '0', '0', '0', '1']],
            [chr(255), ['1', '1', '1', '1', '1', '1', '1', '1']],
        ];
    }

    /**
     * @param $string
     * @param $expected
     * @dataProvider getBitsFromStringDataProvider
     * @return void
     */
    public function testGetBitsFromString($string, $expected)
    {
        static::assertSame(DnsUtils::getBitsFromString($string), $expected);
    }

    /**
     * @return array[]
     */
    public static function getRecordsNamesFromBinaryDataProvider()
    {
        return [
            [[], 0, ''],
            [[0, 1, 1], 0, 'A NS'],
            [[1, 1, 1], 256, 'URI CAA AVC'],
        ];
    }

    /**
     * @param array $binary
     * @param $offset
     * @param $expected
     * @return void
     * @dataProvider getRecordsNamesFromBinaryDataProvider
     */
    public function testGetRecordsNamesFromBinary(array $binary, $offset, $expected)
    {
        static::assertSame(DnsUtils::getRecordsNamesFromBinary($binary, $offset), $expected);
    }

    /**
     * @return array[]
     */
    public static function getHumanReadableDateTimeDataProvider()
    {
        return [
            [0, 19700101010000],
            [1673468849, 20230111202729],
            [1673862849, 20230116095409],
        ];
    }

    /**
     * @param $timestamp
     * @param $expected
     * @dataProvider getHumanReadableDateTimeDataProvider
     * @return void
     */
    public function testGetHumanReadableDateTime($timestamp, $expected)
    {
        static::assertSame(DnsUtils::getHumanReadableDateTime($timestamp), $expected);
    }

    /**
     * @return array[]
     */
    public static function getSplitSignatureDataProvider()
    {
        return [
            ['', 58, ' ', ''],
            ['x', 58, ' ', 'x'],
            ['1234', 1, ' ', '1 2 3 4'],
            ['1234567890123456789012', 10, ' ', '1234567890 1234567890 12'],
        ];
    }

    /**
     * @param $signature
     * @param $bufferLen
     * @param $separator
     * @param $expected
     * @dataProvider getSplitSignatureDataProvider
     * @return void
     */
    public function testGetSplitSignature($signature, $bufferLen, $separator, $expected)
    {
        static::assertSame(DnsUtils::getSplitSignature($signature, $bufferLen, $separator), $expected);
    }

    /**
     * @return array[]
     */
    public static function asciiStringDataProvider()
    {
        return [
            ['', '', ''],
            ['', ' ', ''],
            ['A', '', '65'],
            ['A', ' ', '65'],
            ['AA', '', '6565'],
            ['AA', ' ', '65 65'],
        ];
    }

    /**
     * @param $value
     * @param $glue
     * @param $expected
     * @return void
     * @dataProvider asciiStringDataProvider
     */
    public function testAsciiString($value, $glue, $expected)
    {
        self::assertSame($expected, DnsUtils::asciiString($value, $glue));
    }

    /**
     * @return array[]
     */
    public static function trimDataProvider()
    {
        return [
            ['', '' , 0, ''],
            ['test', 't', 1, 'es'],
            ['test', 't', 2, 'es'],
            ['test', 't', 0, 'test'],
            ['ttestt', 't', 0, 'ttestt'],
            ['ttestt', 't', 1, 'test'],
            ['ttestt', 't', 2, 'es'],
            ['ttestt', 't', 3, 'es'],
        ];
    }

    /**
     * @param $haystack
     * @param $needle
     * @param $length
     * @param $expected
     * @dataProvider trimDataProvider
     * @return void
     */
    public function testTrim($haystack, $needle, $length, $expected)
    {
        self::assertSame($expected, DnsUtils::trim($haystack, $needle, $length));
    }

    /**
     * @return array[]
     */
    public static function getBlocksDataProvider()
    {
        return [
            ['', []],
            [chr(1) . 'x', ['x']],
            [chr(2).'xx'.chr(0) . 'x', ['xx']],
            [chr(1) . 'x' . chr(2) . 'xy', ['x', 'xy']],
        ];
    }

    /**
     * @param $string
     * @param array $expected
     * @return void
     * @dataProvider getBlocksDataProvider
     */
    public function testGetBlocks($string, array $expected)
    {
        static::assertSame($expected, DnsUtils::getBlocks($string));
    }

    /**
     * @return array[]
     */
    public static function getConsecutiveLabelsDataProvider()
    {
        return [
            ['', 0, 0, 1, []],
            [chr(1).chr(0).chr(4).'test', 0, 0, 1, ['\000', 'test']],
            [chr(1) . 'x', 0, 0, 1, ['x']],
            [chr(1) . 'x' . chr(0) . chr(2) . 'xy', 0, 0, 1, ['x']],
            [chr(1) . 'x' . chr(0) . chr(2) . 'xy', 0, 0, 2, ['x', '', 'xy']],
            [chr(1) . 'x' . chr(0) . chr(2) . 'xy', 0, 0, 2, ['x', '', 'xy']],
        ];
    }

    /**
     * @param $text
     * @param $index
     * @param $from
     * @param $count
     * @param array $expected
     * @dataProvider getConsecutiveLabelsDataProvider
     * @return void
     */
    public function testGetConsecutiveLabels($text, $index, $from, $count, array $expected)
    {
        static::assertSame($expected, DnsUtils::getConsecutiveLabels($text, $index, $from, $count));
    }

}
