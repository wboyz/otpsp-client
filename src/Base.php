<?php

declare(strict_types=1);

namespace Cheppers\OtpClient;

use Psr\Log\LoggerInterface;

class Base
{
    protected $merchantId;
    protected $secretKey;
    protected $hashCode;
    protected $hashString;
    protected $hashData = [];
    protected $runMode = 'LIVE';
    protected $commMethod;
    public $logger = true;
    public $logPath = "log";
    public $hashFields = [];
    public $deniedInputChars = ["'", "\\", "\""];
    public $defaultsData = [
        'BASE_URL' => "https://secure.simplepay.hu/payment/",
        'SANDBOX_URL' => "https://sandbox.simplepay.hu/payment/",
        'LU_URL' => "order/lu.php",
        'ALU_URL' => "order/alu.php",
        'IDN_URL' => "order/idn.php",
        'IRN_URL' => "order/irn.php",
        'IOS_URL' => "order/ios.php",
        'OC_URL' => "order/tokens/"
    ];

    public $propertyMapping = [
        'MERCHANT' => 'merchantId',
        'SECRET_KEY' => 'secretKey',
        'BASE_URL' => 'baseUrl',
        'ALU_URL' => 'aluUrl',
        'LU_URL' => 'luUrl',
        'IOS_URL' => 'iosUrl',
        'IDN_URL' => 'idnUrl',
        'IRN_URL' => 'irnUrl',
        'OC_URL' => 'ocUrl',
        'GET_DATA' => 'getData',
        'POST_DATA' => 'postData',
        'SERVER_DATA' => 'serverData',
        'PROTOCOL' => 'protocol',
        'SANDBOX' => 'sandbox',
        'CURL' => 'curl',
        'LOGGER' => 'logger',
        'LOG_PATH' => 'logPath',
    ];

    public $supportedLangCodes = [
        'CZ', 'DE', 'EN', 'ES', 'IT',
        'HR', 'HU', 'PL', 'RO', 'SK',
    ];
    
    public function merchantByCurrency(array $config, string $currency = ''): array
    {

        $config['CURRENCY'] = str_replace(' ', '', $currency);
        $variables = ['MERCHANT', 'SECRET_KEY'];

        foreach ($variables as $var) {
            if (isset($config[$currency . '_' . $var])) {
                $config[$var] = str_replace(' ', '', $config[$currency . '_' . $var]);
            } elseif (!isset($config[$currency . '_' . $var])) {
                $config[$var] = 'MISSING_' . $var;
                $this->errorMessage[] = 'Missing ' . $var;
            }
        }

        return $config;
    }

    public function setup(array $config): bool
    {
        if (isset($config['SANDBOX'])) {
            if ($config['SANDBOX']) {
                $this->defaultsData['BASE_URL'] = $this->defaultsData['SANDBOX_URL'];
                $this->runMode = 'SANDBOX';
            }
        }
        $this->processConfig($this->defaultsData);
        $this->processConfig($config);

        if ($this->commMethod == 'liveupdate' && isset($config['BACK_REF'])) {
            $this->setField("BACK_REF", $config['BACK_REF']);
        }
        if ($this->commMethod == 'liveupdate' && isset($config['TIMEOUT_URL'])) {
            $this->setField("TIMEOUT_URL", $config['TIMEOUT_URL']);
        }
        return true;
    }

    public function processConfig(array $config): void
    {
        foreach ($config as $externalKey => $value) {
            if (array_key_exists($externalKey, $this->propertyMapping)) {
                $internalKey = $this->propertyMapping[$externalKey];
                $this->$internalKey = $value;
            }
        }
    }

    public function flatArray(array $array = [], array $skip = []): array
    {
        if (count($array) === 0) {
            return [];
        }
        $return = [];
        foreach ($array as $name => $item) {
            if (!in_array($name, $skip)) {
                if (is_array($item)) {
                    foreach ($item as $subItem) {
                        $return[] = $subItem;
                    }
                } elseif (!is_array($item)) {
                    $return[] = $item;
                }
            }
        }
        return $return;
    }

    public function cleanString($string = ''): string
    {
        return str_replace($this->deniedInputChars, '', $string);
    }
}
