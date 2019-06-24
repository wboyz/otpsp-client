<?php

declare(strict_types=1);

namespace Cheppers\OtpClient;

use Cheppers\OtpClient\Serializer;
use Psr\Http\Message\ResponseInterface;
use Cheppers\OtpClient\Communicator;
use Cheppers\OtpClient\LiveUpdate;

class Transaction extends Base
{
    public function startRequest(string $url, array $data, string $secretKey)
    {
        $communicator = new Communicator();
        $communicator->setBaseUri($url);
        $liveUpdate = new LiveUpdate();
        $liveUpdate->formData = [];

        $form = $liveUpdate->createForm('myfname', 'mysub', 'mysubel');
        $serializer = new Serializer();

        /** @var ResponseInterface $response */
        $response = $communicator->sendPost('/path', [
            'form_params' => [
                $serializer->encode($data, $secretKey),
            ],
        ]);
        
        return $response->getStatusCode();
    }
}
