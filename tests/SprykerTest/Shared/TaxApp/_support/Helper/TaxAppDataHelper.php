<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace SprykerTest\Shared\TaxApp\Helper;

use ArrayObject;
use Codeception\Module;
use Codeception\Stub;
use Generated\Shared\DataBuilder\AddressBuilder;
use Generated\Shared\DataBuilder\CalculableObjectBuilder;
use Generated\Shared\DataBuilder\ItemBuilder;
use Generated\Shared\DataBuilder\OrderBuilder;
use Generated\Shared\DataBuilder\TaxAppConfigBuilder;
use Generated\Shared\DataBuilder\TaxAppConfigConditionsBuilder;
use Generated\Shared\DataBuilder\TaxAppConfigCriteriaBuilder;
use Generated\Shared\DataBuilder\TaxAppSaleBuilder;
use Generated\Shared\DataBuilder\TaxCalculationRequestBuilder;
use Generated\Shared\DataBuilder\TaxCalculationResponseBuilder;
use Generated\Shared\Transfer\CalculableObjectTransfer;
use Generated\Shared\Transfer\OrderTransfer;
use Generated\Shared\Transfer\QuoteTransfer;
use Generated\Shared\Transfer\StoreTransfer;
use Generated\Shared\Transfer\TaxAppConfigConditionsTransfer;
use Generated\Shared\Transfer\TaxAppConfigCriteriaTransfer;
use Generated\Shared\Transfer\TaxAppConfigTransfer;
use Generated\Shared\Transfer\TaxAppSaleTransfer;
use Generated\Shared\Transfer\TaxCalculationRequestTransfer;
use Generated\Shared\Transfer\TaxCalculationResponseTransfer;
use Orm\Zed\TaxApp\Persistence\SpyTaxAppConfig;
use Orm\Zed\TaxApp\Persistence\SpyTaxAppConfigQuery;
use Spryker\Zed\Store\Business\StoreFacadeInterface;
use SprykerTest\Shared\Testify\Helper\DataCleanupHelperTrait;
use SprykerTest\Shared\Testify\Helper\LocatorHelperTrait;
use SprykerTest\Shared\Testify\Helper\TableRelationsCleanupHelperTrait;
use SprykerTest\Zed\Testify\Helper\Business\DependencyProviderHelperTrait;

class TaxAppDataHelper extends Module
{
    use LocatorHelperTrait;
    use DataCleanupHelperTrait;
    use DependencyProviderHelperTrait;
    use TableRelationsCleanupHelperTrait;

    /**
     * @param array $seed
     *
     * @return \Generated\Shared\Transfer\TaxAppConfigTransfer
     */
    public function createTaxAppConfigTransfer(array $seed = []): TaxAppConfigTransfer
    {
        return (new TaxAppConfigBuilder())->seed($seed)->build();
    }

    /**
     * @param array $seed
     *
     * @return \Generated\Shared\Transfer\TaxAppConfigCriteriaTransfer
     */
    public function createTaxAppConfigCriteriaTransfer(array $seed = []): TaxAppConfigCriteriaTransfer
    {
        return (new TaxAppConfigCriteriaBuilder())->seed($seed)->build();
    }

    /**
     * @param array $seed
     *
     * @return \Generated\Shared\Transfer\TaxAppConfigCriteriaTransfer
     */
    public function createTaxAppConfigCriteriaTransferWithTaxAppConfigConditionsTransfer(array $seed = []): TaxAppConfigCriteriaTransfer
    {
        $taxAppConfigCriteriaTransfer = $this->createTaxAppConfigCriteriaTransfer($seed);
        $taxAppConfigConditionsTransfer = $this->createTaxAppConfigConditionsTransfer($seed);

        return $taxAppConfigCriteriaTransfer->setTaxAppConfigConditions($taxAppConfigConditionsTransfer);
    }

    /**
     * @param array $seed
     *
     * @return \Generated\Shared\Transfer\TaxAppConfigConditionsTransfer
     */
    public function createTaxAppConfigConditionsTransfer(array $seed = []): TaxAppConfigConditionsTransfer
    {
        return (new TaxAppConfigConditionsBuilder())->seed($seed)->build();
    }

    /**
     * @param array $seed
     *
     * @return \Generated\Shared\Transfer\TaxAppConfigTransfer
     */
    public function haveTaxAppConfig(array $seed = []): TaxAppConfigTransfer
    {
        $taxAppConfigTransfer = $this->createTaxAppConfigTransfer();
        $seed = array_merge($taxAppConfigTransfer->toArray(), $seed);

        $taxAppConfigEntity = (new SpyTaxAppConfig())
            ->fromArray($seed);

        $taxAppConfigEntity->save();

        $this->getDataCleanupHelper()->_addCleanup(function () use ($taxAppConfigEntity): void {
            $taxAppConfigEntity->delete();
        });

        return $taxAppConfigTransfer->fromArray($taxAppConfigEntity->toArray(), true);
    }

    /**
     * @return void
     */
    public function ensureTaxAppConfigTableIsEmpty(): void
    {
        $this->getTableRelationsCleanupHelper()->ensureDatabaseTableIsEmpty($this->getTaxAppConfigQuery());
    }

    /**
     * @return \Orm\Zed\TaxApp\Persistence\SpyTaxAppConfigQuery
     */
    protected function getTaxAppConfigQuery(): SpyTaxAppConfigQuery
    {
        return SpyTaxAppConfigQuery::create();
    }

    /**
     * @param \Generated\Shared\Transfer\TaxAppConfigTransfer $taxAppConfigTransfer
     * @param \Orm\Zed\TaxApp\Persistence\SpyTaxAppConfig|null $taxAppConfigEntity
     *
     * @return void
     */
    public function assertTaxAppConfigStoredProperly(
        TaxAppConfigTransfer $taxAppConfigTransfer,
        ?SpyTaxAppConfig $taxAppConfigEntity = null
    ): void {
        $this->assertNotNull($taxAppConfigEntity);
        $this->assertEquals(
            $taxAppConfigTransfer->getApplicationId(),
            $taxAppConfigEntity->getApplicationId(),
        );
        $this->assertEquals(
            $taxAppConfigTransfer->getApiUrl(),
            $taxAppConfigEntity->getApiUrl(),
        );
        $this->assertEquals(
            $taxAppConfigTransfer->getVendorCode(),
            $taxAppConfigEntity->getVendorCode(),
        );
    }

    /**
     * @param int $idStore
     *
     * @return \Orm\Zed\TaxApp\Persistence\SpyTaxAppConfig|null
     */
    public function findTaxAppConfigByIdStore(int $idStore): ?SpyTaxAppConfig
    {
        return $this->getTaxAppConfigQuery()
            ->filterByFkStore($idStore)
            ->findOne();
    }

    /**
     * @return \Generated\Shared\Transfer\StoreTransfer
     */
    public function createStoreTransferWithStoreReference(): StoreTransfer
    {
        return (new StoreTransfer())
            ->setName('test_store_name')
            ->setIdStore(1)
            ->setStoreReference('test_store_reference');
    }

    /**
     * @param string $vendorCode
     *
     * @return void
     */
    public function assertTaxAppWithVendorCodeIsConfigured(string $vendorCode): void
    {
        $taxAppConfigEntity = $this->findTaxAppConfigByVendorCode($vendorCode);

        $this->assertNotNull($taxAppConfigEntity, sprintf('Expected to find a Tax App configuration for the vendor with vendor code "%s" but it was not found.', $vendorCode));
    }

    /**
     * @param string $vendorCode
     *
     * @return void
     */
    public function assertTaxAppWithVendorCodeDoesNotExist(string $vendorCode): void
    {
        $taxAppConfigEntity = $this->findTaxAppConfigByVendorCode($vendorCode);

        $this->assertNull($taxAppConfigEntity, sprintf('Expected not to find a Tax App configuration for the vendor with vendor code "%s" but it was found.', $vendorCode));
    }

    /**
     * @param string $vendorCode
     *
     * @return \Orm\Zed\TaxApp\Persistence\SpyTaxAppConfig|null
     */
    protected function findTaxAppConfigByVendorCode(string $vendorCode): ?SpyTaxAppConfig
    {
        return $this->getTaxAppConfigQuery()
            ->filterByVendorCode($vendorCode)
            ->findOne();
    }

    /**
     * @param \Generated\Shared\Transfer\StoreTransfer|null $storeTransfer
     *
     * @return void
     */
    public function configureStoreFacadeGetStoreByStoreReferenceMethod(?StoreTransfer $storeTransfer = null): void
    {
        $storeTransfer = $this->createStoreTransferWithStoreReference();

        $storeFacadeMock = Stub::makeEmpty(StoreFacadeInterface::class, [
            'getStoreByStoreReference' => $storeTransfer,
            'getCurrentStore' => $storeTransfer,
            'getAllStores' => [$storeTransfer],
            'getIsDynamicStoreEnabled' => true,
            'isDynamicStoreEnabled' => true,
        ]);
        $this->getLocatorHelper()->addToLocatorCache('store-facade', $storeFacadeMock);
    }

    /**
     * @param array<mixed> $seed
     * @param \Generated\Shared\Transfer\StoreTransfer|null $storeTransfer
     *
     * @return \Generated\Shared\Transfer\CalculableObjectTransfer
     */
    public function haveQuoteTransfer(array $seed = [], ?StoreTransfer $storeTransfer = null): CalculableObjectTransfer
    {
        $calculableObjectTransfer = (new CalculableObjectBuilder())->seed($seed)->build();

        $quoteTransfer = (new QuoteTransfer())->fromArray($calculableObjectTransfer->toArray(), true);
        $quoteTransfer->setBillingAddress((new AddressBuilder())->build());
        $storeTransfer = $storeTransfer ?? (new StoreTransfer())->setIdStore(1);

        $calculableObjectTransfer->setOriginalQuote($quoteTransfer)->setStore($storeTransfer);

        if (!isset($seed['items']) || !is_array($seed['items'])) {
            return $calculableObjectTransfer;
        }

        return $calculableObjectTransfer->setItems($this->getItems($seed['items']));
    }

    /**
     * @param array<mixed> $seed
     *
     * @return \Generated\Shared\Transfer\OrderTransfer
     */
    public function haveOrderTransfer(array $seed = []): OrderTransfer
    {
        $orderTransfer = (new OrderBuilder())
            ->seed($seed)
            ->withBillingAddress()
            ->build();

        if (!isset($seed['items']) || !is_array($seed['items'])) {
            return $orderTransfer;
        }

        return $orderTransfer->setItems($this->getItems($seed['items']));
    }

    /**
     * @param array<int, mixed> $seed
     *
     * @return \ArrayObject<\Generated\Shared\Transfer\ItemTransfer>
     */
    protected function getItems(array $seed): ArrayObject
    {
        $items = [];

        foreach ($seed as $itemSeed) {
            $items[] = (new ItemBuilder())->seed($itemSeed)->build();
        }

        return new ArrayObject($items);
    }

    /**
     * @param array $seed
     *
     * @return \Generated\Shared\Transfer\TaxAppSaleTransfer
     */
    public function haveTaxAppSaleTransfer(array $seed = []): TaxAppSaleTransfer
    {
        return (new TaxAppSaleBuilder())->seed($seed)->withItem()->withShipment()->build();
    }

    /**
     * @param array $seed
     *
     * @return \Generated\Shared\Transfer\TaxCalculationRequestTransfer
     */
    public function haveTaxCalculationRequestTransfer(array $seed = []): TaxCalculationRequestTransfer
    {
        return (new TaxCalculationRequestBuilder())->seed($seed)->withSale($this->haveTaxAppSaleTransfer()->toArray())->build();
    }

    /**
     * @param array $seed
     *
     * @return \Generated\Shared\Transfer\TaxCalculationResponseTransfer
     */
    public function haveTaxCalculationResponseTransfer(array $seed = []): TaxCalculationResponseTransfer
    {
        $saleTransfer = $this->haveTaxAppSaleTransfer();
        $saleTransfer->setTaxTotal(12345);

        return (new TaxCalculationResponseBuilder())->seed($seed)->withSale($saleTransfer->toArray())->build();
    }
}
