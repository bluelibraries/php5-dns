<?php

namespace BlueLibraries\PHP5\Dns\Records\Types\Txt;

use BlueLibraries\PHP5\Dns\Records\ExtendedRecordTrait;
use BlueLibraries\PHP5\Dns\Records\ExtendedTxtRecords;
use BlueLibraries\PHP5\Dns\Records\Types\TXT;

class DomainVerification extends TXT
{

    use ExtendedRecordTrait;

    /**
     * @return string|null
     */
    public function getExtendedTypeName()
    {
        return ExtendedTxtRecords::DOMAIN_VERIFICATION;
    }

    /**
     * @return string|null
     */
    public function getProvider()
    {
        return ExtendedTxtRecords::getSiteVerification($this->getTxt());
    }

    /**
     * @return string
     */
    public function getValue()
    {
        return ExtendedTxtRecords::getSiteVerificationValue($this->getTxt());
    }

}
