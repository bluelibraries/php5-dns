<?php

namespace BlueLibraries\PHP5\Dns\Handlers\Types;

use BlueLibraries\PHP5\Dns\Handlers\AbstractDnsHandler;
use BlueLibraries\PHP5\Dns\Handlers\DnsHandlerException;
use BlueLibraries\PHP5\Dns\Handlers\DnsHandlerTypes;
use BlueLibraries\PHP5\Dns\Handlers\Raw\RawDataException;
use BlueLibraries\PHP5\Dns\Handlers\Raw\RawDataRequest;
use BlueLibraries\PHP5\Dns\Handlers\Raw\RawDataResponse;

class UDP extends AbstractDnsHandler
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
        return DnsHandlerTypes::UDP;
    }

    /**
     * @return bool
     */
    public function canUseIt()
    {
        return function_exists('socket_create');
    }

    protected function getSocket()
    {

        if (!is_null($this->socket)) {
            return $this->socket;
        }

        $result = socket_create(
            AF_INET, SOCK_DGRAM, SOL_UDP
        );

        socket_set_option($result, SOL_SOCKET, SO_RCVTIMEO, array('sec' => $this->timeout, 'usec' => 0));

        return $this->socket = ($result === false ? null : $result);
    }

    private function close()
    {
        $this->socket && socket_close($this->socket);
        $this->socket = null;
    }

    /**
     * @return int
     */
    public function getPort()
    {
        return $this->port;
    }

    /**
     * @param $port
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
     * @param int $retry
     * @return RawDataResponse|null
     * @throws DnsHandlerException
     * @throws RawDataException
     */
    protected function query($hostName, $typeId, $retry = 0)
    {
        $socket = $this->getSocket();

        if (is_null($socket)) {
            return null;
        }

        $request = new RawDataRequest($hostName, $typeId, $this->timeout);

        $header = $request->generateHeader();

        if ($socket) {
            socket_setopt($socket, SOL_SOCKET, SO_RCVBUF, 4096);
            socket_setopt($socket, SOL_SOCKET, SO_SNDBUF, 4096);
        }

        if (!$this->write($header)) {
            if ($retry < $this->retries) {
                return $this->query($hostName, $typeId, $retry + 1);
            }
            $this->close();
            throw new DnsHandlerException(
                "Failed to write question to UDP socket",
                DnsHandlerException::ERR_UNABLE_TO_WRITE_TO_UDP_SOCKET
            );
        }

        $rawDataResponse = $this->read();

        if (empty($rawDataResponse)) {
            $this->close();
            throw new DnsHandlerException(
                "Failed to read data buffer",
                DnsHandlerException::ERR_UNABLE_TO_READ_DATA_BUFFER
            );
        }

        $this->close();

        return new RawDataResponse($request, $rawDataResponse, $this->getType());
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

    /**
     * @return string|null
     */
    protected function read()
    {
        $result = socket_read($this->getSocket(), 512);
        return is_string($result) ? $result : null;
    }

    /**
     * @param string $header
     * @return int|null
     */
    protected function write($header)
    {
        $result = socket_sendto($this->getSocket(), $header, strlen($header), 0, $this->nameserver, $this->port);
        return is_int($result) ? $result : null;
    }

}
