<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace Spryker\Zed\TaxApp\Persistence;

use Generated\Shared\Transfer\TaxAppConfigCollectionTransfer;
use Generated\Shared\Transfer\TaxAppConfigCriteriaTransfer;

interface TaxAppRepositoryInterface
{
    public function getTaxAppConfigCollection(TaxAppConfigCriteriaTransfer $taxAppConfigCriteriaTransfer): TaxAppConfigCollectionTransfer;
}
