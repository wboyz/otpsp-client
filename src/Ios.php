<?php

namespace Cheppers\OtpClient;

class Ios extends Transaction
{
    protected $orderNumber;
    protected $merchantId;
    protected $orderStatus;
    protected $maxRun = 10;
    protected $iosOrderUrl = '';
    public $commMethod = 'ios';
    public $status = [];

    public function __construct(array $config, string $currency = '', string $orderNumber = 'N/A')
    {
        $config = $this->merchantByCurrency($config, $currency);
        $this->setup($config);
        $this->orderNumber = $orderNumber;
        $this->iosOrderUrl = $this->defaultsData['BASE_URL'] . $this->defaultsData['IOS_URL'];
        $this->runIos();
    }

    public function runIos(): void
    {
        $serializer = new Serializer();
        if ($this->merchantId === '' || $this->orderNumber === 'N/A') {
            return;
        }
        $iosArray = [
            'MERCHANT' => $this->merchantId,
            'REFNOEXT' => $this->orderNumber,
            'HASH' => $serializer->encode([$this->merchantId, $this->orderNumber], $this->secretKey),
        ];
        $iosCounter = 0;
        while ($iosCounter < $this->maxRun) {
            $result = $this->startRequest($this->iosOrderUrl, $iosArray, $this->secretKey);
            if (!$result) {
                $result = '<?xml version="1.0"?>
                <Order>
                    <ORDER_DATE>' . @date("Y-m-d H:i:s", time()) . '</ORDER_DATE>
                    <REFNO>N/A</REFNO>
                    <REFNOEXT>N/A</REFNOEXT>
                    <ORDER_STATUS>EMPTY RESULT</ORDER_STATUS>
                    <PAYMETHOD>N/A</PAYMETHOD>
                    <HASH>N/A</HASH>
                </Order>';
            }

            $resultArray = (array) simplexml_load_string($result);
            foreach ($resultArray as $itemName => $itemValue) {
                $this->status[$itemName] = $itemValue;
            }

            $valid = false;
            if ($serializer->encode(
                $this->flatArray($this->status, ["HASH"]),
                $this->secretKey
            ) === @$this->status['HASH']
            ) {
                $valid = true;
            }
            if (!$valid) {
                $iosCounter += $this->maxRun+10;
            }

            //state
            switch ($this->status['ORDER_STATUS']) {
                case 'NOT_FOUND':
                    $iosCounter++;
                    sleep(1);
                    break;
                case 'CARD_NOTAUTHORIZED':
                    $iosCounter += 5;
                    sleep(1);
                    break;
                default:
                    $iosCounter += $this->maxRun;
            }
        }
    }
}
