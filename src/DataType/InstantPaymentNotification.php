<?php

declare(strict_types = 1);

namespace Cheppers\OtpspClient\DataType;

class InstantPaymentNotification extends ResponseBase implements \JsonSerializable
{

    /**
     * @var string
     */
    public $method = 'CARD';

    /**
     * @var string
     */
    public $finishDate = '';

    /**
     * @var string
     */
    public $paymentDate = '';

    /**
     * @var string
     */
    public $status = '';

    /**
     * @var string
     */
    public $receiveDate = '';

    public function jsonSerialize()
    {
        $data = [];

        foreach (array_keys(get_object_vars($this)) as $key) {
            switch ($key) {
                case 'salt':
                    $data['salt'] = $this->salt;
                    break;
                case 'orderRef':
                    $data['orderRef'] = $this->orderRef;
                    break;
                case 'method':
                    $data['method'] = $this->method;
                    break;
                case 'merchant':
                    $data['merchant'] = $this->merchant;
                    break;
                case 'finishDate':
                    $data['finishDate'] = $this->finishDate;
                    break;
                case 'paymentDate':
                    $data['paymentDate'] = $this->paymentDate;
                    break;
                case 'transactionId':
                    $data['transactionId'] = $this->transactionId;
                    break;
                case 'status':
                    $data['status'] = $this->status;
                    break;
                case 'receiveDate':
                    $data['receiveDate'] = $this->receiveDate;
                    break;
            }
        }

        return $data;
    }
}
