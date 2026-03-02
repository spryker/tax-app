<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace Spryker\Zed\TaxApp\Persistence\Mapper;

use Generated\Shared\Transfer\TaxAppApiUrlsTransfer;
use Generated\Shared\Transfer\TaxAppConfigCollectionTransfer;
use Generated\Shared\Transfer\TaxAppConfigTransfer;
use Generated\Shared\Transfer\TaxIdValidationHistoryTransfer;
use Orm\Zed\TaxApp\Persistence\SpyTaxAppConfig;
use Orm\Zed\TaxApp\Persistence\SpyTaxIdValidationHistory;
use Propel\Runtime\Collection\Collection;
use Spryker\Shared\TaxApp\Dependency\Service\TaxAppToUtilEncodingServiceInterface;

class TaxAppConfigMapper
{
    /**
     * @var \Spryker\Shared\TaxApp\Dependency\Service\TaxAppToUtilEncodingServiceInterface
     */
    protected TaxAppToUtilEncodingServiceInterface $utilEncodingService;

    public function __construct(TaxAppToUtilEncodingServiceInterface $utilEncodingService)
    {
        $this->utilEncodingService = $utilEncodingService;
    }

    public function mapTaxAppConfigTransferToTaxAppConfigEntity(
        TaxAppConfigTransfer $taxAppConfigTransfer,
        SpyTaxAppConfig $taxAppConfigEntity
    ): SpyTaxAppConfig {
        $taxAppApiUrlsJson = $this->utilEncodingService->encodeJson($taxAppConfigTransfer->getApiUrlsOrFail()->toArray());
        $taxAppConfigTransfer = $taxAppConfigTransfer->toArray();
        unset($taxAppConfigTransfer['api_urls']);

        $taxAppConfigEntity = $taxAppConfigEntity->fromArray($taxAppConfigTransfer);
        $taxAppConfigEntity->setApiUrls($taxAppApiUrlsJson ?? '');

        return $taxAppConfigEntity;
    }

    public function mapTaxAppConfigEntityToTaxAppConfigTransfer(
        SpyTaxAppConfig $spyTaxAppConfigTransfer,
        TaxAppConfigTransfer $taxAppConfigTransfer
    ): TaxAppConfigTransfer {
        $taxAppApiUrlsArray = $this->utilEncodingService->decodeJson($spyTaxAppConfigTransfer->getApiUrls(), true);
        $taxAppApiUrlsTransfer = (new TaxAppApiUrlsTransfer())->fromArray((array)($taxAppApiUrlsArray ?? []), true);

        $spyTaxAppConfigTransfer = $spyTaxAppConfigTransfer->toArray();
        unset($spyTaxAppConfigTransfer['api_urls']);

        $taxAppConfigTransfer = $taxAppConfigTransfer->fromArray($spyTaxAppConfigTransfer, true);
        $taxAppConfigTransfer->setApiUrls($taxAppApiUrlsTransfer);

        return $taxAppConfigTransfer;
    }

    public function mapTaxAppConfigEntitiesToTaxAppConfigCollectionTransfer(
        Collection $taxAppConfigEntities,
        TaxAppConfigCollectionTransfer $taxAppConfigCollectionTransfer
    ): TaxAppConfigCollectionTransfer {
        foreach ($taxAppConfigEntities as $taxAppConfigEntity) {
            $taxAppConfigCollectionTransfer->addTaxAppConfig(
                $this->mapTaxAppConfigEntityToTaxAppConfigTransfer($taxAppConfigEntity, new TaxAppConfigTransfer()),
            );
        }

        return $taxAppConfigCollectionTransfer;
    }

    public function mapTaxIdValidationHistoryTransferToTaxIdValidationHistoryEntity(
        TaxIdValidationHistoryTransfer $taxIdValidationHistoryTransfer,
        SpyTaxIdValidationHistory $taxIdValidationHistoryEntity
    ): SpyTaxIdValidationHistory {
        return $taxIdValidationHistoryEntity->fromArray($taxIdValidationHistoryTransfer->toArray());
    }
}
