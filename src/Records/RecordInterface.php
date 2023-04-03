<?php

namespace BlueLibraries\PHP5\Dns\Records;

interface RecordInterface
{
    /**
     * @param array $data
     * @return mixed
     */
    public function setData(array $data);

    /**
     * @return int
     */
    public function getTypeId();

    /**
     * @return string
     */
    public function getTypeName();

    /**
     * @return string|null
     */
    public function getHost();

    /**
     * @return string|null
     */
    public function getClass();

    /**
     * @return int|null
     */
    public function getTtl();

    /**
     * @return array
     */
    public function toArray();

    /**
     * @return array
     */
    public function toBaseArray();

    /**
     * @param $separator
     * @return string
     */
    public function toString($separator = ' ');

    /**
     * @return string
     */
    public function getHash();

}
