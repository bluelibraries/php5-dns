<?php

namespace BlueLibraries\PHP5\Dns\Test\Unit\Handlers\Raw;

use BlueLibraries\PHP5\Dns\Handlers\DnsHandlerException;
use BlueLibraries\PHP5\Dns\Handlers\DnsHandlerTypes;
use BlueLibraries\PHP5\Dns\Handlers\Raw\RawDataRequest;
use BlueLibraries\PHP5\Dns\Handlers\Raw\RawDataResponse;
use BlueLibraries\PHP5\Dns\Records\RecordTypes;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

class RawDataResponseTest extends TestCase
{

    /** 
  @var RawDataRequest $request; 
**/ 
 private $request;

    public function setUp()
    {
        parent::setUp();
        $this->request = $this->getMockBuilder(RawDataRequest::class)
            ->getMock();
    }


    /**
     * @return void
     * @throws DnsHandlerException
     * @expectedException BlueLibraries\PHP5\Dns\Handlers\DnsHandlerException
     * @expectedExceptionCode BlueLibraries\PHP5\Dns\Handlers\DnsHandlerException::TRUNCATION_DETECTED
     * @expectedExceptionMessage Response too big, truncation detected, retry TCP or DI... or else! domain: " typeId:null typeName: n\/a"
     */
    public function testIsUDPTruncatedHeaderThrowsException()
    {
        new RawDataResponse(
            $this->request,
            '',
            DnsHandlerTypes::UDP
        );
    }

    public function testEmptyRawResponse()
    {
        /**
         * @var RawDataResponse|MockObject $subject
         */
        $subject = $this->getMockBuilder(RawDataResponse::class)
            ->disableOriginalConstructor()
            ->setMethods(['getHeaderQuestionsCount'])
            ->getMock();
        $this->assertSame([], $subject->getData());
    }


    public function testEmptyQuestionsCountNoRawResponse()
    {
        /**
         * @var RawDataResponse|MockObject $subject
         */
        $subject = $this->getMockBuilder(RawDataResponse::class)
            ->setMethods(['readQuestions'])
            ->disableOriginalConstructor()
            ->getMock();
        $subject->method('readQuestions')
            ->willReturn([]);
        $this->assertSame([], $subject->getData());
    }


    /**
     * @return void
     * @throws DnsHandlerException
     * @expectedException BlueLibraries\PHP5\Dns\Handlers\DnsHandlerException
     * @expectedExceptionCode BlueLibraries\PHP5\Dns\Handlers\DnsHandlerException::TRUNCATION_DETECTED
     * @expectedExceptionMessage Response too big, truncation detected, retry TCP or DI... or else! domain: " typeId:16 typeName: TXT"
     */
    public function testIsUDPTruncatedHeaderValidRecordTypeIdThrowsException()
    {
        $this->request->method('getTypeId')
            ->willReturn(RecordTypes::TXT);
        new RawDataResponse(
            $this->request,
            '',
            DnsHandlerTypes::UDP
        );
    }

    /**
     * @throws DnsHandlerException
     */
    public function testGetDataValidData()
    {
        $this->request->method('getTypeId')
            ->willReturn(RecordTypes::TXT);
        $response = new RawDataResponse(
            $this->request,
            base64_decode('C4+BgAABAAIAAAAACW1ldGFsbGljYQNjb20AAAEAAcAMAAEAAQAAUtYABGgT7U7ADAABAAEAAFLWAARoE+xO'),
            DnsHandlerTypes::UDP
        );
        $this->assertSame([
            [
                'host'  => 'metallica.com',
                'ttl'   => 21206,
                'class' => 'IN',
                'type'  => 'A',
                'ip'    => '104.19.237.78',
            ],
            [
                'host'  => 'metallica.com',
                'ttl'   => 21206,
                'class' => 'IN',
                'type'  => 'A',
                'ip'    => '104.19.236.78',
            ]
        ], $response->getData());
    }

    /**
     * @return void
     * @throws DnsHandlerException
     * @expectedException BlueLibraries\PHP5\Dns\Handlers\DnsHandlerException
     * @expectedExceptionCode BlueLibraries\PHP5\Dns\Handlers\DnsHandlerException::ERR_INVALID_RECORD_HEADER_LENGTH
     * @expectedExceptionMessage Unable to parse header data, it's length must be 10, got: 0 bytes, label: ""
     */
    public function testInvalidHeaderLengthThrowsException()
    {
        $this->request->method('getTypeId')
            ->willReturn(RecordTypes::TXT);
        $response = new RawDataResponse(
            $this->request,
            base64_decode('C4+BgAABAAIAAAAACW1ldGFsbGljYQNjb20AAAEAAcAMAAEAAQAAUtYABGgT7U'),
            DnsHandlerTypes::UDP
        );
        $response->getData();
    }

    /**
     * @return array
     */
    public static function rawResponsesDataProvider()
    {

        $records = [
            RecordTypes::DNSKEY,
            RecordTypes::CDNSKEY,
            RecordTypes::SRV,
            RecordTypes::DEPRECATED_SPF,
            RecordTypes::DS,
            RecordTypes::CDS,
            RecordTypes::RRSIG,
            RecordTypes::NSEC,
            RecordTypes::NSEC3PARAM,
            RecordTypes::HTTPS,
            RecordTypes::NAPTR,
            RecordTypes::A,
            RecordTypes::CAA,
            RecordTypes::AAAA,
            RecordTypes::NS,
            RecordTypes::SOA,
            RecordTypes::TXT,
            RecordTypes::CNAME,
            RecordTypes::MX,
            RecordTypes::HINFO,
        ];

        $path = dirname(__FILE__) . '../../../Data/responses/';

        return array_map(function ($item) use ($path) {

            return
                json_decode(
                    file_get_contents(
                        $path . strtolower(RecordTypes::getName($item)) . '.json'
                    ),
                    true);
        }, $records);
    }

    /**
     * @throws DnsHandlerException
     * @dataProvider rawResponsesDataProvider
     */
    public function testRawResponses($data, $typeId, $expected)
    {
        $subject = new RawDataResponse($this->request, base64_decode($data), DnsHandlerTypes::TCP);
        $this->assertSame($expected, $subject->getData(), "TypeId: " . $typeId);
    }

    /**
     * @return void
     * @throws DnsHandlerException
     * @expectedException BlueLibraries\PHP5\Dns\Handlers\DnsHandlerException
     * @expectedExceptionMessage Not implemented type: 65534
     * @expectedExceptionCode BlueLibraries\PHP5\Dns\Handlers\DnsHandlerException::TYPE_ID_NOT_IMPLEMENTED
     */
    public function testNotImplementedType64435Response()
    {
        $data = json_decode(
            file_get_contents(
                dirname(__FILE__) . '../../../Data/responses/type65534.json'
            ),
            true);
        $subject = new RawDataResponse($this->request, base64_decode($data['rawData']), DnsHandlerTypes::TCP);
        self::assertSame([], $subject->getData());
    }

}
