<?php

namespace BlueLibraries\PHP5\Dns\Handlers\Types;

use BlueLibraries\PHP5\Dns\Handlers\AbstractDnsHandler;
use BlueLibraries\PHP5\Dns\Handlers\DnsHandlerException;
use BlueLibraries\PHP5\Dns\Handlers\DnsHandlerTypes;
use BlueLibraries\PHP5\Dns\Records\RecordTypes;
use BlueLibraries\PHP5\Dns\Records\StringRecordUtils;
use BlueLibraries\PHP5\Dns\Regex;

class Dig extends AbstractDnsHandler
{

    protected $nameserver = '';

    /**
     * @return string
     */
    public function getType()
    {
        return DnsHandlerTypes::DIG;
    }

    /**
     * @return bool
     * @throws DnsHandlerException
     */
    public function canUseIt()
    {
        $result = $this->executeCommand('dig -v 2>&1');
        return !empty($result[0]) && stripos($result[0], 'dig') === 0;
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

        return StringRecordUtils::normalizeRawResult(
            $this->getDnsRawResult($host, $typeId)
        );
    }

    /**
     * @param string $hostName
     * @param int $typeId
     * @return array
     * @throws DnsHandlerException
     */
    public function getDnsRawResult($hostName, $typeId)
    {

        $command = $this->getCommand($hostName, $typeId);

        if (is_null($command)) {
            return [];
        }

        if (!$this->isValidCommand($command)) {
            return [];
        }

        $output = $this->executeCommand($command);

        return array_filter($output);
    }

    /**
     * @param string $hostName
     * @param int $typeId
     * @return string|null
     */
    public function getCommand($hostName, $typeId)
    {
        try {
            $this->validateParams($hostName, $typeId);
        } catch (DnsHandlerException $e) {
            return null;
        }

        $recordName = RecordTypes::getName($typeId);

        $result = 'dig +nocmd +bufsize=1024 +noall +noauthority +answer +nomultiline +tries=' . ($this->retries + 1) . ' +time=' . $this->timeout;
        $result .= ' ' . $hostName . ' ' . $recordName;

        return $result . (empty($this->nameserver) ? '' : ' @' . $this->nameserver);
    }

    /**
     * @param string $command
     * @return array
     * @throws DnsHandlerException
     */
    protected function executeCommand($command)
    {
        $result = $this->executeRawCommand($command, $output);

        if (!$this->isValidOutput($output)) {
            throw new DnsHandlerException(
                'Error: ' . json_encode($output) . PHP_EOL .
                ' Command: ' . PHP_EOL . json_encode($command),
                DnsHandlerException::ERR_UNABLE_TO_GET_RECORD
            );
        }
        return $result === false ? [] : $output;
    }

    /**
     * @param string $command
     * @return bool
     */
    public function isValidCommand($command)
    {
        return preg_match(Regex::DIG_COMMAND, $command) === 1;
    }

    /**
     * @param array $output
     * @return bool
     */
    public function isValidOutput(array $output)
    {
        return empty($output)
            || strpos($output[0], ';;') !== 0;
    }

    /**
     * @param $command
     * @param $output
     * @return false|string
     */
    protected function executeRawCommand($command, &$output)
    {
        return exec($command, $output);
    }

}
