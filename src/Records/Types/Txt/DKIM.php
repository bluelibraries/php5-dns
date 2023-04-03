<?php

namespace BlueLibraries\PHP5\Dns\Records\Types\Txt;

use BlueLibraries\PHP5\Dns\Records\ExtendedRecordTrait;
use BlueLibraries\PHP5\Dns\Records\ExtendedTxtRecords;
use BlueLibraries\PHP5\Dns\Records\TXTValuesRecordsTrait;
use BlueLibraries\PHP5\Dns\Records\Types\TXT;
use BlueLibraries\PHP5\Dns\Regex;

class DKIM extends TXT
{
    use ExtendedRecordTrait;
    use TXTValuesRecordsTrait;

    const VERSION = 'v';
    const KEY_TYPE = 'k';
    const PUBLIC_KEY = 'p';
    const HASH_TYPE = 'h';
    const GROUP = 'g';
    const NOTES = 'n';
    const QUERY = 'q';
    const SERVICE_TYPE = 's';
    const TESTING_TYPE = 't';

    private $txtRegex = Regex::DKIM;

    /**
     * @return string|null
     */
    private function getExtendedTypeName()
    {
        return ExtendedTxtRecords::DKIM;
    }

    /**
     * @return string|null
     */
    public function getPublicKey()
    {
        return $this->getParsedValue(self::PUBLIC_KEY);
    }

    /**
     * @return string|null
     */
    public function getVersion()
    {
        return $this->getParsedValue(self::VERSION);
    }

    /**
     * @return string|null
     */
    public function getKeyType()
    {
        return $this->getParsedValue(self::KEY_TYPE);
    }

    /**
     * @return string|null
     */
    public function getHashType()
    {
        return $this->getParsedValue(self::HASH_TYPE);
    }

    /**
     * @return string|null
     */
    public function getGroup()
    {
        return $this->getParsedValue(self::GROUP);
    }

    /**
     * @return string|null
     */
    public function getNotes()
    {
        return $this->getParsedValue(self::NOTES);
    }

    /**
     * @return string|null
     */
    public function getQuery()
    {
        return $this->getParsedValue(self::QUERY);
    }

    /**
     * @return string|null
     */
    public function getServiceType()
    {
        return $this->getParsedValue(self::SERVICE_TYPE);
    }

    /**
     * @return string|null
     */
    public function getTestingType()
    {
        return $this->getParsedValue(self::TESTING_TYPE);
    }

    /**
     * @return string|null
     */
    public function getSelector()
    {
        if (empty($this->getHost())) {
            return null;
        }

        $result = preg_match(Regex::DKIM_SELECTOR_VALUE, $this->getHost(), $matches);

        if ($result !== 1) {
            return null;
        }

        return isset($matches[1]) ? $matches[1] : null;
    }

}
