<?php

namespace BlueLibraries\PHP5\Dns\Records;

class DnsRecordProperties
{
    /**
     * @var string[]
     */
    protected static $defaultProperties = ['host', 'ttl', 'class', 'type'];

    /**
     * @var array[]
     */
    protected static $properties = [
        RecordTypes::A              => [
            'ip'
        ],
        RecordTypes::AAAA           => [
            'ipv6'
        ],
        RecordTypes::CAA            => [
            'flags',
            'tag',
            'value'
        ],
        RecordTypes::CNAME          => [
            'target'
        ],
        RecordTypes::SOA            => [
            'mname',
            'rname',
            'serial',
            'refresh',
            'retry',
            'expire',
            'minimum-ttl'
        ],
        RecordTypes::TXT            => [
            'txt',
        ],
        RecordTypes::DEPRECATED_SPF => [
            'txt',
        ],
        RecordTypes::NS             => [
            'target'
        ],
        RecordTypes::MX             => [
            'pri',
            'target'
        ],
        RecordTypes::PTR            => [
            'target'
        ],
        RecordTypes::SRV            => [
            'pri',
            'weight',
            'port',
            'target'
        ],
        RecordTypes::HINFO          => [
            'hardware',
            'os'
        ],
        RecordTypes::RRSIG          => [
            'type-covered',
            'algorithm', // int?
            'labels-number',
            'original-ttl',
            'signature-expiration',
            'signature-creation',
            'key-tag',
            'signer-name',
            'signature',
        ],

        RecordTypes::DNSKEY => [
            'flags',
            'protocol',
            'algorithm', // int?
            'public-key',
        ],

        RecordTypes::NSEC3PARAM => [
            'algorithm', // int?
            'flags',
            'iterations',
            'salt'
        ],

        RecordTypes::CDS => [
            'key-tag',
            'algorithm',
            'algorithm-digest',
            'digest',
        ],


        RecordTypes::DS => [
            'key-tag',
            'algorithm',
            'algorithm-digest',
            'digest',
        ],

        RecordTypes::CDNSKEY => [
            'flags',
            'protocol',
            'algorithm',
            'public-key'
        ],

        RecordTypes::NSEC => [
            'next-authoritative-name',
            'types',
        ],

        RecordTypes::HTTPS => [
            'separator',
            'original-length',
            'data',
        ],

        RecordTypes::NAPTR => [
            'order',
            'pref',
            'flag',
            'services',
            'regex',
            'replacement',
        ],

    ];

    /**
     * @var string[]
     */
    protected static $numberProperties = [
        'ttl',
        'minimum-ttl',
        'expire',
        'retry',
        'refresh',
        'port',
        'pri',
        'weight',
        'original-ttl',
        'signature-expiration',
        'signature-creation',
        'iterations',
        'flags',
        'algorithm',
        'key-tag',
        'algorithm-digest',
        'zone-key',
        'serial',
        'labels-number',
        'protocol',
        'original-length',
        'order',
        'pref',
    ];

    /**
     * @var string[]
     */
    private static $wrappedProperties = [
        'txt',
        'hardware',
        'os',
        'regex',
        'replacement',
        'flag',
        'services',
    ];

    /**
     * @var string[]
     */
    private static $loweredCaseProperties = [
        'host',
    ];

    /**
     * @var string[]
     */
    private static $unwrappedDotValues = [
        'regex',
        'replacement',
    ];

    /**
     * @var string[]
     */
    protected static $excludedBaseProperties = [
        'ttl',
        'entries',
    ];

    /**
     * @param int $typeId
     * @return array|null
     */
    public static function getProperties($typeId)
    {
        return isset(self::$properties[$typeId]) ? self::$properties[$typeId] : null;
    }

    /**
     * @return array
     */
    public static function getDefaultProperties()
    {
        return self::$defaultProperties;
    }

    /**
     * @param string $property
     * @return bool
     */
    public static function isNumberProperty($property)
    {
        return in_array($property, self::$numberProperties);
    }

    /**
     * @param string $property
     * @return bool
     */
    public static function isLoweredCaseProperty($property)
    {
        return in_array($property, self::$loweredCaseProperties);
    }

    /**
     * @param int $typeId
     * @return array
     */
    public static function getRecordTypeProperties($typeId)
    {
        return array_merge(
            self::$defaultProperties,
            is_null(static::getProperties($typeId)) ? [] : static::getProperties($typeId)
        );
    }

    /**
     * @param int $typeId
     * @param array $data
     * @return array
     */
    public static function getFilteredProperties($typeId, array $data)
    {
        return array_filter(
            self::getMappedProperties($data, $typeId),
            [DnsRecordProperties::class, 'filterExceptNumbers']
        );
    }

    /**
     * @param string $propertyName
     * @param mixed $value
     * @return bool
     */
    public static function isUnWrappedDotValue($propertyName, $value)
    {
        return in_array($propertyName, self::$unwrappedDotValues) && $value === '.';
    }

    /**
     * @param mixed $value
     * @return bool
     */
    protected static function filterExceptNumbers($value)
    {
        return ($value !== null && $value !== false && $value !== '');
    }

    /**
     * @param array $data
     * @param int $typeId
     * @return array|string[]
     */
    private static function getMappedProperties(array $data, $typeId)
    {
        return array_map(
            function ($property) use ($data) {
                return isset($data[$property]) ? $data[$property] : '';
            },
            DnsRecordProperties::getRecordTypeProperties($typeId)
        );
    }

    /**
     * @return array
     */
    public static function getExcludedBaseProperties()
    {
        return self::$excludedBaseProperties;
    }

    /**
     * @param string $propertyName
     * @return bool
     */
    public static function isWrappedProperty($propertyName)
    {
        return in_array($propertyName, self::$wrappedProperties);
    }

}
