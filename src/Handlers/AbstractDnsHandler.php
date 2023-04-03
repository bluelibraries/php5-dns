<?php

namespace BlueLibraries\PHP5\Dns\Handlers;

use BlueLibraries\PHP5\Dns\Records\RecordTypes;
use BlueLibraries\PHP5\Dns\Regex;

abstract class AbstractDnsHandler implements DnsHandlerInterface
{

    protected $retries = 2;

    /**
     * maximum number of seconds DNS interrogation retries are allowed
     * seconds timeout per retry
     * 2 retries = 3 tries (1 initial try + 2 more tries)
     * if timeout is 10 that means that script duration may go up to 10 x number of tries
     * eg: timeout = 5,  retries = 1 (+ 1 initial try) => 10 seconds
     *     timeout = 5,  retries = 2 (+ 1 initial try) => 15 seconds
     *     timeout = 10, retries = 3 (+ 1 initial try) => 40 seconds (this is huge)
     */
    protected $timeout = 5;

    protected $nameserver = '8.8.8.8';

    /**
     * @param string $host
     * @param int $typeId
     * @return array
     */
    public abstract function getDnsData($host, $typeId);

    /**
     * @return int
     */
    public function getRetries()
    {
        return $this->retries;
    }

    /**
     * @param $retries
     * @return self
     */
    public function setRetries($retries)
    {
        $this->retries = $retries;
        return $this;
    }

    /**
     * @return int
     */
    public function getTimeout()
    {
        return $this->timeout;
    }

    /**
     * @param int $timeout
     * @return self
     */
    public function setTimeout($timeout)
    {
        $this->timeout = $timeout;
        return $this;
    }

    /**
     * @param string $nameserver
     * @return $this
     * @throws DnsHandlerException
     */
    public function setNameserver($nameserver)
    {
        if (filter_var($nameserver, FILTER_VALIDATE_IP) === false) {
            throw new DnsHandlerException(
                'Unable to set nameserver, as ' . json_encode($nameserver) . ' is an invalid IPV4 format!',
                DnsHandlerException::INVALID_NAMESERVER
            );
        }
        $this->nameserver = $nameserver;
        return $this;
    }

    /**
     * @param string $hostName
     * @throws DnsHandlerException
     */
    protected function validateHostName($hostName)
    {
        if (empty($hostName)) {
            throw new DnsHandlerException(
                'Invalid hostname, it must not be empty!',
                DnsHandlerException::HOSTNAME_EMPTY
            );
        }

        $hostnameErrorInfo = 'Invalid hostname ' . json_encode($hostName);

        if (strlen($hostName) < 3) {
            throw new DnsHandlerException(
                $hostnameErrorInfo . ' length. It must be 3 or more!',
                DnsHandlerException::HOSTNAME_LENGTH_TOO_SMALL
            );
        }

        if (!preg_match(Regex::DOMAIN_OR_SUBDOMAIN, $hostName)) {
            throw new DnsHandlerException(
                $hostnameErrorInfo . ' format! (characters "A-Za-z0-9.-", max length 63 chars allowed)',
                DnsHandlerException::HOSTNAME_FORMAT_INVALID
            );
        }

        if (!preg_match(Regex::HOSTNAME_LENGTH, $hostName)) {
            throw new DnsHandlerException(
                $hostnameErrorInfo . ' length! (min 3, max 253 characters allowed)',
                DnsHandlerException::HOSTNAME_LENGTH_INVALID
            );
        }
    }

    /**
     * @throws DnsHandlerException
     */
    protected function validateTypeIdValue($type, $hostName = null)
    {
        if (!RecordTypes::isValidTypeId($type)) {
            throw new DnsHandlerException(
                'Invalid records typeId: ' . json_encode($type) .
                ' host ' . json_encode($hostName) . ' !',
                DnsHandlerException::TYPE_ID_INVALID
            );
        }
    }

    /**
     * @throws DnsHandlerException
     */
    protected function validateParams($hostName, $typeId)
    {
        $this->validateHostName($hostName);
        $this->validateTypeIdValue($typeId, $hostName);
    }

}
