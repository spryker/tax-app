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
use Spryker\Shared\Kernel\Transfer\AbstractTransfer;

class AddressMapper implements AddressMapperInterface
{
    public function mapAddressTransferToTaxAppAddressTransfer(
        AddressTransfer $addressTransfer,
        TaxAppAddressTransfer $taxAppAddressTransfer
    ): TaxAppAddressTransfer {
        return $this->mapAddressAndMerchantProfileAddressTransferToTaxAppAddressTransfer($addressTransfer, $taxAppAddressTransfer);
    }

    public function mapStockAddressTransferToTaxAppAddressTransfer(
        StockAddressTransfer $addressTransfer,
        TaxAppAddressTransfer $taxAppAddressTransfer
    ): TaxAppAddressTransfer {
        $taxAppAddressTransfer = $taxAppAddressTransfer->fromArray($addressTransfer->toArray(), true);

        if (
            $addressTransfer->getCountry()
            && $addressTransfer->getCountry()->getIso2Code()
        ) {
            $taxAppAddressTransfer->setCountry($addressTransfer->getCountry()->getIso2Code());
        }

        return $taxAppAddressTransfer;
    }

    public function mapMerchantProfileAddressTransferToTaxAppAddressTransfer(
        MerchantProfileAddressTransfer $addressTransfer,
        TaxAppAddressTransfer $taxAppAddressTransfer
    ): TaxAppAddressTransfer {
        return $this->mapAddressAndMerchantProfileAddressTransferToTaxAppAddressTransfer($addressTransfer, $taxAppAddressTransfer);
    }

    protected function mapAddressAndMerchantProfileAddressTransferToTaxAppAddressTransfer(
        AbstractTransfer $addressTransfer,
        TaxAppAddressTransfer $taxAppAddressTransfer
    ): TaxAppAddressTransfer {
        $taxAppAddressTransfer = $taxAppAddressTransfer->fromArray($addressTransfer->toArray(), true);

        if ($addressTransfer->offsetExists('iso2Code')) {
            $taxAppAddressTransfer->setCountry($addressTransfer->getIso2Code());
        }

        return $taxAppAddressTransfer;
    }
}
