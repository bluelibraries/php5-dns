<?php

namespace BlueLibraries\PHP5\Dns\Handlers\Raw;

use BlueLibraries\PHP5\Dns\Records\DnsUtils;

class RawDataRequest
{
    /**
     * @var int
     * See RawClassTypes.php
     */
    private $classId = 1;

    /**
     * @var string|null
     */
    private $domain = null;

    /**
     * @var int|null
     */
    private $typeId = null;

    /**
     * @var int|null
     */
    private $timeout = null;

    /**
     * @var null
     */
    private $id = null;

    /**
     * @var bool
     */
    private $isRecursionDesired = false;

    /**
     * @var bool
     */
    private $useAuthoritativeAnswer = true;

    /**
     * @var bool
     */
    private $useTruncation = false;

    /**
     * @var bool
     */
    private $useRecursionIfAvailable = false;

    /**
     * @param string|null $domain
     * @param int|null $typeId
     * @param int|null $timeout
     */
    public function __construct($domain = null, $typeId = null, $timeout = 30)
    {
        $this->domain = $domain;
        $this->typeId = $typeId;
        $this->timeout = $timeout;
    }

    /**
     * @return string|null
     */
    public function getDomain()
    {
        return $this->domain;
    }

    /**
     * @param string|null $domain
     * @return self
     */
    public function setDomain($domain)
    {
        $this->domain = $domain;
        return $this;
    }

    /**
     * @return int|null
     */
    public function getTypeId()
    {
        return $this->typeId;
    }

    /**
     * @param int|null $typeId
     * @return self
     */
    public function setTypeId($typeId)
    {
        $this->typeId = $typeId;
        return $this;
    }

    /**
     * @return int|null
     */
    public function getTimeout()
    {
        return $this->timeout;
    }

    /**
     * @param int|null $timeout
     * @return self
     */
    public function setTimeout($timeout)
    {
        $this->timeout = $timeout;
        return $this;
    }

    /**
     * @return int|null
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @param int|null $id
     * @return self
     */
    public function setId($id)
    {
        $this->id = $id;
        return $this;
    }

    /**
     * @return int
     */
    public function getClassId()
    {
        return $this->classId;
    }

    /**
     * @param $classId
     * @return self
     */
    public function setClassId($classId)
    {
        $this->classId = $classId;
        return $this;
    }

    /**
     * @return bool
     */
    public function isRecursionDesired()
    {
        return $this->isRecursionDesired;
    }

    /**
     * @param $isRecursionDesired
     * @return self
     */
    public function setIsRecursionDesired($isRecursionDesired)
    {
        $this->isRecursionDesired = $isRecursionDesired;
        return $this;
    }

    /**
     * @return bool
     */
    public function useAuthoritativeAnswer()
    {
        return $this->useAuthoritativeAnswer;
    }

    /**
     * @param $useAuthoritativeAnswer
     * @return self
     */
    public function setUseAuthoritativeAnswer($useAuthoritativeAnswer)
    {
        $this->useAuthoritativeAnswer = $useAuthoritativeAnswer;
        return $this;
    }

    /**
     * @return bool
     */
    public function useTruncation()
    {
        return $this->useTruncation;
    }

    /**
     * @param $useTruncation
     * @return self
     */
    public function setUseTruncation($useTruncation)
    {
        $this->useTruncation = $useTruncation;
        return $this;
    }

    /**
     * @return bool
     */
    public function useRecursionIfAvailable()
    {
        return $this->useRecursionIfAvailable;
    }

    /**
     * @param $useRecursionIfAvailable
     * @return self
     */
    public function setUseRecursionIfAvailable($useRecursionIfAvailable)
    {
        $this->useRecursionIfAvailable = $useRecursionIfAvailable;
        return $this;
    }

    /**
     * @return string
     * @throws RawDataException
     */
    public function generateHeader()
    {
        return $this->getBinaryId() .
            $this->getBinaryQR() .
            $this->getBinaryAuthoritativeAnswer() .
            $this->getBinaryUseTruncation() .
            $this->getBinaryRecursionDesired() .
            $this->getBinaryRecursionAvailable() .
            $this->getBinaryQuestion() .
            $this->getBinaryType() .
            $this->getBinaryClass() .
            $this->getTtl();
    }

    protected function getBinaryId()
    {
        $this->id = isset($this->id) ? (int)$this->id : rand(0, 65535);
        return (string)pack('n', $this->id);
    }

    /**
     * @return string
     */
    protected function getBinaryQR()
    {
        $flags = 0x0100 & 0x0300; // recursion & query spec mask
        $opcode = 0x0000;
        return (string)pack('n', $opcode | $flags);
    }

    /**
     * @return string
     */
    protected function getBinaryAuthoritativeAnswer()
    {
        return (string)pack('n', (int)$this->useAuthoritativeAnswer);
    }

    /**
     * @return string
     */
    protected function getBinaryUseTruncation()
    {
        return (string)pack('n', (int)$this->useTruncation);
    }

    /**
     * @return string
     */
    protected function getBinaryRecursionDesired()
    {
        return (string)pack('n', (int)$this->isRecursionDesired);
    }

    /**
     * @return string
     */
    protected function getBinaryRecursionAvailable()
    {
        return (string)pack('n', (int)$this->useRecursionIfAvailable);
    }

    /**
     * @return string|null
     * @throws RawDataException
     */
    protected function getBinaryQuestion()
    {
        $labels = $this->getLabels($this->domain);

        if (empty($labels)) {
            return null;
        }

        return implode('',
                array_map(function ($item) {
                    return pack("C", strlen($item)) . $item;
                }, $labels)
            ) . (string)pack('C', 0);
    }

    /**
     * @return string
     */
    protected function getBinaryType()
    {
        return (string)pack('n', $this->typeId);
    }

    /**
     * @return string
     * @throws RawDataException
     */
    protected function getBinaryClass()
    {
        if (!in_array($this->classId, RawClassTypes::getRawTypes())) {
            throw new RawDataException(
                'Invalid class Id, got:' . json_encode($this->classId),
                RawDataException::ERR_INVALID_CLASS_ID
            );
        }

        return (string)pack('n', $this->classId);
    }

    /**
     * @return string
     */
    protected function getTtl()
    {
        return (string)pack('N', $this->timeout);
    }

    /**
     * @param int $headerLength
     * @return string
     */
    public function getBinaryHeaderLength($headerLength)
    {
        return (string)pack("n", $headerLength);
    }

    /**
     * @param string $ip
     * @return array
     */
    protected function getLabelsFromIp($ip)
    {
        return array_merge(['in-addr', 'arpa'], array_reverse(explode('.', $ip)));
    }

    /**
     * @param $address
     * @return array
     * @throws RawDataException
     */
    protected function getLabels($address)
    {
        if (empty($address) || $address === '.') {
            return [];
        }
        if (filter_var($address, FILTER_VALIDATE_IP) !== false) {
            return $this->getLabelsFromIp($address);
        }

        if (!DnsUtils::isValidDomainOrSubdomain($address)) {
            throw new RawDataException(
                'Invalid address, it must be an IP or domain, got:' . json_encode($address),
                RawDataException::ERR_INVALID_ADDRESS
            );
        }

        return explode('.', strtolower($address));
    }

}
