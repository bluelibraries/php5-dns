<?php

namespace BlueLibraries\PHP5\Dns\Handlers\Raw;

use BlueLibraries\PHP5\Dns\Handlers\DnsHandlerException;
use BlueLibraries\PHP5\Dns\Handlers\DnsHandlerTypes;
use BlueLibraries\PHP5\Dns\Records\RecordTypes;
use BlueLibraries\PHP5\Dns\Records\DnsUtils;

class RawDataResponse
{

    /**
     * @var RawDataRequest $request ;
     **/
    private $request;

    /**
     * @var false|string|null
     */
    private $rawResponse = null;

    /**
     * @var string|null
     */
    private $rawBuffer = null;

    /**
     * @var string|null
     */
    private $rawResponseHeader = null;

    /**
     * @var array
     */
    private $headerData = [];

    /**
     * @var int
     */
    private $responseCounter = 12;

    /**
     * @var array|null
     */
    private $questions = null;

    /**
     * @var array|null
     */
    private $answers = null;

    /**
     * @var string|null
     */
    private $handlerType = null;

    /**
     * @var array|null
     */
    private $lastResult = null;

    /**
     * @var int
     */
    private $lastIndex = 0;

    /**
     * @param RawDataRequest $request
     * @param $rawData
     * @param $handlerType
     * @throws DnsHandlerException
     */
    public function __construct(RawDataRequest $request, $rawData, $handlerType)
    {
        if (strlen($rawData) >= 12) {
            $this->rawResponseHeader = substr($rawData, 0, 12);
            $this->rawResponse = substr($rawData, 12);
            $this->rawBuffer = $rawData;
            $this->headerData = unpack("nid/nspec/nqdcount/nancount/nnscount/narcount", $this->rawResponseHeader);
        }
        $this->request = $request;
        $this->handlerType = $handlerType;
        $this->validateHeaderData();
    }

    /**
     * @return void
     * @throws DnsHandlerException
     */
    private function validateHeaderData()
    {
        if (
            $this->handlerType === DnsHandlerTypes::UDP
            && $this->isHeaderTruncated()
        ) {
            $typeName = is_int($this->request->getTypeId())
                ? RecordTypes::getName($this->request->getTypeId())
                : 'n/a';
            throw new DnsHandlerException(
                'Response too big, truncation detected, retry TCP or DI... or else!' .
                ' domain: ' . json_encode($this->request->getDomain() .
                    ' typeId:' . json_encode($this->request->getTypeId()) .
                    ' typeName: ' . $typeName
                ),
                DnsHandlerException::TRUNCATION_DETECTED
            );
        }
    }

    /**
     * @return int
     */
    private function getHeaderAnswersCount()
    {
        return isset($this->headerData['ancount']) ? (int)$this->headerData['ancount'] : 0;
    }

    /**
     * @return int
     */
    private function getHeaderQuestionsCount()
    {
        return isset($this->headerData['qdcount']) ? (int)$this->headerData['qdcount'] : null;
    }

    /**
     * @param int $count
     * @param int|null $offset
     * @return string
     */
    function readResponse($count = 1, $offset = null)
    {
        if (is_null($offset)) {
            $return = substr($this->rawBuffer, $this->responseCounter, $count);
            $this->responseCounter += $count;
        } else {
            $return = substr($this->rawBuffer, $offset, $count);
        }
        return $return;
    }

    /**
     * @return string
     */
    function getDomainLabel()
    {
        $count = 0;
        $labels = $this->getDomainLabels($this->responseCounter, $count);
        $domain = implode(".", $labels);
        $this->responseCounter += $count;
        return $domain;
    }

    /**
     * @param int $offset
     * @param int $counter
     * @return array
     */
    function getDomainLabels($offset, &$counter = 0)
    {
        $labels = [];
        $offsetStart = $offset;
        $return = false;

        while (!$return) {
            $labelLength = ord($this->readResponse(1, $offset++));
            if ($labelLength <= 0) {
                $return = true;
            } // end of data
            else if ($labelLength < 64) // uncompressed data
            {
                $labels[] = $this->readResponse($labelLength, $offset);
                $offset += $labelLength;
            } else // labelLength >= 64 --> pointer
            {
                $nextItem = $this->readResponse(1, $offset++);
                $pointerOffset = (($labelLength & 0x3f) << 8) + ord($nextItem);
                $labelsPointers = $this->getDomainLabels($pointerOffset);
                foreach ($labelsPointers as $labelPointer) {
                    $labels[] = $labelPointer;
                }
                $return = true;
            }
        }
        $counter = $offset - $offsetStart;

        return array_map(function ($item) {
            return str_replace('.', '\.', $item);
        }, $labels);
    }

    /**
     * @return array
     * @throws DnsHandlerException
     */
    function readRecord()
    {

        $domain = $this->getDomainLabel();

        // 10 byte header
        $headerResponse = $this->readResponse(10);

        $headerLen = strlen($headerResponse);
        if ($headerLen < 10) {
            throw new DnsHandlerException(
                'Unable to parse header data, it\'s length must be 10, got: ' .
                $headerLen . ' bytes, label: ' . json_encode($domain),
                DnsHandlerException::ERR_INVALID_RECORD_HEADER_LENGTH
            );
        }

        $headerData = unpack("ntype/nclass/Nttl/nlength", $headerResponse);

        $typeId = $headerData['type'];
        $typeName = RecordTypes::getName($typeId);

        $result = [
            'host'  => strtolower($domain),
            'ttl'   => $headerData['ttl'],
            'class' => RawClassTypes::getClassNameByRawType($headerData['class']),
            'type'  => $typeName,
        ];

        switch ($typeId) {

            case RecordTypes::A:
                $ipBinary = $this->readResponse(4);
                $result['ip'] = @inet_ntop($ipBinary);
                break;

            case RecordTypes::AAAA:
                $ipBinary = $this->readResponse(16);
                $result['ipv6'] = DnsUtils::ipV6Shortener(inet_ntop($ipBinary));
                break;

            case RecordTypes::CNAME:
            case RecordTypes::PTR:
            case RecordTypes::NS:
                $result['target'] = strtolower($this->getDomainLabel());
                break;

            case RecordTypes::DNSKEY:
            case RecordTypes::CDNSKEY:
                $values = unpack("nflags/cprotocol/calgo", $this->readResponse(4));
                $result['flags'] = $values['flags'];
                $result['protocol'] = (int)$values['protocol'];
                $result['algorithm'] = $values['algo'];
                $result['public-key'] =
                    DnsUtils::getSplitSignature(base64_encode($this->readResponse($headerData['length'] - 4)), 56);
                break;

            case RecordTypes::MX:
                $values = unpack("npri", $this->readResponse(2));
                $result['pri'] = $values['pri'];
                $result['target'] = strtolower($this->getDomainLabel());
                break;

            case RecordTypes::SOA:
                $values = $this->getDomainLabel();
                $responsible = $this->getDomainLabel();
                $buffer = $this->readResponse(20);
                $resultData = unpack("Nserial/Nrefresh/Nretry/Nexpire/Nminttl", $buffer); // butfix to NNNNN from nnNNN for 1.01
                $result['mname'] = strtolower($values);
                $result['rname'] = strtolower($responsible);
                $result['serial'] = $resultData['serial'];
                $result['refresh'] = $resultData['refresh'];
                $result['retry'] = $resultData['retry'];
                $result['expire'] = $resultData['expire'];
                $result['minimum-ttl'] = $resultData['minttl'];
                break;

            case RecordTypes::SRV:
                $response = $this->readResponse(6);
                $values = unpack("npriority/nweight/nport", $response);
                $result['pri'] = $values['priority'];
                $result['weight'] = $values['weight'];
                $result['port'] = $values['port'];
                $result['target'] = strtolower($this->getDomainLabel());
                break;

            case RecordTypes::TXT:
                $strLen = ord($this->readResponse());
                $text = $this->readResponse($strLen);
                $result['txt'] = DnsUtils::sanitizeRecordTxt($text);
                break;

            case RecordTypes::DEPRECATED_SPF:
                $result['type'] = 'TXT';
                $strLen = ord($this->readResponse());
                $result['txt'] = DnsUtils::sanitizeRecordTxt($this->readResponse($strLen));
                break;

            case RecordTypes::CAA:
                $values = $this->readResponse();
                $values = unpack("Cflags", $values);
                $strLen = ord($this->readResponse(1));
                $tags = $this->readResponse($strLen);
                $dif = $headerData['length'] - 2 - strlen($tags);
                $value = $this->readResponse($dif);
                $result['flags'] = $values['flags'];
                $result['tag'] = $tags;
                $result['value'] = $value;
                break;

            case RecordTypes::DS:
            case RecordTypes::CDS:
                $response = $this->readResponse($headerData['length']);
                $values = unpack("ntag/calgo/ctype/H*digest",
                    $response
                );

                $result['key-tag'] = is_numeric($values['tag']) ? (int)$values['tag'] : null;
                $result['algorithm'] = is_numeric($values['algo']) ? (int)$values['algo'] : null;
                $result['algorithm-digest'] = is_numeric($values['type']) ? (int)$values['type'] : null;
                $fullDigest = strtoupper($values['digest']);
                $result['digest'] = DnsUtils::getSplitSignature($fullDigest, 56);
                break;

            case RecordTypes::RRSIG:
                $readResponse = $this->readResponse($headerData['length']);
                $values = unpack("ntype/calgo/clabels/Nttl/Nexpire/Ninception/ntag",
                    $readResponse
                );

                $readResponseLen = strLen($readResponse); // 12 = original size

                $lastOffset = 0;
                $label = implode('.', DnsUtils::getConsecutiveLabels(substr($readResponse, 18), $lastOffset));
                $newOffset = 18 + $lastOffset;
                $signature = base64_encode(substr($readResponse, -($readResponseLen - $newOffset)));

                $result['type-covered'] = RecordTypes::getName($values['type']);
                $result['algorithm'] = $values['algo'];
                $result['labels-number'] = $values['labels'];
                $result['original-ttl'] = $values['ttl'];
                $result['signature-expiration'] = DnsUtils::getHumanReadableDateTime($values['expire']);
                $result['signature-creation'] = DnsUtils::getHumanReadableDateTime($values['inception']);
                $result['key-tag'] = $values['tag'];
                $result['signer-name'] = $label;
                $result['signature'] = DnsUtils::getSplitSignature($signature, 56);
                break;

            case RecordTypes::NSEC:
                $response = $this->readResponse($headerData['length']);
                $responseLen = strlen($response);
                $lastOffset = 0;
                $label = implode('.', DnsUtils::getConsecutiveLabels($response, $lastOffset));
                $blocksString = substr($response, -$responseLen + $lastOffset + 1);
                $blocks = DnsUtils::getBlocks($blocksString);
                $recordTypes = '';

                foreach ($blocks as $key => $block) {
                    $blockOffset = $key * 256;
                    $blockData = DnsUtils::getBitsFromString($block);
                    $typesFound = DnsUtils::getRecordsNamesFromBinary($blockData, $blockOffset);
                    $recordTypes .=
                        empty($recordTypes) ? $typesFound : ' ' . $typesFound;
                }

                $result['next-authoritative-name'] = $label;
                $result['types'] = $recordTypes;
                break;

            case RecordTypes::NSEC3PARAM:
                $response = $this->readResponse($headerData['length']);
                $values = unpack("Calgo/nflags/citerations/clength/H*salt",
                    $response
                );
                $result['algorithm'] = $values['algo'];
                $result['flags'] = $values['flags'];
                $result['iterations'] = $values['iterations'];
                $salt = strtoupper($values['salt']);
                $result['salt'] = $salt === '' ? '-' : $salt;
                break;

            case RecordTypes::HTTPS:
                $response = $this->readResponse($headerData['length']);
                $values = unpack('H*data', $response);
                $rawData = $values['data'];
                $originalLen = strlen($response);
                $result['separator'] = '\#';
                $result['original-length'] = $originalLen;
                $result['data'] = strtoupper(DnsUtils::getSplitSignature($rawData, 56));
                break;

            case RecordTypes::NAPTR:
                $response = $this->readResponse($headerData['length']);
                $values = unpack('norder/npreference', $response);
                $lastOffset = 4;
                $newOffset = 0;

                $labels = DnsUtils::getConsecutiveLabels($response, $newOffset, $lastOffset, 3);

                $flag = isset($labels[0]) ? $labels[0] : '';
                $service = isset($labels[1]) ? $labels[1] : '';
                $regexp = isset($labels[2]) ? $labels[2] : '';
                $replacement = isset($labels[3]) ? $labels[3] : '';

                $result['order'] = $values['order'];
                $result['pref'] = $values['preference'];
                $result['flag'] = $flag;
                $result['services'] = $service;
                $result['regex'] = $regexp;
                $result['replacement'] = $replacement;
                break;

            case RecordTypes::HINFO:
                $response = $this->readResponse($headerData['length']);
                $values = DnsUtils::getBlocks($response);
                $result['hardware'] = isset($values[0]) ? $values[0] : '';
                $result['os'] = isset($values[1]) ? $values[1] : '';
                break;

            default:
                throw new DnsHandlerException(
                    'Not implemented type: ' . json_encode($typeId) . PHP_EOL .
                    ' headerData:' . json_encode($headerData),
                    DnsHandlerException::TYPE_ID_NOT_IMPLEMENTED
                );

        }

        $this->lastResult = $result;
        $this->lastIndex++;

        return $result;
    }


    /**
     * @return array
     * @throws DnsHandlerException
     */
    private function readAnswers()
    {

        $answersCount = $this->getHeaderAnswersCount();

        if ($answersCount === 0) {
            return [];
        }

        $result = [];

        for ($index = 0; $index < $answersCount; $index++) {
            $record = $this->readRecord();
            if (!empty($record)) {
                $result [] = $record;
            }
        }

        return $result;
    }

    /**
     * @return array|string[]
     */
    protected function readQuestions()
    {
        if (empty($this->rawResponse)) {
            return [];
        }

        $questionsCount = $this->getHeaderQuestionsCount();

        if ($questionsCount === 0) {
            return [];
        }

        do {
            $byteValue = ord($this->readResponse(1));
        } while ($byteValue != 0);

        return [$this->readResponse(4)];
    }

    /**
     * @return array
     * @throws DnsHandlerException
     */
    public function getData()
    {
        $this->questions = $this->readQuestions();

        if (empty($this->questions)) {
            return [];
        }

        $this->answers = $this->readAnswers();

        return $this->answers;
    }

    /**
     * @return bool
     */
    private function isHeaderTruncated()
    {
        return isset($this->headerData['spec'])
            ? (($this->headerData['spec'] >> 9) & 1)
            : true;
    }

}
