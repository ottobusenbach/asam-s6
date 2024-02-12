<?php

declare(strict_types=1);

namespace AsamFieldPlugin\Service\Api;

use Symfony\Component\HttpClient\HttpClient;
use Symfony\Contracts\HttpClient\Exception\TransportExceptionInterface;

class ApiService implements ApiServiceInterface
{
    private const URL_BASE = "http://localhost/dummy-end-point.php?param1=%s&param2=%s";

    public function sendFieldOnOrderPlaced(string $orderId, string $asamCustomField): int
    {
        $generatedUrl = $this->getGeneratedUrl($orderId, $asamCustomField);

        try {
            $response = HttpClient::create()->request(
                'GET',
                $generatedUrl
            );
            $responseStatusCode = $response->getStatusCode();
        } catch (TransportExceptionInterface $e) {
            $responseStatusCode = -1;
        }

        return $responseStatusCode;
    }

    private function getGeneratedUrl(string $orderId, string $asamCustomField): string
    {
        return sprintf(self::URL_BASE, $orderId, $asamCustomField);
    }
}
