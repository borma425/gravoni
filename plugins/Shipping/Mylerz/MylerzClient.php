<?php

namespace Plugins\Shipping\Mylerz;

use Illuminate\Support\Facades\Log;

/**
 * Mylerz API Client - uses cURL to match the reference implementation exactly.
 * Based on create-mylerz-order.php
 */
class MylerzClient
{
    protected string $baseUrl;
    protected ?string $token = null;
    protected string $username;
    protected string $password;
    protected string $country;

    protected const API_URLS = [
        'EG' => 'https://integration.mylerz.net',
        'TN' => 'https://integration.tunisia.mylerz.net',
        'MA' => 'https://integration.morocco.mylerz.net',
        'DZ' => 'https://integration.algeria.mylerz.net',
    ];

    public function __construct()
    {
        $this->username = config('plugins.mylerz.username', '');
        $this->password = config('plugins.mylerz.password', '');
        $this->country = config('plugins.mylerz.country', 'EG');
        $this->baseUrl = self::API_URLS[$this->country] ?? self::API_URLS['EG'];
    }

    protected function makeRequest(string $url, string $method = 'GET', ?array $data = null, array $headers = []): array
    {
        $ch = curl_init();

        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_TIMEOUT, 45);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, true);
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, $method);

        if ($method === 'POST' && $data !== null) {
            $contentType = $headers['Content-Type'] ?? 'application/json';
            if (str_contains($contentType, 'application/x-www-form-urlencoded')) {
                    curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($data));
                } else {
                curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
            }
        }

        $headerArray = ['Accept: application/json'];
        foreach ($headers as $key => $value) {
            $headerArray[] = "$key: $value";
        }
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headerArray);

        $response = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        $error = curl_error($ch);
        curl_close($ch);

        if ($error) {
            return ['error' => $error, 'http_code' => $httpCode, 'raw' => $response];
        }

        return [
            'response' => json_decode($response, true),
            'raw' => $response,
            'http_code' => $httpCode,
        ];
    }

    public function authenticate(): ?string
    {
        $url = $this->baseUrl . '/token';
        $data = [
            'username' => $this->username,
            'password' => $this->password,
            'grant_type' => 'password',
        ];

        $result = $this->makeRequest($url, 'POST', $data, [
            'Content-Type' => 'application/x-www-form-urlencoded',
        ]);

        if (isset($result['error'])) {
            Log::error('Mylerz auth cURL error', ['error' => $result['error'], 'http_code' => $result['http_code'] ?? null]);
            return null;
        }

        if (isset($result['response']['access_token'])) {
            $this->token = $result['response']['access_token'];
            return $this->token;
        }

        Log::error('Mylerz auth failed', [
            'http_code' => $result['http_code'],
            'response' => $result['response'],
            'raw' => $result['raw'] ?? '',
        ]);
        return null;
    }

    public function ensureAuthenticated(): bool
    {
        return $this->token ? true : $this->authenticate() !== null;
    }

    public function addOrders(array $orders): array
    {
        if (!$this->ensureAuthenticated()) {
            return ['success' => false, 'error' => 'Authentication failed'];
        }

        Log::info('Mylerz addOrders payload', ['orders' => $orders]);

        $url = $this->baseUrl . '/api/orders/addorders';
        $result = $this->makeRequest($url, 'POST', $orders, [
            'Content-Type' => 'application/json',
            'Authorization' => 'Bearer ' . $this->token,
        ]);

        if (isset($result['error'])) {
            Log::error('Mylerz addorders cURL error', ['error' => $result['error']]);
            return ['success' => false, 'error' => $result['error']];
        }

        $data = $result['response'];

        Log::info('Mylerz addOrders response', ['http_code' => $result['http_code'], 'response' => $data]);

        if (isset($data['IsErrorState']) && $data['IsErrorState'] === true) {
            $err = $data['Value']['ErrorMessage'] ?? $data['ErrorDescription'] ?? 'Unknown error';
            $err = trim($err, " \t\n\r\0\x0B,");
            Log::error('Mylerz addorders API error', ['response' => $data, 'raw' => $result['raw'] ?? '']);
            return ['success' => false, 'error' => $err];
        }

        if (isset($data['Value']['Packages']) && is_array($data['Value']['Packages']) && count($data['Value']['Packages']) > 0) {
            return ['success' => true, 'packages' => $data['Value']['Packages']];
        }

        Log::error('Mylerz addorders unexpected response', ['response' => $data, 'raw' => $result['raw'] ?? '', 'http_code' => $result['http_code']]);
        return ['success' => false, 'error' => 'Unexpected response from Mylerz'];
    }

    public function createPickup(array $barcodes): array
    {
        if (!$this->ensureAuthenticated()) {
            return ['success' => false, 'error' => 'Authentication failed'];
        }

        $payload = array_map(fn ($b) => ['Barcode' => $b], $barcodes);
        $url = $this->baseUrl . '/api/packages/CreateMultiplePickup';
        $result = $this->makeRequest($url, 'POST', $payload, [
            'Content-Type' => 'application/json',
            'Authorization' => 'Bearer ' . $this->token,
        ]);

        if (isset($result['error'])) {
            Log::error('Mylerz createPickup cURL error', ['error' => $result['error']]);
            return ['success' => false, 'error' => $result['error']];
        }

        $data = $result['response'];

        if (isset($data['IsErrorState']) && $data['IsErrorState'] === true) {
            $err = $data['ErrorDescription'] ?? 'Unknown error';
            Log::error('Mylerz createPickup API error', ['response' => $data, 'raw' => $result['raw'] ?? '']);
            return ['success' => false, 'error' => $err];
        }

        if (isset($data['Value'])) {
            return ['success' => true, 'pickups' => $data['Value']];
        }

        Log::error('Mylerz createPickup unexpected response', ['response' => $data, 'raw' => $result['raw'] ?? '']);
        return ['success' => false, 'error' => 'Unexpected response'];
    }

    public function cancelPackage(string $barcode): array
    {
        if (!$this->ensureAuthenticated()) {
            return ['success' => false, 'error' => 'Authentication failed'];
        }

        $url = $this->baseUrl . '/api/packages/CancelPackage';
        $result = $this->makeRequest($url, 'POST', [['Barcode' => $barcode]], [
            'Content-Type' => 'application/json',
            'Authorization' => 'Bearer ' . $this->token,
        ]);

        if (isset($result['error'])) {
            return ['success' => false, 'error' => $result['error']];
        }

        $data = $result['response'];

        if (isset($data['IsErrorState']) && $data['IsErrorState'] === true) {
            return ['success' => false, 'error' => $data['ErrorDescription'] ?? 'Unknown error'];
        }

        return ['success' => true];
    }

    public function getPackageStatus(string $barcode): ?array
    {
        if (!$this->ensureAuthenticated()) {
            return null;
        }

        $url = $this->baseUrl . '/api/packages/GetPackageListStatus';
        $result = $this->makeRequest($url, 'POST', [$barcode], [
            'Content-Type' => 'application/json',
            'Authorization' => 'Bearer ' . $this->token,
        ]);

        if (isset($result['error']) || !isset($result['response']['Value']) || !is_array($result['response']['Value'])) {
            return null;
        }

        $arr = $result['response']['Value'];
        return count($arr) > 0 ? $arr[0] : null;
    }

    public function getWarehouses(): array
    {
        if (!$this->ensureAuthenticated()) {
            return [];
        }

        $url = $this->baseUrl . '/api/orders/GetWarehouses';
        $result = $this->makeRequest($url, 'GET', null, [
            'Content-Type' => 'application/json',
            'Authorization' => 'Bearer ' . $this->token,
        ]);

        if (isset($result['error']) || !isset($result['response']['Value'])) {
            return [];
        }

        return array_map(fn ($w) => $w['Name'] ?? '', $result['response']['Value']);
    }
}
