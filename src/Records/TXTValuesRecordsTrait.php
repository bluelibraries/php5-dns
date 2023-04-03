<?php

namespace BlueLibraries\PHP5\Dns\Records;

use BlueLibraries\PHP5\Dns\Regex;

trait TXTValuesRecordsTrait
{

    /**
     * @var array
     */
    private $parsedValues = [];

    /**
     * @return bool
     */
    public function parseValues()
    {
        $txt = trim($this->getTxt());

        if (empty($txt)) {
            return false;
        }

        if ($this->isParsedValue()) {
            return true;
        }

        $value = DnsUtils::sanitizeTextLineSeparators($txt);
        preg_match_all(Regex::TXT_VALUES, $value, $matches);

        $result = [];

        foreach ($matches[0] as $match) {
            $matchData = explode('=', $match);
            if (!isset($matchData[1])) {
                return false;
            }
            $result[strtolower($matchData[0])] = trim($matchData[1]);
        }

        $this->parsedValues = $result;
        $this->parsedValues['internalHash'] = $this->getValueHash();

        if (!preg_match($this->txtRegex, $txt)) {
            return false;
        }

        return true;
    }

    /**
     * @return string
     */
    private function getValueHash()
    {
        return md5($this->getTxt());
    }

    /**
     * @return bool
     */
    private function isParsedValue()
    {
        $hash = isset($this->parsedValues['internalHash']) ? $this->parsedValues['internalHash'] : null;

        if (is_null($hash)) {
            return false;
        }

        return $hash === $this->getValueHash();
    }

    /**
     * @param string $key
     * @return string|null
     */
    private function getParsedValue($key)
    {
        $this->parseValues();
        return isset($this->parsedValues[$key]) ? $this->parsedValues[$key] : null;
    }

    /**
     * @param string $key
     * @return int|null
     */
    private function getIntegerParsedValue($key)
    {
        $result = $this->getParsedValue($key);
        return is_null($result)
            ? $result
            : (int)$result;
    }

}
