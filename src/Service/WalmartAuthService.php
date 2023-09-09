<?php

namespace App\Service;

use GuzzleHttp\Client;

class WalmartAuthService
{
    private $clientId;
    private $clientSecret;
    private $wmSvcName;

    public function __construct(string $clientId, string $clientSecret, string $wmSvcName)
    {
        $this->clientId = $clientId;
        $this->clientSecret = $clientSecret;
        $this->wmSvcName = $wmSvcName;
    }

    public function getAccessToken()
    {
        $tokenEndpoint = 'https://marketplace.walmartapis.com/v3/token';
        
        $wmQosCorrelationId = sprintf(
            '%04x%04x-%04x-%04x-%04x-%04x%04x%04x',
            mt_rand(0, 0xffff), mt_rand(0, 0xffff),
            mt_rand(0, 0xffff),
            mt_rand(0, 0x0fff) | 0x4000,
            mt_rand(0, 0x3fff) | 0x8000,
            mt_rand(0, 0xffff), mt_rand(0, 0xffff), mt_rand(0, 0xffff)
        );

        $client = new Client();

        $accessToken = null;

        try {
            $response = $client->post($tokenEndpoint, [
                'headers' => [
                    'Authorization' => 'Basic ' . base64_encode($this->clientId . ':' . $this->clientSecret),
                    'Content-Type' => 'application/x-www-form-urlencoded',
                    'WM_SVC.NAME' => $this->wmSvcName,
                    'WM_QOS.CORRELATION_ID' => $wmQosCorrelationId,
                    'Accept' => 'application/json',
                ],
                'form_params' => [
                    'grant_type' => 'client_credentials',
                ],
            ]);

            $statusCode = $response->getStatusCode();
            $body = (string) $response->getBody();

            if ($statusCode !== 200) {
                return [
                    'status' => 'error',
                    'message' => 'Received HTTP status code ' . $statusCode
                ];
            }

            $data = json_decode($body, true);

            if (json_last_error() !== JSON_ERROR_NONE) {
                throw new \Exception("JSON decode error: " . json_last_error_msg());
            }

            if (isset($data['access_token'])) {
                $accessToken = $data['access_token'];
            } else {
                return [
                    'status' => 'error',
                    'message' => 'Access token not found in response.'
                ];
            }

        } catch (\Exception $e) {
            return [
                'status' => 'error',
                'message' => 'Error: ' . $e->getMessage()
            ];
        }

        return [
            'status' => 'success',
            'access_token' => $accessToken
        ];
    }
}
