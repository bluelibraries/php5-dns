<?php

namespace BlueLibraries\PHP5\Dns\Records;

use DateTime;
use BlueLibraries\PHP5\Dns\Regex;

class DnsUtils
{

    /**
     * @param string $domain
     * @return bool
     */
    public static function isValidDomainOrSubdomain($domain)
    {
        if (empty($domain) || strlen($domain) < 4) {
            return false;
        }
        return preg_match(Regex::DOMAIN_OR_SUBDOMAIN, $domain) === 1;
    }

    /**
     * @param string $ipv6
     * @return string
     */
    public static function ipV6Shortener($ipv6)
    {
        if (substr($ipv6, -2) != ':0') {
            return preg_replace("/:0{1,3}/", ":", $ipv6);
        }
        return $ipv6;
    }

    /**
     * @param string $text
     * @return array|string|string[]
     */
    public static function sanitizeTextLineSeparators($text)
    {
        return
            str_replace(
                '  ', ' ',
                str_replace('" "', '', $text)
            );
    }

    /**
     * @param string $txt
     * @return string
     */
    public static function sanitizeRecordTxt($txt)
    {
        return str_replace('"', '\"', $txt);
    }

    /**
     * @param string $string
     * @return array
     */
    public static function getBitsFromString($string)
    {
        if (strlen($string) === 0) {
            return [];
        }

        $data = str_split($string);

        $result = '';

        foreach ($data as $value) {
            $decimal = (ord($value));
            $binary = decbin($decimal);
            $binary = str_pad($binary, 8, '0', STR_PAD_LEFT);
            $longBinary = $binary;
            $result .= $longBinary;
        }

        return str_split($result);
    }

    /**
     * @param array $binary
     * @param int $blockOffset
     * @return string
     */
    public static function getRecordsNamesFromBinary(array $binary, $blockOffset)
    {
        $result = [];

        foreach ($binary as $recordTypeId => $value) {
            if ((int)$value === 1) {
                $result[] = RecordTypes::getName($recordTypeId + $blockOffset);
            }
        }
        return implode(' ', $result);
    }

    /**
     * @param int $timestamp
     * @return int
     */
    public static function getHumanReadableDateTime($timestamp)
    {
        $dateTime = new DateTime();
        $dateTime->setTimestamp($timestamp);
        $result = $dateTime->format('YmdHis');
        return (int)$result;
    }

    /**
     * @param string $signature
     * @param int $bufferLength
     * @param string $separator
     * @return string
     */
    public static function getSplitSignature($signature, $bufferLength, $separator = ' ')
    {
        $signatureLen = strlen($signature);
        if ($bufferLength >= $signatureLen) {
            return $signature;
        }
        return trim(chunk_split($signature, $bufferLength, $separator));
    }

    /**
     * @param string $string
     * @param string $glue
     * @return string
     */
    public static function asciiString($string, $glue = '')
    {
        if (empty($string)) {
            return '';
        }

        $result = [];
        $stringData = str_split($string);

        foreach ($stringData as $key => $value) {
            $result[] = ord($value);
        }

        return implode($glue, $result);
    }

    /**
     * @param RecordInterface[] $array
     * @return array
     */
    public static function removeDuplicates(array $array)
    {
        if (empty($array)) {
            return [];
        }

        $result = [];
        $foundHashes = [];

        foreach ($array as $record) {
            $recordHash = $record->getHash();
            if (!in_array($recordHash, $foundHashes)) {
                $result[] = $record;
                $foundHashes[] = $recordHash;
            }
        }

        return $result;
    }


    /**
     * @param RecordInterface[] $results
     * @return RecordInterface[]
     */
    public static function sortRecords(array $results)
    {
        $result = [];

        foreach ($results as $record) {
            $result[$record->getHash()] = $record;
        }

        ksort($result);
        return array_values($result);
    }

    public static function trim($haystack, $needle, $length = 1)
    {
        if (empty($haystack)) {
            return '';
        }

        if (empty($needle) || empty($length)) {
            return $haystack;
        }

        $result = preg_replace(
            sprintf(
                Regex::TRIM_LENGTH_START, $needle, $length),
            '',
            $haystack
        );
        return preg_replace(
            sprintf(Regex::TRIM_LENGTH_END, $needle, $length),
            '',
            $result
        );
    }

    /**
     * @param string $text
     * @param int $i
     * @param int $startsFrom
     * @param int|null $count
     * @return array
     */
    public static function getConsecutiveLabels(
        $text,
        &$i,
        $startsFrom = 0,
        $count = 1
    )
    {
        if (empty($text)) {
            return [];
        }

        $textLen = strlen($text);

        $foundCount = 0;

        $result = [];

        for ($i = $startsFrom; $i < $textLen; $i++) {
            $len = ord($text[$i]);
            if ($len === 0) {
                if ($foundCount >= $count) {
                    $i += 1;
                    break;
                }
            }

            $substr = substr($text, $i + 1, $len);

            if ($substr === chr(0) && $count === 1) {
                $substr = '\000';
            }

            $result[] = $substr;
            $i += $len;
            $foundCount++;
        }

        return $result;
    }

    /**
     * @param string $string
     * @return array
     */
    public static function getBlocks($string)
    {

        if (empty($string)) {
            return [];
        }

        $result = [];
        $stringLen = strlen($string);

        for ($i = 0; $i < $stringLen; $i++) {
            $item = substr($string, $i, 1);
            $len = ord($item);

            if ($len === 0) {
                break;
            }

            $result[] = substr($string, $i + 1, $len);
            $i += $len;
        }

        return $result;
    }

}
