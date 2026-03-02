<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace Spryker\Zed\TaxApp\Business\Config;

use Generated\Shared\Transfer\SortTransfer;
use Generated\Shared\Transfer\TaxAppConfigConditionsTransfer;
use Generated\Shared\Transfer\TaxAppConfigCriteriaTransfer;
use Generated\Shared\Transfer\TaxAppConfigTransfer;
use Spryker\Zed\TaxApp\Dependency\Facade\TaxAppToStoreFacadeInterface;
use Spryker\Zed\TaxApp\Persistence\TaxAppRepositoryInterface;

class ConfigReader implements ConfigReaderInterface
{
    public function __construct(
        protected TaxAppRepositoryInterface $taxAppRepository,
        protected TaxAppToStoreFacadeInterface $storeFacade
    ) {
    }

    public function getTaxAppConfigByIdStore(int $idStore): ?TaxAppConfigTransfer
    {
        $taxAppConfigConditionsTransfer = new TaxAppConfigConditionsTransfer();
        $taxAppConfigConditionsTransfer->addFkStore($idStore);

        $taxAppConfigCriteriaTransfer = (new TaxAppConfigCriteriaTransfer())
            ->setTaxAppConfigConditions($taxAppConfigConditionsTransfer)
            ->addSort(
                (new SortTransfer())
                    ->setField(TaxAppConfigTransfer::IS_ACTIVE)
                    ->setIsAscending(false),
            );

        $taxAppConfigCollectionTransfer = $this->taxAppRepository->getTaxAppConfigCollection($taxAppConfigCriteriaTransfer);

        if (!$taxAppConfigCollectionTransfer->getTaxAppConfigs()->count()) {
            return null;
        }

        return $taxAppConfigCollectionTransfer->getTaxAppConfigs()->offsetGet(0);
    }

    public function findTaxAppConfigForCurrentStore(): ?TaxAppConfigTransfer
    {
        $taxAppConfigConditionsTransfer = new TaxAppConfigConditionsTransfer();
        $taxAppConfigConditionsTransfer->addFkStore((int)$this->storeFacade->getCurrentStore()->getIdStore());

        $taxAppConfigCriteriaTransfer = (new TaxAppConfigCriteriaTransfer())
            ->setTaxAppConfigConditions($taxAppConfigConditionsTransfer)
            ->addSort(
                (new SortTransfer())
                    ->setField(TaxAppConfigTransfer::IS_ACTIVE)
                    ->setIsAscending(false),
            );

        $taxAppConfigCollectionTransfer = $this->taxAppRepository->getTaxAppConfigCollection($taxAppConfigCriteriaTransfer);

        if (!$taxAppConfigCollectionTransfer->getTaxAppConfigs()->count()) {
            return null;
        }

        return $taxAppConfigCollectionTransfer->getTaxAppConfigs()->offsetGet(0);
    }
}
