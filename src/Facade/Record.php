<?php

namespace BlueLibraries\PHP5\Dns\Facade;

use BlueLibraries\PHP5\Dns\Records\RecordException;
use BlueLibraries\PHP5\Dns\Records\RecordFactory;
use BlueLibraries\PHP5\Dns\Records\RecordInterface;
use BlueLibraries\PHP5\Dns\Records\StringRecordUtils;

class Record
{
    /**
     * @var RecordFactory|null
     */
    protected static $factory = null;

    /**
     * @return RecordFactory
     */
    private static function getRecordFactory()
    {
        return is_null(self::$factory)
            ? self::$factory = new RecordFactory() : self::$factory;
    }

    /**
     * @param $string
     * @param bool $asExtendedRecord
     * @return RecordInterface|null
     * @throws RecordException
     */
    public static function fromString($string, $asExtendedRecord = true)
    {
        if (empty($string)) {
            return null;
        }
        $recordData = StringRecordUtils::normalizeRawResult(
            StringRecordUtils::lineToArray($string)
        );

        if (empty($recordData)) {
            return null;
        }

        return self::getRecordFactory()->create(
            $recordData[0],
            $asExtendedRecord
        );
    }

    /**
     * @param array $array
     * @param bool $asExtendedRecord
     * @return RecordInterface|null
     * @throws RecordException
     */
    public static function fromNormalizedArray(array $array, $asExtendedRecord = true)
    {
        if (empty($array)) {
            return null;
        }
        return self::getRecordFactory()->create(
            $array,
            $asExtendedRecord
        );
    }

}
