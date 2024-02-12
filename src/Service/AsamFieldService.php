<?php

declare(strict_types=1);

namespace AsamFieldPlugin\Service;

use AsamFieldPlugin\AsamFieldPlugin;
use Shopware\Core\Checkout\Order\OrderEntity;

class AsamFieldService
{
    public function __construct(private \AsamFieldPlugin\Service\Api\ApiServiceInterface $apiService)
    {
    }

    public function orderPlaced(OrderEntity $order): int
    {
        $asamCustomField = $this->getAsamCustomFieldFromOrder($order);
        if (empty($asamCustomField))
        {
            $asamCustomField = 'DUMMY_TEXT';
        }

        return $this->apiService->sendFieldOnOrderPlaced($order->getId(), $asamCustomField);
    }

    private function getAsamCustomFieldFromOrder(OrderEntity $order): string
    {
        $customFields = $order->getCustomFields();
        return $customFields[AsamFieldPlugin::ASAM_CUSTOM_FIELD_NAME] ?? '';
    }
}
