<?php

namespace BlueLibraries\PHP5\Dns\Records;

use JsonSerializable;

abstract class AbstractRecord implements RecordInterface, JsonSerializable
{
    /**
     * @var array
     */
    protected $data = [];

    /**
     * @return int
     */
    public abstract function getTypeId();

    /**
     * @return string
     */
    public function getTypeName()
    {
        return RecordTypes::getName($this->getTypeId());
    }

    /**
     * @param array|null $data
     */
    public function __construct(array $data = [])
    {
        $this->setData($data);
    }

    /**
     * @param array $data
     * @return $this
     */
    public function setData(array $data)
    {
        $this->data = $data;

        if (!isset($this->data['host'])) {
            $this->data['host'] = '';
        } else {
            $this->data['host'] = strtolower(trim($this->data['host']));
        }

        if (!isset($this->data['ttl'])) {
            $this->data['ttl'] = 0;
        }

        if (!isset($this->data['class'])) {
            $this->data['class'] = 'IN';
        }

        if (!isset($this->data['type'])) {
            $this->data['type'] = RecordTypes::getName($this->getTypeId());
        }

        return $this;
    }

    /**
     * @return array
     */
    public function toArray()
    {
        return $this->data;
    }

    /**
     * @return string|null
     */
    public function getHost()
    {
        return isset($this->data['host']) ? $this->data['host'] : null;
    }

    /**
     * @return string|null
     */
    public function getClass()
    {
        return isset($this->data['class']) ? $this->data['class'] : null;
    }

    /**
     * @return int|null
     */
    public function getTtl()
    {
        return isset($this->data['ttl'])
            ? (int)$this->data['ttl']
            : null;
    }

    /**
     * @param string|null $separator
     * @return string
     */
    public function toString($separator = ' ')
    {
        return implode(
            $separator,
            DnsRecordProperties::getFilteredProperties(
                $this->getTypeId(),
                $this->getParsedData($this->data)
            )
        );
    }

    /**
     * @return string
     */
    public function __toString()
    {
        return $this->toString();
    }

    /**
     * @param array $array
     * @return string
     */
    private function makeString(array $array)
    {
        $result = [];

        foreach ($array as $key => $value) {
            $result[$key] = is_array($value) ? $this->makeString($value) : $value;
        }

        return implode('', $result);
    }

    /**
     * @return string
     */
    public function getHash()
    {
        return md5($this->makeString($this->toBaseArray()));
    }

    /**
     * @return array
     */
    public function toBaseArray()
    {
        $data = $this->toArray();
        $expiringKeys = DnsRecordProperties::getExcludedBaseProperties();

        foreach ($expiringKeys as $expiringKey) {
            if (isset($data[$expiringKey])) {
                unset($data[$expiringKey]);
            }
        }

        return $data;
    }

    /**
     * @param array $data
     * @return array
     */
    private function getParsedData(array $data)
    {
        $result = [];

        foreach ($data as $propertyName => $value) {
            $result[$propertyName] = $this->getParsedProperty($propertyName, $value);
        }

        return $result;
    }

    /**
     * @param string $propertyName
     * @param mixed $value
     * @return string
     */
    protected function getParsedProperty($propertyName, $value)
    {

        $result = DnsRecordProperties::isWrappedProperty($propertyName)
            ? '"' . DnsUtils::sanitizeRecordTxt($value) . '"'
            : $value;

        if (DnsRecordProperties::isUnWrappedDotValue($propertyName, $value)) {
            $result = trim($value, '"');
        }

        return $result;
    }

    /**
     * @return array
     */
    public function jsonSerialize()
    {
        return $this->toArray();
    }

}
