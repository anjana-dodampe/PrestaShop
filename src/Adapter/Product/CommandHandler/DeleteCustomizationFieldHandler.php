<?php
/**
 * Copyright since 2007 PrestaShop SA and Contributors
 * PrestaShop is an International Registered Trademark & Property of PrestaShop SA
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.md.
 * It is also available through the world-wide-web at this URL:
 * https://opensource.org/licenses/OSL-3.0
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@prestashop.com so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade PrestaShop to newer
 * versions in the future. If you wish to customize PrestaShop for your
 * needs please refer to https://devdocs.prestashop.com/ for more information.
 *
 * @author    PrestaShop SA and Contributors <contact@prestashop.com>
 * @copyright Since 2007 PrestaShop SA and Contributors
 * @license   https://opensource.org/licenses/OSL-3.0 Open Software License (OSL 3.0)
 */

declare(strict_types=1);

namespace PrestaShop\PrestaShop\Adapter\Product\CommandHandler;

use PrestaShop\PrestaShop\Adapter\Product\CustomizationFieldProvider;
use PrestaShop\PrestaShop\Adapter\Product\ProductProvider;
use PrestaShop\PrestaShop\Adapter\Product\ProductUpdater;
use PrestaShop\PrestaShop\Core\Domain\Product\Customization\Command\DeleteCustomizationFieldCommand;
use PrestaShop\PrestaShop\Core\Domain\Product\Customization\CommandHandler\DeleteCustomizationFieldHandlerInterface;
use PrestaShop\PrestaShop\Core\Domain\Product\Customization\CustomizationFieldDeleterInterface;
use PrestaShop\PrestaShop\Core\Domain\Product\ValueObject\ProductId;

/**
 * Handles @see DeleteCustomizationFieldCommand using legacy object model
 */
final class DeleteCustomizationFieldHandler implements DeleteCustomizationFieldHandlerInterface
{
    /**
     * @var CustomizationFieldDeleterInterface
     */
    private $customizationFieldDeleter;

    /**
     * @var CustomizationFieldProvider
     */
    private $customizationFieldProvider;

    /**
     * @var ProductProvider
     */
    private $productProvider;

    /**
     * @var ProductUpdater
     */
    private $productUpdater;

    /**
     * @param CustomizationFieldDeleterInterface $customizationFieldDeleter
     * @param CustomizationFieldProvider $customizationFieldProvider
     * @param ProductProvider $productProvider
     * @param ProductUpdater $productUpdater
     */
    public function __construct(
        CustomizationFieldDeleterInterface $customizationFieldDeleter,
        CustomizationFieldProvider $customizationFieldProvider,
        ProductProvider $productProvider,
        ProductUpdater $productUpdater
    ) {
        $this->customizationFieldDeleter = $customizationFieldDeleter;
        $this->productUpdater = $productUpdater;
        $this->productProvider = $productProvider;
        $this->customizationFieldProvider = $customizationFieldProvider;
    }

    /**
     * {@inheritdoc}
     */
    public function handle(DeleteCustomizationFieldCommand $command): void
    {
        $customizationField = $this->customizationFieldProvider->get($command->getCustomizationFieldId());
        $this->customizationFieldDeleter->delete($command->getCustomizationFieldId());

        $product = $this->productProvider->get(new ProductId($customizationField->id_product));
        $this->productUpdater->refreshProductCustomizabilityProperties($product);
    }
}
