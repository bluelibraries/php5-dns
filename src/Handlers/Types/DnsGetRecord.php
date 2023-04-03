<?php

namespace BlueLibraries\PHP5\Dns\Handlers\Types;

use BlueLibraries\PHP5\Dns\Handlers\AbstractDnsHandler;
use BlueLibraries\PHP5\Dns\Handlers\DnsHandlerException;
use BlueLibraries\PHP5\Dns\Handlers\DnsHandlerTypes;
use BlueLibraries\PHP5\Dns\Records\RecordTypes;

class DnsGetRecord extends AbstractDnsHandler
{
    /**
     * @var array
     */
    public static $internalPHPTypes = [
        RecordTypes::A     => DNS_A,
        RecordTypes::CNAME => DNS_CNAME,
        RecordTypes::HINFO => DNS_HINFO,
        RecordTypes::MX    => DNS_MX,
        RecordTypes::NS    => DNS_NS,
        RecordTypes::PTR   => DNS_PTR,
        RecordTypes::SOA   => DNS_SOA,
        RecordTypes::TXT   => DNS_TXT,
        RecordTypes::AAAA  => DNS_AAAA,
        RecordTypes::SRV   => DNS_SRV,
        RecordTypes::NAPTR => DNS_NAPTR,
        RecordTypes::A6    => DNS_A6,
        RecordTypes::ALL   => DNS_ALL,
    ];

    /**
     * @param int $typeId
     * @return int|null
     */
    private static function getInternalTypeId($typeId)
    {
        return isset(static::$internalPHPTypes[$typeId])
            ? static::$internalPHPTypes[$typeId]
            : null;
    }

    /**
     * @return string
     */
    public function getType()
    {
        return DnsHandlerTypes::DNS_GET_RECORD;
    }

    /**
     * @return bool
     */
    public function canUseIt()
    {
        return function_exists('dns_get_record');
    }

    /**
     * @param string $host
     * @param int $typeId
     * @return array
     * @throws DnsHandlerException
     */
    public function getDnsData($host, $typeId)
    {
        $this->validateParams($host, $typeId);
        $this->validatePHPInternalTypeId($typeId);

        $internalTypeId = static::getInternalTypeId($typeId);

        return $this->getDnsRawResult($host, $internalTypeId);
    }

    /**
     * @param string $host
     * @param int $type
     * @return array
     */
    public function getDnsRawResult($host, $type)
    {
        $startProcess = time();
        for ($i = 0; $i <= $this->retries; $i++) {
            if (
                ($result = $this->getDnsRecord($host, $type)) !== []
                || ((time() - $startProcess) >= $this->timeout)
            ) {
                return is_array($result) ? $result : [];
            }
        }
        return [];
    }

    /**
     * @param string $host
     * @param int $type
     * @return array|bool
     */
    protected function getDnsRecord($host, $type)
    {
        return empty($host) ? false : $this->getUpdatedRecordsData(dns_get_record($host, $type));
    }

    /**
     * @param array $records
     * @return array
     */
    public function getUpdatedRecordsData($records)
    {
        if (!is_array($records) || empty($records)) {
            return [];
        }

        $records = $this->splitTXTEntries($records);

        foreach ($records as $key => $record) {
            if ($record['type'] === 'NAPTR') {
                $records[$key] = $this->fixNAPTRFlags($record);
            }
        }
        return $records;
    }

    /**
     * @param array $record
     * @return array
     */
    private function fixNAPTRFlags(array $record)
    {
        $result = [];
        foreach ($record as $key => $value) {
            if ($key === 'flags') {
                $result['flag'] = $value;
            } else {
                $result[$key] = $value;
            }
        }
        return $result;
    }

    /**
     * @param string $nameserver
     * @return DnsGetRecord
     * @throws DnsHandlerException
     */
    public function setNameserver($nameserver)
    {
        throw new DnsHandlerException(
            'Unable to set nameserver, as `dns_get_record` cannot use custom nameservers!',
            DnsHandlerException::UNABLE_TO_USE_CUSTOM_NAMESERVER
        );
    }

    /**
     * @param int $typeId
     * @return void
     * @throws DnsHandlerException
     */
    private function validatePHPInternalTypeId($typeId)
    {
        if (!isset(self::$internalPHPTypes[$typeId])) {
            $recordTypeName = RecordTypes::getName($typeId);
            throw new DnsHandlerException(
                'DNS record type ' . json_encode($recordTypeName) .
                ' , please use a different DNS Handler (TCP is recommended)!',
                DnsHandlerException::TYPE_ID_NOT_SUPPORTED
            );
        }
    }

    /**
     * @param array $records
     * @return array
     */
    private function splitTXTEntries(array $records)
    {
        if (empty($records)) {
            return $records;
        }

        $result = [];

        foreach ($records as $record) {

            if ($record['type'] !== 'TXT') {
                $result[] = $record;
                continue;
            }
            $entries = isset($record['entries']) ? $record['entries'] : [];
            if (!empty($entries)) {
                foreach ($entries as $entry) {
                    $subEntry = $record;
                    unset($subEntry['entries']);
                    $subEntry['txt'] = $entry;
                    $result[] = $subEntry;
                }
            } else {
                $result[] = $record;
            }
        }
        return $result;
    }

}
