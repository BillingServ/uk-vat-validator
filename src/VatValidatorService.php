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
        // Load configuration from environment
        $this->clientId     = env('HMRC_CLIENT_ID');
        $this->clientSecret = env('HMRC_CLIENT_SECRET');
        $this->grantType    = env('HMRC_GRANT_TYPE', 'client_credentials');
        $this->scope        = env('HMRC_SCOPE', 'read:vat');

        if (empty($this->clientId) || empty($this->clientSecret)) {
            throw new \Exception("ERROR: HMRC_CLIENT_ID or HMRC_CLIENT_SECRET is not set in .env");
        }

        // Determine API endpoints based on sandbox flag
        $useSandbox      = env('HMRC_USE_SANDBOX', false);
        $this->baseUri   = $useSandbox
            ? 'https://test-api.service.hmrc.gov.uk'
            : 'https://api.service.hmrc.gov.uk';
        $this->oauthUrl  = $this->baseUri . '/oauth/token';

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
