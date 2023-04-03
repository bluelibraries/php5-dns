<?php

namespace BlueLibraries\PHP5\Dns\Records\Types;

use BlueLibraries\PHP5\Dns\Records\AbstractRecord;
use BlueLibraries\PHP5\Dns\Records\RecordTypes;

class SOA extends AbstractRecord
{

    /**
     * @return int
     */
    public function getTypeId()
    {
        return RecordTypes::SOA;
    }

    /**
     * @return string|null
     */
    public function getMasterNameServer()
    {
        return isset($this->data['mname']) ? $this->data['mname'] : null;
    }

    /**
     * @return string|null
     */
    public function getRawEmailName()
    {
        return isset($this->data['rname']) ? $this->data['rname'] : null;
    }

    /**
     * @return string|null
     */
    public function getAdministratorEmailAddress()
    {
        if (
            empty($this->data)
            || empty($this->data['rname'])
            || !is_string($this->data['rname'])
        ) {
            return null;
        }

        $parts = explode('.', $this->data['rname']);
        $partsLength = count($parts);

        if ($partsLength < 3) {
            return null;
        }

        $result = '';

        foreach ($parts as $key => $part) {
            $separator = $key === 0 ?
                ''
                : ($key === ($partsLength - 2) ? '@' : '.');
            $result .= $separator . $part;
        }

        return $result;
    }


    /**
     * @return int|null
     */
    public function getSerial()
    {
        return isset($this->data['serial']) ? (int)$this->data['serial'] : null;
    }

    /**
     * @return int|null
     */
    public function getRefresh()
    {
        return isset($this->data['refresh']) ? (int)$this->data['refresh'] : null;
    }

    /**
     * @return int|null
     */
    public function getRetry()
    {
        return isset($this->data['retry']) ? (int)$this->data['retry'] : null;
    }

    /**
     * @return int|null
     */
    public function getExpire()
    {
        return isset($this->data['expire']) ? (int)$this->data['expire'] : null;
    }

    /**
     * @return int|null
     */
    public function getMinimumTtl()
    {
        return isset($this->data['minimum-ttl']) ? (int)$this->data['minimum-ttl'] : null;
    }

}
