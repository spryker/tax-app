<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace Spryker\Zed\TaxApp\Persistence;

use Generated\Shared\Transfer\TaxAppConfigCriteriaTransfer;
use Generated\Shared\Transfer\TaxAppConfigTransfer;
use Generated\Shared\Transfer\TaxIdValidationHistoryTransfer;

interface TaxAppEntityManagerInterface
{
    public function saveTaxIdValidationHistory(TaxIdValidationHistoryTransfer $taxIdValidationHistoryTransfer): void;

    /**
     * @param \Generated\Shared\Transfer\TaxAppConfigTransfer $taxAppConfigTransfer
     * @param array<\Generated\Shared\Transfer\StoreTransfer> $storeTransfers
     *
     * @return void
     */
    public function saveTaxAppConfig(TaxAppConfigTransfer $taxAppConfigTransfer, array $storeTransfers): void;

    public function deleteTaxAppConfig(TaxAppConfigCriteriaTransfer $taxAppConfigCriteriaTransfer): void;
}
