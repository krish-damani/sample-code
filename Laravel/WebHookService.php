<?php

namespace App\Services;

use App\Exceptions\Documents\WebHookFailedException;
use App\Models\Document;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\ClientException;
use GuzzleHttp\Exception\GuzzleException;
use GuzzleHttp\Psr7\Request;

class WebHookService
{
    /**
     * @var Client $client
     */
    private $client;

    /**
     * WebHookService constructor.
     */
    public function __construct()
    {
        $this->client = new Client();
    }

    /**
     * Prepare data and notify to web hook.
     *
     * @param  Document $document
     * @return bool
     * @throws WebHookFailedException
     */
    public function prepareDataAndCallWebHook(Document $document): bool
    {
        return $this->notifyToWebHook($document->webhook_url, ['id' => $document->id]);
    }

    /**
     * Call web hook URL with the required params.
     *
     * @param string $webHookUrl
     * @param array  $data
     * @param array  $headers
     * @param string $requestType
     *
     * @return bool
     *
     * @throws WebHookFailedException
     */
    public function notifyToWebHook(string $webHookUrl, array $data, array $headers = [], string $requestType = 'PATCH'): bool
    {
        $request = new Request($requestType, $webHookUrl, $headers, json_encode($data));

        try {
            return $this->client->send($request);
        } catch (ClientException | GuzzleException $exception) {
            throw new WebHookFailedException($exception->getMessage());
        }
    }
}
