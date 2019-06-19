<?php

namespace Cheppers\OtpClient;

class BackRef extends Transaction
{
    public $commMethod = 'backref';
    public $protocol;
    protected $request;
    protected $returnVars = [
        "RC",
        "RT",
        "3dsecure",
        "date",
        "payrefno",
        "ctrl",
    ];

    public $backStatusArray = [
        'BACKREF_DATE' => 'N/A',
        'REFNOEXT' => 'N/A',
        'PAYREFNO' => 'N/A',
        'ORDER_STATUS' => 'N/A',
        'PAYMETHOD' => 'N/A',
        'RESULT' => false,
    ];

    public $successfulStatus = [
        "IN_PROGRESS",
        "PAYMENT_AUTHORIZED",
        "COMPLETE",
        "WAITING_PAYMENT",
    ];

    public $unsuccessfulStatus = [
        "CARD_NOTAUTHORIZED",
        "FRAUD",
        "TEST",
        "TIMEOUT",
    ];

    /**
     * @var Serializer
     */
    protected $serializer;

    /**
     * @var Ios
     */
    protected $ios;

    public function setupBackRef(
        array $config,
        Serializer $serializer,
        string $currency = ''
    ) {
        $config = $this->merchantByCurrency($config, $currency);
        $this->iosConfig = $config;
        $this->setup($config);
        $this->createRequestUri();
        $this->backStatusArray['BACKREF_DATE'] = (isset($this->getData['date'])) ? $this->getData['date'] : 'N/A';
        $this->backStatusArray['REFNOEXT'] = (isset($this->getData['order_ref'])) ? $this->getData['order_ref'] : 'N/A';
        $this->backStatusArray['PAYREFNO'] = (isset($this->getData['payrefno'])) ? $this->getData['payrefno'] : 'N/A';
        $this->serializer = $serializer;
    }

    public function createRequestUri(): void
    {
        if ($this->protocol == '') {
            $this->protocol = "http";
        }
        $this->request = $this->protocol . '://' . $this->serverData['HTTP_HOST'] . $this->serverData['REQUEST_URI'];
    }

    public function isBackRefSuccess(string $returnCode): bool
    {
        return $returnCode === '000' || $returnCode === '001';
    }

    public function checkCtrl(): bool
    {
        $serializer = new Serializer();

        if (isset($this->getData['ctrl'])
            && $this->getData['ctrl']
            === $serializer->decode($this->request, $this->secretKey)
        ) {
            return true;
        }

        return false;
    }

    public function checkResponse(): bool
    {
        if (!isset($this->order_ref)) {
            return false;
        }

        if (!$this->checkCtrl()) {
            return false;
        }

        $ios = new Ios($this->iosConfig, $this->getData['order_currency'], $this->order_ref);

        if (is_object($ios)) {
            $this->checkIOSStatus($ios);
        }
        if (!$this->checkRtVariable($ios)) {
            return false;
        }
        if (!$this->backStatusArray['RESULT']) {
            return false;
        }
        return true;
    }

    protected function checkIOSStatus(Ios $ios): bool
    {
        if (isset($ios->status['ORDER_STATUS']) && isset($ios->status['PAYMETHOD'])) {
            $this->backStatusArray['ORDER_STATUS'] = $ios->status['ORDER_STATUS'];
            $this->backStatusArray['PAYMETHOD'] = $ios->status['PAYMETHOD'];
            if (in_array(trim($ios->status['ORDER_STATUS']), $this->successfulStatus)) {
                $this->backStatusArray['RESULT'] = true;
            }
            if (in_array(trim($ios->status['ORDER_STATUS']), $this->unsuccessfulStatus)) {
                $this->backStatusArray['RESULT'] = false;
                return false;
            }
            return true;
        } else {
            $this->backStatusArray['ORDER_STATUS'] = 'IOS_ERROR';
            $this->backStatusArray['PAYMETHOD'] = 'N/A';
            return false;
        }
    }

    protected function checkRtVariable(Ios $ios): bool
    {
        $successCode = ['000', '001'];
        if (isset($this->getData['RT'])) {
            $returnCode = substr($this->getData['RT'], 0, 3);
            if (in_array($returnCode, $successCode)) {
                $this->backStatusArray['RESULT'] = true;
                return true;
            } elseif ($this->getData['RT'] != "" && !in_array($returnCode, $successCode)) {
                $this->backStatusArray['RESULT'] = false;
                return false;
            } elseif ($this->getData['RT'] == "") {
                if (in_array(trim($ios->status['ORDER_STATUS']), $this->successfulStatus)) {
                    $this->backStatusArray['RESULT'] = true;
                    return true;
                }
            }
            $this->backStatusArray['RESULT'] = false;
            return false;
        }

        $this->backStatusArray['RESULT'] = false;
        return false;
    }
}
