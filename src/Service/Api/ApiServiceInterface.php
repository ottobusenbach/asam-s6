<?php

namespace AsamFieldPlugin\Service\Api;

interface ApiServiceInterface
{
    public function sendFieldOnOrderPlaced(string $orderId, string $asamCustomField): int;
}
