<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace Spryker\Zed\TaxApp\Business\Mapper\Prices;

use Generated\Shared\Transfer\ExpenseTransfer;
use Generated\Shared\Transfer\ItemTransfer;

interface ItemExpensePriceRetrieverInterface
{
    public function getUnitPrice(ExpenseTransfer|ItemTransfer $transfer, string $priceMode): int;

    public function getUnitPriceWithoutDiscount(ExpenseTransfer|ItemTransfer $transfer, string $priceMode): int;

    public function getSumPrice(ExpenseTransfer|ItemTransfer $transfer, string $priceMode): int;

    public function getSumPriceWithoutDiscount(ExpenseTransfer|ItemTransfer $transfer, string $priceMode): int;
}
