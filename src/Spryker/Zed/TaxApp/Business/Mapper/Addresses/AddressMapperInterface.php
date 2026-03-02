<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace Spryker\Zed\TaxApp\Business\Mapper\Addresses;

use Generated\Shared\Transfer\AddressTransfer;
use Generated\Shared\Transfer\MerchantProfileAddressTransfer;
use Generated\Shared\Transfer\StockAddressTransfer;
use Generated\Shared\Transfer\TaxAppAddressTransfer;

interface AddressMapperInterface
{
    public function mapAddressTransferToTaxAppAddressTransfer(
        AddressTransfer $addressTransfer,
        TaxAppAddressTransfer $taxAppAddressTransfer
    ): TaxAppAddressTransfer;

    public function mapMerchantProfileAddressTransferToTaxAppAddressTransfer(
        MerchantProfileAddressTransfer $addressTransfer,
        TaxAppAddressTransfer $taxAppAddressTransfer
    ): TaxAppAddressTransfer;

    public function mapStockAddressTransferToTaxAppAddressTransfer(
        StockAddressTransfer $addressTransfer,
        TaxAppAddressTransfer $taxAppAddressTransfer
    ): TaxAppAddressTransfer;
}
