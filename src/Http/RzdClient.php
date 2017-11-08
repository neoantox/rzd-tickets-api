<?php

namespace Rudnikov\RzdTicketsApi\Http;

use GuzzleHttp\Client;
use GuzzleHttp\ClientInterface;
use GuzzleHttp\Cookie\CookieJar;
use GuzzleHttp\RequestOptions;
use Psr\Http\Message\ResponseInterface;

class RzdClient
{
    /**
     * @var ClientInterface
     */
    protected $client;

    /**
     * RzdRequest constructor.
     */
    public function __construct()
    {
        $this->client = $this->makeClient();
    }

    public function makeRequest(string $method, string $uri, array $getParams = [], array $bodyParams = []): array
    {
        $uri     = $this->buildUri($uri, $getParams);
        $options = [
            RequestOptions::HEADERS     => $this->getHeaders(),
            RequestOptions::FORM_PARAMS => $bodyParams,
        ];

        $response = $this->client->request($method, $uri, $options);

        while (($rid = $this->getResponseRid($response)) !== null) {
            sleep(2);

            $options[RequestOptions::FORM_PARAMS]['rid'] = $rid;
            $response = $this->client->request($method, $uri, $options);
        }

        return \GuzzleHttp\json_decode((string) $response->getBody(), true);
    }

    protected function getResponseRid(ResponseInterface $response): ?int
    {
        $data = \GuzzleHttp\json_decode((string) $response->getBody());

        if ($data->result === 'RID') {
            return $data->RID;
        }

        return null;
    }

    protected function buildUri(string $uri, array $getParams = []): string
    {
        if (empty($getParams)) {
            return $uri;
        }

        $params = http_build_query($getParams);

        return strpos($uri, '?') === false
            ? $uri . '?' . $params
            : $uri . '&' . $params;
    }

    protected function getHeaders(): array
    {
        return [
            'X-Requested-With' => 'XMLHttpRequest',
            'Referer'          => 'http://m.rzd.ru/?layer_name=mpass_trains',
            'Accept'           => 'application/json, text/javascript, */*; q=0.01',
            'Accept-Encoding'  => 'gzip, deflate, br',
            'Accept-Language'  => 'ru,en;q=0.8,la;q=0.6,id;q=0.4',
            'Origin'           => 'https://m.rzd.ru',
            'Host'             => 'm.rzd.ru',
            'Content-Type'     => 'application/x-www-form-urlencoded; charset=UTF-8',
            'User-Agent'       => 'Mozilla/5.0 (Windows NT 10.0; WOW64) AppleWebKit/537.36 (KHTML, like Gecko) '
                                . 'Chrome/59.0.3071.125 YaBrowser/17.7.1.792 Yowser/2.5 Safari/537.36',
        ];
    }

    protected function makeClient(): ClientInterface
    {
        return new Client([
            'base_uri' => 'http://m.rzd.ru/',
            'timeout'  => 2.0,

            'cookies'  => new CookieJar(true, [
                [
                    'Name'   => 'lang',
                    'Value'  => 'ru',
                    'Domain' => 'rzd.ru'
                ],
                [
                    'Name'   => 'AuthFlag',
                    'Value'  => 'false',
                    'Domain' => 'rzd.ru'
                ],
            ]),
        ]);
    }
}