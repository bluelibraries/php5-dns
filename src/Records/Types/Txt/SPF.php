<?php

namespace BlueLibraries\PHP5\Dns\Records\Types\Txt;

use BlueLibraries\PHP5\Dns\Records\DnsUtils;
use BlueLibraries\PHP5\Dns\Records\ExtendedRecordTrait;
use BlueLibraries\PHP5\Dns\Records\ExtendedTxtRecords;
use BlueLibraries\PHP5\Dns\Records\Types\TXT;
use BlueLibraries\PHP5\Dns\Regex;

/**
 * Sender Policy Framework
 */
class SPF extends TXT
{

    use ExtendedRecordTrait;

    /**
     * @param array|null $data
     */
    public function __construct($data = [])
    {

        if (!empty($data['txt'])) {
            $data['txt'] = DnsUtils::sanitizeTextLineSeparators($data['txt']);
        }
        parent::__construct($data);
    }

    /**
     * @return string|null
     */
    private function getExtendedTypeName()
    {
        return ExtendedTxtRecords::SPF;
    }

    /**
     * @return array
     */
    public function getHosts()
    {
        if (empty($this->getTxt())) {
            return [];
        }

        preg_match_all(Regex::WORDS_SEPARATED_SPACE, $this->getTxt(), $matches);

        $words = $matches[0];

        if ($words[0] !== 'v=spf1') {
            return [];
        }

        array_shift($words);

        return $words;
    }

}
