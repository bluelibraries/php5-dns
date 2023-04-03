<?php

namespace BlueLibraries\PHP5\Dns\Records\Types\Txt;

use BlueLibraries\PHP5\Dns\Records\ExtendedRecordTrait;
use BlueLibraries\PHP5\Dns\Records\ExtendedTxtRecords;
use BlueLibraries\PHP5\Dns\Records\TXTValuesRecordsTrait;
use BlueLibraries\PHP5\Dns\Records\Types\TXT;
use BlueLibraries\PHP5\Dns\Regex;

class TLSReporting extends TXT
{
    use ExtendedRecordTrait;
    use TXTValuesRecordsTrait;

    const VERSION = 'v';
    const RUA = 'rua';

    private $txtRegex = Regex::TLS_REPORTING;

    /**
     * @return string|null
     */
    private function getExtendedTypeName()
    {
        return ExtendedTxtRecords::TLS_REPORTING;
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
    public function getRua()
    {
        return $this->getParsedValue(self::RUA);
    }

}
