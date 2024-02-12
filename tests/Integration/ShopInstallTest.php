<?php

declare(strict_types=1);

namespace AsamFieldPlugin\Tests\Integration;

use AsamFieldPlugin\AsamFieldPlugin;
use AsamFieldPlugin\Service\Api\ApiService;
use AsamFieldPlugin\Service\AsamFieldService;
use AsamFieldPlugin\Subscriber\OrderSubscriber;
use PHPUnit\Framework\TestCase;
use Shopware\Core\Framework\DataAbstractionLayer\Search\Filter\EqualsFilter;
use Shopware\Core\Framework\Test\TestCaseBase\KernelTestBehaviour;
use Shopware\Core\Checkout\Order\OrderEntity;
use Shopware\Core\Framework\Context;
use Shopware\Core\Framework\DataAbstractionLayer\EntityRepository;
use Shopware\Core\Framework\DataAbstractionLayer\Search\Criteria;
use Shopware\Core\Checkout\Cart\Event\CheckoutOrderPlacedEvent;
use Shopware\Core\Test\TestDefaults;

/**
 * @group asam_sw6
 */
class ShopInstallTest extends TestCase
{
    use KernelTestBehaviour;

    protected function setUp(): void
    {
        parent::setUp();
        $this->context = Context::createDefaultContext();
    }

    public function testSendCurlOnOrderPlacedEvent()
    {
        $order = new OrderEntity();
        $order->setId('dummy_string');
        $event = new CheckoutOrderPlacedEvent(
            $this->context,
            $order,
            TestDefaults::SALES_CHANNEL
        );

        $apiMock = $this->getMockBuilder(ApiService::class)
            ->onlyMethods(['sendFieldOnOrderPlaced'])
            ->getMock();

        $apiMock->method('sendFieldOnOrderPlaced')
            ->willReturn(200);

        $subscriber = new OrderSubscriber(new AsamFieldService($apiMock));

        $result = $subscriber->onOrderPlaced($event);

        $this->assertEquals(200, $result);
    }

    public function testCustomFieldIsInstalled()
    {
        /** @var EntityRepository $customFields */
        $customFields = $this->getContainer()->get('custom_field.repository');

        $criteria = new Criteria();
        $criteria->addFilter(new EqualsFilter('name', AsamFieldPlugin::ASAM_CUSTOM_FIELD_NAME));
        $installedCustomFields = $customFields->search($criteria, $this->context)->getEntities()->count();

        $this->assertEquals(1, $installedCustomFields);
    }
}
