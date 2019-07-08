<?php

declare(strict_types = 1);

namespace Cheppers\OtpspClient;

class UrlParser
{

    /**
     * @var string
     */
    protected $defaultScheme = 'http';

    public function getDefaultScheme(): string
    {
        return $this->defaultScheme;
    }

    /**
     * @return $this
     */
    public function setDefaultScheme(string $defaultScheme)
    {
        $this->defaultScheme = $defaultScheme;

        return $this;
    }

    /**
     * @return array|string|null
     */
    public function getUrlQueryVariable(string $url, string $key)
    {
        $query = parse_url($url, PHP_URL_QUERY);
        if (!$query) {
            return null;
        }

        $values =[];
        parse_str($query, $values);

        return array_key_exists($key, $values) ? $values[$key] : null;
    }

    public function removeQueryVariable(string $url, string $key): string
    {
        $parts = parse_url($url);
        parse_str($parts['query'] ?? '', $parts['query']);

        unset($parts['query'][$key]);

        return $this->buildUrl($parts);
    }

    public function buildUrl(array $parts): string
    {
        $parts += [
            'scheme' => '',
            'user' => '',
            'pass' => '',
            'host' => '',
            'port' => '',
            'path' => '',
            'query' => '',
            'fragment' => '',
        ];

        if (is_array($parts['query'])) {
            $parts['query'] = http_build_query($parts['query']);
        }

        $url = ($parts['scheme'] ?: $this->getDefaultScheme()) . '://';
        if (strlen($parts['user']) !== 0) {
            $url .= urlencode($parts['user']);

            if (strlen($parts['pass']) !== 0) {
                $url .= ':' . urlencode($parts['pass']);
            }

            $url .= '@';
        }


        $url .= $parts['host'];

        if ($parts['port']) {
            $url .= ':' . $parts['port'];
        }

        if ($parts['path']) {
            if (mb_substr($parts['path'], 0, 1) !== '/') {
                $url .= '/';
            }

            $url .= $parts['path'];
        }

        if ($parts['query']) {
            $url .= '?' . $parts['query'];
        }

        if ($parts['fragment']) {
            $url .= '#' . $parts['fragment'];
        }

        return $url;
    }
}
