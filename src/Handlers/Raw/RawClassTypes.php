<?php

namespace BlueLibraries\PHP5\Dns\Handlers\Raw;

class RawClassTypes
{

    const IN = 1;
    const CS = 2;
    const CH = 3;
    const HS = 4;

    /**
     * @var int[]
     */
    private static $rawClassTypes = [
        'IN' => self::IN, // Internet
        'CS' => self::CS, // CSNet -> obsolete
        'CH' => self::CH, // Chaos
        'HS' => self::HS, // Hesiod
    ];

    /**
     * @return int[]
     */
    public static function getRawTypes()
    {
        return self::$rawClassTypes;
    }

    /**
     * @param int $rawClassId
     * @return string|null
     */
    public static function getClassNameByRawType($rawClassId)
    {
        foreach (self::$rawClassTypes as $key => $type) {
            if ($rawClassId === $type) {
                return $key;
            }
        }
        return null;
    }

}
