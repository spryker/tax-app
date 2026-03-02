<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace Spryker\Zed\TaxApp\Dependency\Facade;

use Generated\Shared\Transfer\StoreTransfer;

interface TaxAppToStoreFacadeInterface
{
    public function getStoreByStoreReference(string $storeReference): StoreTransfer;

    public function getStoreByName(string $storeName): StoreTransfer;

    /**
     * @return array<\Generated\Shared\Transfer\StoreTransfer>
     */
    public function getAllStores(): array;

    public function getCurrentStore(bool $fallbackToDefault = false): StoreTransfer;
}
