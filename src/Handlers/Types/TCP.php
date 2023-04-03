<?php

namespace BlueLibraries\PHP5\Dns\Handlers\Types;

use BlueLibraries\PHP5\Dns\Handlers\AbstractDnsHandler;
use BlueLibraries\PHP5\Dns\Handlers\DnsHandlerException;
use BlueLibraries\PHP5\Dns\Handlers\DnsHandlerTypes;
use BlueLibraries\PHP5\Dns\Handlers\Raw\RawDataException;
use BlueLibraries\PHP5\Dns\Handlers\Raw\RawDataRequest;
use BlueLibraries\PHP5\Dns\Handlers\Raw\RawDataResponse;

class TCP extends AbstractDnsHandler
{

    private $port = 53;

    /**
     * @var mixed
     */
    private $socket = null;

    /**
     * @return string
     */
    public function getType()
    {
        return DnsHandlerTypes::TCP;
    }

    /**
     * @return bool
     */
    function canUseIt()
    {
        return function_exists('fsockopen');
    }

    private function getSocket()
    {

        if (!is_null($this->socket)) {
            return $this->socket;
        }

        $result = fsockopen(
            $this->nameserver,
            $this->port,
            $errorCode,
            $errorMessage,
            $this->timeout
        );

        return $this->socket = ($result === false ? null : $result);
    }

    /**
     * @param int $port
     * @return self
     */
    public function setPort($port)
    {
        $this->port = $port;
        return $this;
    }

    /**
     * @param string $hostName
     * @param int $typeId
     * @param int|null $retry
     * @return RawDataResponse|null
     * @throws DnsHandlerException
     * @throws RawDataException
     */
    protected function query($hostName, $typeId, $retry = 0)
    {

        $request = new RawDataRequest($hostName, $typeId, $this->timeout);

        $header = $request->generateHeader();
        $headerLen = strlen($header);
        $headerBinLen = $request->getBinaryHeaderLength($headerLen);

        if (!$this->write($headerBinLen)) // write the socket
        {
            if ($retry < $this->retries) {
                return $this->query($hostName, $typeId, $retry + 1);
            }
            $this->close();
            throw new DnsHandlerException(
                "Failed to write question length to TCP socket",
                DnsHandlerException::ERR_UNABLE_TO_WRITE_QUESTION_LENGTH_TO_TCP_SOCKET
            );
        }

        if (!$this->write($header, $headerLen)) {
            if ($retry < $this->retries) {
                return $this->query($hostName, $typeId, $retry + 1);
            }
            $this->close();
            throw new DnsHandlerException(
                "Failed to write question to TCP socket",
                DnsHandlerException::ERR_UNABLE_TO_WRITE_QUESTION_TO_TCP_SOCKET
            );
        }

        if (!$returnLen = $this->read(2)) {
            if ($retry < $this->retries) {
                return $this->query($hostName, $typeId, $retry + 1);
            }
            $this->close();
            throw new DnsHandlerException(
                "Failed to read size from TCP socket",
                DnsHandlerException::ERR_UNABLE_TO_READ_SIZE_FROM_TCP_SOCKET
            );
        }

        $returnLenData = unpack("nlength", $returnLen);
        $dataLen = $returnLenData['length'];
        $rawDataResponse = $this->read($dataLen);
        $this->close();

        if ($rawDataResponse === null) {
            if ($retry < $this->retries) {
                return $this->query($hostName, $typeId, $retry + 1);
            }
            return null;
        }

        return new RawDataResponse($request, $rawDataResponse, $this->getType());
    }

    private function close()
    {
        if (is_null($this->socket)) {
            return;
        }
        fclose($this->socket);
        $this->socket = null;
    }

    /**
     * @param $data
     * @param $length
     * @return int|null
     */
    protected function write($data, $length = null)
    {
        $result = is_null($length)
            ? fwrite($this->getSocket(), $data)
            : fwrite($this->getSocket(), $data, $length);
        return is_int($result) ? $result : null;
    }

    /**
     * @param int $length
     * @return string|null
     */
    public function read($length)
    {
        $result = fread($this->getSocket(), $length);
        return is_string($result) ? $result : null;
    }

    /**
     * @param string $host
     * @param int $typeId
     * @return array
     * @throws DnsHandlerException
     * @throws RawDataException
     */
    public function getDnsData($host, $typeId)
    {

        $this->validateParams($host, $typeId);
        $result = $this->query($host, $typeId);

        if (is_null($result)) {
            return [];
        }

        return $result->getData();
    }

}
