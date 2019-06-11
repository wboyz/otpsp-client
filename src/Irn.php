<?php

declare(strict_types=1);

namespace Cheppers\OtpClient;

class Irn extends Transaction
{
    /**
     * @var string
     */
    public $targetUrl = '';

    /**
     * @var string
     */
    public $commMethod = 'irn';

    /**
     * @var array
     */
    public $irnRequest = [];

    /**
     * @var array
     */
    public $hashFields = [
        "MERCHANT",
        "ORDER_REF",
        "ORDER_AMOUNT",
        "ORDER_CURRENCY",
        "IRN_DATE",
        "AMOUNT"
    ];

    /**
     * @var array
     */
    protected $validFields = [
        "MERCHANT"       => ["type" => "single", "paramName" => "merchantId", "required" => true],
        "ORDER_REF"      => ["type" => "single", "paramName" => "orderRef", "required" => true],
        "ORDER_AMOUNT"   => ["type" => "single", "paramName" => "amount", "required" => true],
        "AMOUNT"         => ["type" => "single", "paramName" => "amount", "required" => true],
        "ORDER_CURRENCY" => ["type" => "single", "paramName" => "currency", "required" => true],
        "IRN_DATE"       => ["type" => "single", "paramName" => "irnDate", "required" => true],
    ];

    public function __construct($config = [], string $currency = '')
    {
        $config = $this->merchantByCurrency($config, $currency);
        $this->setup($config);
        if (isset($this->debug_irn)) {
            $this->debug = $this->debug_irn;
        }
        $this->fieldData['MERCHANT'] = $this->merchantId;
        $this->targetUrl = $this->defaultsData['BASE_URL'] . $this->defaultsData['IRN_URL'];
    }

    protected function nameData(array $data = []): array
    {
        return [
            "ORDER_REF"     => (isset($data[0])) ? $data[0] : 'N/A',
            "RESPONSE_CODE" => (isset($data[1])) ? $data[1] : 'N/A',
            "RESPONSE_MSG"  => (isset($data[2])) ? $data[2] : 'N/A',
            "IRN_DATE"      => (isset($data[3])) ? $data[3] : 'N/A',
            "ORDER_HASH"    => (isset($data[4])) ? $data[4] : 'N/A',
        ];
    }

    public function requestIrn(array $data = [])
    {
        if (count($data) == 0) {
            return $this->nameData();
        }

        $data['MERCHANT'] = $this->merchantId;
        $this->refnoext = $data['REFNOEXT'];
        unset($data['REFNOEXT']);

        foreach ($this->hashFields as $fieldKey) {
            $data2[$fieldKey] = $data[$fieldKey];
        }
        $irnHash = $this->createHashString($data2);
        $data2['ORDER_HASH'] = $irnHash;
        $this->irnRequest = $data2;
        $this->logFunc("IRN", $this->irnRequest, $this->refnoext);

        $result = $this->startRequest($this->targetUrl, $this->irnRequest, 'POST');

        if (is_string($result)) {
            $processed = $this->processResponse($result);
            $this->logFunc("IRN", $processed, $this->refnoext);
            return $processed;
        }

        return false;
    }
}
