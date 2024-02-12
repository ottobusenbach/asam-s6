<?php

declare(strict_types=1);

namespace AsamFieldPlugin\Subscriber;

use AsamFieldPlugin\Service\AsamFieldService;
use Shopware\Core\Checkout\Cart\Event\CheckoutOrderPlacedEvent;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

class OrderSubscriber implements EventSubscriberInterface
{

    public function __construct(private AsamFieldService $fieldService)
    {
    }

    public static function getSubscribedEvents(): array
    {
        return [
            CheckoutOrderPlacedEvent::class => 'onOrderPlaced',
        ];
    }

    public function onOrderPlaced(CheckoutOrderPlacedEvent $event): int
    {
        $order = $event->getOrder();
        return $this->fieldService->orderPlaced($order);
    }
}
