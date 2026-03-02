<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace Spryker\Zed\TaxApp\Business\Mapper;

use Generated\Shared\Transfer\AddressTransfer;
use Generated\Shared\Transfer\CalculableObjectTransfer;
use Generated\Shared\Transfer\ExpenseTransfer;
use Generated\Shared\Transfer\ItemTransfer;
use Generated\Shared\Transfer\MerchantStockAddressTransfer;
use Generated\Shared\Transfer\OrderTransfer;
use Generated\Shared\Transfer\ShippingWarehouseTransfer;
use Generated\Shared\Transfer\TaxAppItemTransfer;
use Generated\Shared\Transfer\TaxAppSaleTransfer;
use Generated\Shared\Transfer\TaxAppShipmentTransfer;

interface TaxAppMapperInterface
{
    public function mapCalculableObjectToTaxAppSaleTransfer(
        CalculableObjectTransfer $calculableObjectTransfer,
        TaxAppSaleTransfer $taxAppSaleTransfer
    ): TaxAppSaleTransfer;

    public function mapItemTransfersToSaleItemTransfers(
        ItemTransfer $itemTransfer,
        string $priceMode,
        ?AddressTransfer $billingAddressTransfer,
        int $itemIndex
    ): TaxAppItemTransfer;

    public function mapMerchantStockAddressTransferToShippingWarehouse(
        TaxAppItemTransfer $taxAppItemTransfer,
        MerchantStockAddressTransfer $merchantStockAddressTransfer,
        ShippingWarehouseTransfer $shippingWarehouseTransfer
    ): ShippingWarehouseTransfer;

    public function mapExpenseTransferToSaleShipmentTransfer(
        ExpenseTransfer $expenseTransfer,
        string $priceMode,
        ?AddressTransfer $billingAddressTransfer
    ): TaxAppShipmentTransfer;

    public function mapOrderTransferToTaxAppSaleTransfer(OrderTransfer $orderTransfer, TaxAppSaleTransfer $taxAppSaleTransfer): TaxAppSaleTransfer;
}
