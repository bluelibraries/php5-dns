<?php

namespace BlueLibraries\PHP5\Dns;

use BlueLibraries\PHP5\Dns\Handlers\DnsHandlerException;
use BlueLibraries\PHP5\Dns\Handlers\DnsHandlerInterface;
use BlueLibraries\PHP5\Dns\Handlers\Types\TCP;
use BlueLibraries\PHP5\Dns\Records\DnsUtils;
use BlueLibraries\PHP5\Dns\Records\RecordException;
use BlueLibraries\PHP5\Dns\Records\RecordFactory;
use BlueLibraries\PHP5\Dns\Records\RecordInterface;

class DnsRecords
{
    /** 
  @var DnsHandlerInterface $handler; 
**/ 
 private $handler;
    /** 
  @var RecordFactory $factory; 
**/ 
 private $factory;

    /**
     * @param DnsHandlerInterface|null $handler
     * @param RecordFactory|null $factory
     */
    public function __construct(DnsHandlerInterface $handler = null, RecordFactory $factory = null)
    {
        if (is_null($handler)) {
            $handler = new TCP();
        }

        if (is_null($factory)) {
            $factory = new RecordFactory();
        }

        $this->handler = $handler;
        $this->factory = $factory;
    }

    /**
     * @return DnsHandlerInterface
     */
    public function getHandler()
    {
        return $this->handler;
    }

    /**
     * @param DnsHandlerInterface $handler
     * @return DnsRecords
     */
    public function setHandler(DnsHandlerInterface $handler)
    {
        $this->handler = $handler;
        return $this;
    }

    /**
     * @return RecordFactory
     */
    public function getFactory()
    {
        return $this->factory;
    }

    /**
     * @param RecordFactory $factory
     * @return DnsRecords
     */
    public function setFactory(RecordFactory $factory)
    {
        $this->factory = $factory;
        return $this;
    }

    /**
     * @param $host
     * @param int|array $type
     * @param $useExtendedRecords
     * @param $keepOrder
     * @param $removeDuplicates
     * @return array
     * @throws DnsHandlerException
     * @throws RecordException
     */
    public function get($host, $type, $useExtendedRecords = true, $keepOrder = true, $removeDuplicates = true)
    {
        if (is_int($type)) {
            return $this->getRecordDataForType($host, $type, $useExtendedRecords, $keepOrder);
        }

        $result = [];

        foreach ($type as $typeId) {
            $result = array_merge($result, $this->getRecordDataForType($host, $typeId, $useExtendedRecords, $keepOrder));
        }

        if ($removeDuplicates) {
            $result = DnsUtils::removeDuplicates($result);
        }

        return $result;
    }

    /**
     * @param $host
     * @param $typeId
     * @param $useExtendedRecords
     * @param $keepOrder
     * @return array|RecordInterface[]
     * @throws DnsHandlerException
     * @throws RecordException|Handlers\Raw\RawDataException
     */
    private function getRecordDataForType($host, $typeId, $useExtendedRecords, $keepOrder)
    {
        $recordsData = $this->handler->getDnsData($host, $typeId);

        if (empty($recordsData)) {
            return [];
        }

        $result = [];

        foreach ($recordsData as $recordData) {
            $record = $this->factory->create($recordData, $useExtendedRecords);
            if ($record->getTypeId() === $typeId) {
                $result[] = $record;
            }
        }

        if ($keepOrder) {
            $result = DnsUtils::sortRecords($result);
        }

        return $result;
    }

}
