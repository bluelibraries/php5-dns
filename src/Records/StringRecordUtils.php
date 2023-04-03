<?php

namespace BlueLibraries\PHP5\Dns\Records;

use BlueLibraries\PHP5\Dns\Regex;

class StringRecordUtils
{
    /**
     * @param string $line
     * @param int|null $limit
     * @return array
     */
    public static function lineToArray($line, $limit = 0)
    {
        if (empty($line)) {
            return [];
        }
        return explode(
            ' ',
            preg_replace(Regex::SEPARATED_WORDS, ' ', $line),
            $limit
        );
    }

    /**
     * @param $propertyName
     * @param $value
     * @return int|mixed|string
     */
    protected static function getFormattedPropertyValue($propertyName, $value)
    {
        if (
            in_array(
                $propertyName,
                ['host', 'mname', 'rname', 'target', 'signer-name', 'next-authoritative-name', 'replacement'
                ])) {
            $value = strtolower(rtrim($value, '.'));
        }

        if (in_array($propertyName, ['value', 'flag', 'services', 'regex'])) {
            $value = trim($value, '"');
        }

        if ($propertyName === 'ipv6') {
            $value = DnsUtils::ipV6Shortener($value);
        }

        if ($propertyName === 'type' && $value === 'SPF') {
            $value = 'TXT';
        }

        $value = DnsRecordProperties::isNumberProperty($propertyName)
            ? (is_numeric($value) ? $value + 0 : null)
            : $value;

        return DnsRecordProperties::isLoweredCaseProperty($propertyName)
            ? strtolower($value)
            : $value;
    }

    /**
     * @param array $configData
     * @param string $rawLine
     * @return array|null
     */
    public static function getRawData(array $configData, $rawLine)
    {

        $array = self::lineToArray($rawLine, count($configData));

        $result = [];

        foreach ($array as $key => $value) {
            $propertyName = $configData[$key];
            $value = self::getFormattedPropertyValue($propertyName, $value);
            $result[$propertyName] = $value;
        }

        if (isset($result['txt'])) {
            $result['txt'] = DnsUtils::trim(DnsUtils::sanitizeTextLineSeparators($result['txt']), '"', 1);
        }

        return $result;
    }

    /**
     * @param int $typeId
     * @return array|null
     */
    public static function getPropertiesData($typeId)
    {
        $properties = DnsRecordProperties::getProperties($typeId);
        if (empty($properties)) {
            return null;
        }
        return array_merge(DnsRecordProperties::getDefaultProperties(), $properties);
    }

    /**
     * @param array $rawResult
     * @return array
     */
    public static function normalizeRawResult(array $rawResult)
    {
        if (empty($rawResult)) {
            return [];
        }

        $result = [];

        foreach ($rawResult as $rawLine) {

            if (strpos($rawLine, ';;') === 0) {
                return [];
            }

            $lineData = self::lineToArray($rawLine, 5);
            $type = isset($lineData[3]) ? $lineData[3] : null;

            if (is_null($type)) {
                continue;
            }

            $typeId = RecordTypes::getType($type);

            if (is_null($typeId)) {
                continue;
            }

            $configData = self::getPropertiesData($typeId);

            if (!empty($configData)) {
                $result[] = StringRecordUtils::getRawData($configData, $rawLine);
            }
        }

        return $result;
    }

}
