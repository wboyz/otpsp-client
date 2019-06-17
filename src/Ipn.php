<?php

namespace Cheppers\OtpClient;

class Ipn extends Base
{
    /**
     * @var array
     */
    public $successfulStatus = [
        "PAYMENT_AUTHORIZED",
        "COMPLETE",
        "REFUND",
        "PAYMENT_RECEIVED",
    ];

    public function __construct(array $config, string $currency = '')
    {
        $config = $this->merchantByCurrency($config, $currency);
        $this->setup($config);
        $this->commMethod = 'ipn';
    }

    public function validateReceived(): bool
    {
        if ($this->ipnPostDataCheck()) {
            return false;
        }

        if (!in_array(trim($this->postData['ORDERSTATUS']), $this->successfulStatus)) {
            return false;
        }

        $serialize = new Serializer();
        $calculatedHashString = $serialize->encode($this->flatArray($this->postData, ['HASH']), $this->secretKey);

        return $calculatedHashString === $this->postData['HASH'];
    }

    public function confirmReceived(): string
    {
        if (!$this->ipnPostDataCheck()) {
            return false;
        }

        $serverDate = @date("YmdHis");
        $hashArray = [
            $this->postData['IPN_PID'][0],
            $this->postData['IPN_PNAME'][0],
            $this->postData['IPN_DATE'],
            $serverDate,
        ];

        $serialize = new Serializer();

        $hash = $serialize->encode($hashArray, $this->secretKey);
        $string = "<EPAYMENT>" . $serverDate . "|" . $hash . "</EPAYMENT>";

        return $string;
    }

    protected function ipnPostDataCheck(): bool
    {
        return (count($this->postData) > 1 && array_key_exists('REFNOEXT', $this->postData));
    }
}
