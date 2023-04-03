<?php

namespace BlueLibraries\PHP5\Dns\Records\Types\Txt;

use BlueLibraries\PHP5\Dns\Records\ExtendedRecordTrait;
use BlueLibraries\PHP5\Dns\Records\ExtendedTxtRecords;
use BlueLibraries\PHP5\Dns\Records\TXTValuesRecordsTrait;
use BlueLibraries\PHP5\Dns\Records\Types\TXT;
use BlueLibraries\PHP5\Dns\Regex;

class DMARC extends TXT
{
    use ExtendedRecordTrait;
    use TXTValuesRecordsTrait;

    const VERSION = 'v';
    const POLICY = 'p';
    const PERCENTAGE = 'pct';
    const RUA = 'rua';
    const RUF = 'ruf';
    const FO = 'fo';
    const ASPF = 'aspf';
    const ADKIM = 'adkim';
    const REPORT_FORMAT = 'rf';
    const REPORT_INTERVAL = 'ri';
    const SUBDOMAIN_POLICY = 'sp';

    private $txtRegex = Regex::DMARC;

    /**
     * @return string|null
     */
    private function getExtendedTypeName()
    {
        return ExtendedTxtRecords::DMARC;
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
    public function getPolicy()
    {
        return $this->getParsedValue(self::POLICY);
    }

    /**
     * @return int|null
     */
    public function getPercentage()
    {
        return $this->getIntegerParsedValue(self::PERCENTAGE);
    }

    /**
     * @return string|null
     */
    public function getRua()
    {
        return $this->getParsedValue(self::RUA);
    }

    /**
     * @return string|null
     */
    public function getRuf()
    {
        return $this->getParsedValue(self::RUF);
    }

    /**
     * @return string|null
     */
    public function getFo()
    {
        return $this->getParsedValue(self::FO);
    }

    /**
     * @return string|null
     */
    public function getAspf()
    {
        return $this->getParsedValue(self::ASPF);
    }

    /**
     * @return string|null
     */
    public function getAdkim()
    {
        return $this->getParsedValue(self::ADKIM);
    }

    /**
     * @return string|null
     */
    public function getReportFormat()
    {
        return $this->getParsedValue(self::REPORT_FORMAT);
    }

    /**
     * @return int|null
     */
    public function getReportInterval()
    {
        return $this->getIntegerParsedValue(self::REPORT_INTERVAL);
    }

    /**
     * @return string|null
     */
    public function getSubdomainPolicy()
    {
        return $this->getParsedValue(self::SUBDOMAIN_POLICY);
    }

}
