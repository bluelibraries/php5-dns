<?php

namespace BlueLibraries\PHP5\Dns\Handlers;

interface DnsHandlerInterface
{
    /**
     * @return string
     */
    public function getType();

    /**
     * @return bool
     */
    public function canUseIt();

    /**
     * @param $host
     * @param $typeId
     * @return array
     */
    public function getDnsData($host, $typeId);

    /**
     * @return int
     */
    public function getRetries();

    /**
     * @param $retries
     * @return $this
     */
    public function setRetries($retries);

    /**
     * @return int
     */
    public function getTimeout();

    /**
     * @param $timeout
     * @return $this
     */
    public function setTimeout($timeout);

    /**
     * @param $nameserver
     * @return DnsHandlerInterface
     * @throws DnsHandlerException
     */
    public function setNameserver($nameserver);

}