<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace Spryker\Client\TaxApp\Zed;

use Generated\Shared\Transfer\TaxAppValidationRequestTransfer;
use Generated\Shared\Transfer\TaxAppValidationResponseTransfer;

interface TaxAppStubInterface
{
    /**
     * @param \Generated\Shared\Transfer\TaxAppValidationRequestTransfer $taxAppValidationRequestTransfer
     *
     * @return \Generated\Shared\Transfer\TaxAppValidationResponseTransfer
     */
    public function validateTaxId(TaxAppValidationRequestTransfer $taxAppValidationRequestTransfer): TaxAppValidationResponseTransfer;
}
