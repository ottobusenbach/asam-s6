<?php declare(strict_types=1);

namespace AsamFieldPlugin;

use Shopware\Core\Framework\Context;
use Shopware\Core\Framework\DataAbstractionLayer\Search\Criteria;
use Shopware\Core\Framework\DataAbstractionLayer\Search\Filter\EqualsFilter;
use Shopware\Core\Framework\Plugin;
use Shopware\Core\Framework\Plugin\Context\InstallContext;
use Shopware\Core\Framework\Plugin\Context\UninstallContext;
use Shopware\Core\System\CustomField\CustomFieldTypes;

class AsamFieldPlugin extends Plugin
{
    public const ASAM_CUSTOM_FIELD_NAME = 'asam_custom_order_field';

    public function install(InstallContext $installContext): void
    {
        parent::install($installContext);

        $customFieldSetRepository = $this->container->get('custom_field_set.repository');
        $criteria = new Criteria();
        $criteria->addFilter(new EqualsFilter('customFields.name', self::ASAM_CUSTOM_FIELD_NAME));

        $results = $customFieldSetRepository->search($criteria, $installContext->getContext());

        if ($results->getTotal() === 0) {
            $this->addCustomField($installContext->getContext());
        }
    }

    public function uninstall(UninstallContext $uninstallContext): void
    {
        if ($uninstallContext->keepUserData()) {
            return;
        }
        $this->removeCustomField($uninstallContext->getContext());
        parent::uninstall($uninstallContext);
    }

    private function addCustomField(Context $context): void
    {
        $customFieldSetRepository = $this->container->get('custom_field_set.repository');

        $customFieldSetRepository->upsert([
            [
                'name' => 'asam_custom_order_fields',
                'config' => [
                    'label' => [
                        'en-GB' => 'Asam Custom Order Fields',
                        'de-DE' => 'Asam Benutzerdefinierte Bestellfelder'
                    ],
                ],
                'customFields' => [
                    [
                        'name' => self::ASAM_CUSTOM_FIELD_NAME,
                        'type' => CustomFieldTypes::TEXT,
                        'config' => [
                            'label' => [
                                'en-GB' => 'Asam Custom Order Field',
                                'de-DE' => 'Asam Benutzerdefiniertes Bestellfeld'
                            ],
                            'customFieldType' => 'text',
                            'customFieldPosition' => 1
                        ]
                    ]
                ],
                'relations' => [
                    [
                        'entityName' => 'order'
                    ]
                ],
            ]
        ], $context);
    }

    private function removeCustomField(Context $context): void
    {
        $customFieldSetRepository = $this->container->get('custom_field_set.repository');
        $customFieldSetRepository->delete(
            [
                [
                    'name' => self::ASAM_CUSTOM_FIELD_NAME
                ]
            ],
            $context
        );
    }
}
