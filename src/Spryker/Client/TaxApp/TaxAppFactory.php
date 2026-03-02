<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace Spryker\Client\TaxApp;

use Spryker\Client\Kernel\AbstractFactory;
use Spryker\Client\TaxApp\Api\Builder\TaxAppHeaderBuilder;
use Spryker\Client\TaxApp\Api\Builder\TaxAppHeaderBuilderInterface;
use Spryker\Client\TaxApp\Api\Sender\TaxAppRequestSender;
use Spryker\Client\TaxApp\Api\Sender\TaxAppRequestSenderInterface;
use Spryker\Client\TaxApp\Dependency\Client\TaxAppToStoreClientInterface;
use Spryker\Client\TaxApp\Dependency\Client\TaxAppToZedRequestClientInterface;
use Spryker\Client\TaxApp\Dependency\External\TaxAppToHttpClientAdapterInterface;
use Spryker\Client\TaxApp\Zed\TaxAppStub;
use Spryker\Client\TaxApp\Zed\TaxAppStubInterface;
use Spryker\Shared\TaxApp\Dependency\Service\TaxAppToUtilEncodingServiceInterface;

/**
 * @method \Spryker\Client\TaxApp\TaxAppConfig getConfig()
 */
class TaxAppFactory extends AbstractFactory
{
    public function createTaxAppHeaderBuilder(): TaxAppHeaderBuilderInterface
    {
        return new TaxAppHeaderBuilder($this->getStoreClient(), $this->getConfig());
    }

    public function createTaxAppRequestSender(): TaxAppRequestSenderInterface
    {
        return new TaxAppRequestSender(
            $this->createTaxAppHeaderBuilder(),
            $this->getHttpClient(),
            $this->getUtilEncodingService(),
            $this->getConfig()->getRequestTimeoutInSeconds(),
        );
    }

    public function createZedStub(): TaxAppStubInterface
    {
        return new TaxAppStub(
            $this->getZedRequestClient(),
        );
    }

    public function getUtilEncodingService(): TaxAppToUtilEncodingServiceInterface
    {
        return $this->getProvidedDependency(TaxAppDependencyProvider::SERVICE_UTIL_ENCODING);
    }

    public function getHttpClient(): TaxAppToHttpClientAdapterInterface
    {
        return $this->getProvidedDependency(TaxAppDependencyProvider::CLIENT_HTTP);
    }

    public function getStoreClient(): TaxAppToStoreClientInterface
    {
        return $this->getProvidedDependency(TaxAppDependencyProvider::CLIENT_STORE);
    }

    public function getZedRequestClient(): TaxAppToZedRequestClientInterface
    {
        return $this->getProvidedDependency(TaxAppDependencyProvider::CLIENT_ZED_REQUEST);
    }
}
