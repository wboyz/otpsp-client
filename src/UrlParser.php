<?php

declare(strict_types=1);

namespace Cheppers\OtpClient;

class UrlParser
{
    public function getUrlQueryVariable(string $url, string $variable): string
    {
        $queryVariables = $this->getUrlQueryVariables(parse_url($url));

        return $queryVariables[$variable];
    }

    public function getUrlQueryVariables(array $urlArray): array
    {
        $queryVariables = [];
        parse_str($urlArray['query'], $queryVariables);

        return $queryVariables;
    }

    public function removeQueryVariable(string $url, string $variable): string
    {
        $urlArray = parse_url($url);
        $queryVariables = $this->getUrlQueryVariables($urlArray);
        unset($queryVariables[$variable]);
        $urlArray['query'] = http_build_query($queryVariables);

        return $urlArray['scheme'] . '://' . $urlArray['host'] . $urlArray['path'] . '?' . $urlArray['query'];
    }
}
