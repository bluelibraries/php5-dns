<?php

namespace BlueLibraries\PHP5\Dns\Records\Types\Txt;

use BlueLibraries\PHP5\Dns\Records\ExtendedRecordTrait;
use BlueLibraries\PHP5\Dns\Records\ExtendedTxtRecords;
use BlueLibraries\PHP5\Dns\Records\TXTValuesRecordsTrait;
use BlueLibraries\PHP5\Dns\Records\Types\TXT;
use BlueLibraries\PHP5\Dns\Regex;

class MtaSts extends TXT
{
    use ExtendedRecordTrait;
    use TXTValuesRecordsTrait;

    const VERSION = 'v';
    const ID = 'id';

    private $txtRegex = Regex::MTA_STS_RECORD;

    /**
     * @return string|null
     */
    private function getExtendedTypeName()
    {
        return ExtendedTxtRecords::MTA_STS_REPORTING;
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
    public function getId()
    {
        return $this->getParsedValue(self::ID);
    }

}
