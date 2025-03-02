<?php

namespace VatValidator;

use GuzzleHttp\Client;
use GuzzleHttp\Exception\RequestException;
use GuzzleHttp\Exception\ClientException;

class VatValidatorService
{
    protected Client $client;
    protected string $baseUri;
    protected string $oauthUrl;
    protected string $clientId;
    protected string $clientSecret;
    protected string $grantType;
    protected string $scope;
    protected ?string $accessToken = null;

    public function __construct()
    {
        // Determine if we're using sandbox
        $useSandbox = config('vat_validator.use_sandbox', false);
        $config = $useSandbox ? config('vat_validator.sandbox') : config('vat_validator.live');

        // Load configuration
        $this->clientId = $config['client_id'];
        $this->clientSecret = $config['client_secret'];
        $this->grantType = $config['grant_type'];
        $this->scope = $config['scope'];

        if (empty($this->clientId) || empty($this->clientSecret)) {
            throw new \Exception("ERROR: client_id or client_secret is not set in config");
        }

        // Set API endpoints
        $this->baseUri = $config['api_base'];
        $this->oauthUrl = $config['oauth_url'];

        // Initialize Guzzle HTTP client
        $this->client = new Client([
            'base_uri' => $this->baseUri,
            'headers'  => ['Accept' => 'application/vnd.hmrc.2.0+json'],
        ]);

        // Authenticate and store access token
        $this->authenticate();
    }

    protected function authenticate(): void
    {
        try {
            $response = $this->client->post($this->oauthUrl, [
                'form_params' => [
                    'client_id'     => $this->clientId,
                    'client_secret' => $this->clientSecret,
                    'grant_type'    => $this->grantType,
                    'scope'         => $this->scope,
                ],
            ]);

            $data = json_decode($response->getBody(), true);
            $this->accessToken = $data['access_token'] ?? null;
        } catch (ClientException $e) {
            throw new \Exception("Failed to authenticate: " . $e->getResponse()->getBody());
        }
    }

    public function verifyVatNumber(string $vatNumber): array
    {
        $endpoint = "/organisations/vat/check-vat-number/lookup/{$vatNumber}";
        return $this->sendRequest($endpoint);
    }

    public function getConsultationNumber(string $businessVatNumber, string $customerVatNumber): array
    {
        $endpoint = "/organisations/vat/check-vat-number/lookup/{$customerVatNumber}/{$businessVatNumber}";
        return $this->sendRequest($endpoint);
    }

    protected function sendRequest(string $endpoint): array
    {
        try {
            $response = $this->client->get($endpoint, [
                'headers' => [
                    'Authorization' => 'Bearer ' . $this->accessToken,
                ],
            ]);
            return json_decode($response->getBody(), true);
        } catch (RequestException $e) {
            return ['error' => $e->getMessage()];
        }
    }
}
