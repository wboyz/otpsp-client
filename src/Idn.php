<?php

declare(strict_types=1);

namespace Cheppers\OtpClient;

class Idn extends Transaction
{
    /**
     * @var string
     */
    public $targetUrl = '';

    /**
     * @var string
     */
    public $commMethod = 'idn';

    /**
     * @var array
     */
    public $idnRequest = [];

    /**
     * @var array
     */
    public $hashFields = [
        "MERCHANT",
        "ORDER_REF",
        "ORDER_AMOUNT",
        "ORDER_CURRENCY",
        "IDN_DATE"
    ];

    /**
     * @var array
     */
    protected $validFields = [
        "MERCHANT"       => ["type" => "single", "paramName" => "merchantId", "required" => true],
        "ORDER_REF"      => ["type" => "single", "paramName" => "orderRef", "required" => true],
        "ORDER_AMOUNT"   => ["type" => "single", "paramName" => "amount", "required" => true],
        "ORDER_CURRENCY" => ["type" => "single", "paramName" => "currency", "required" => true],
        "IDN_DATE"       => ["type" => "single", "paramName" => "idnDate", "required" => true],
        "REF_URL"        => ["type" => "single", "paramName" => "refUrl"],
    ];

    /**
     * @var Serializer
     */
    protected $serializer;

    public function getSerializer(): Serializer
    {
        return $this->serializer;
    }

    public function setSerializer(Serializer $serializer): void
    {
        $this->serializer = $serializer;
    }

    public function __construct($config = [], $currency = '')
    {
        $config = $this->merchantByCurrency($config, $currency);
        $this->setup($config);
        if (isset($this->debug_idn)) {
            $this->debug = $this->debug_idn;
        }
        $this->fieldData['MERCHANT'] = $this->merchantId;
        $this->targetUrl = $this->defaultsData['BASE_URL'] . $this->defaultsData['IDN_URL'];
    }

    protected function nameData(array $data = []): array
    {
        return [
            "ORDER_REF"     => (isset($data[0])) ? $data[0] : 'N/A',
            "RESPONSE_CODE" => (isset($data[1])) ? $data[1] : 'N/A',
            "RESPONSE_MSG"  => (isset($data[2])) ? $data[2] : 'N/A',
            "IDN_DATE"      => (isset($data[3])) ? $data[3] : 'N/A',
            "ORDER_HASH"    => (isset($data[4])) ? $data[4] : 'N/A',
        ];
    }

    public function requestIdn(array $data = [])
    {
        $serializer = $this->getSerializer();
        if (count($data) == 0) {
            return $this->nameData();
        }

        $data['MERCHANT'] = $this->merchantId;
        $this->refnoext = $data['REFNOEXT'];
        unset($data['REFNOEXT']);

        $data2 = [];
        foreach ($this->hashFields as $fieldKey) {
            $data2[$fieldKey] = $data[$fieldKey];
        }

        $irnHash = $serializer->encode($data2, $this->secretKey);
        $data2['ORDER_HASH'] = $irnHash;
        $this->idnRequest = $data2;
        $this->logFunc("IDN", $this->idnRequest, $this->refnoext);

        $result = $this->startRequest($this->targetUrl, $this->idnRequest, 'POST');

        if (is_string($result)) {
            $processed = $this->processResponse($result);
            $this->logFunc("IDN", $processed, $this->refnoext);
            return $processed;
        }

        return false;
    }
}
