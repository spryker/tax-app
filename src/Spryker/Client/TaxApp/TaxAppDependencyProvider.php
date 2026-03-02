<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace Spryker\Client\TaxApp;

use GuzzleHttp\Client as GuzzleHttpClient;
use Spryker\Client\Kernel\AbstractDependencyProvider;
use Spryker\Client\Kernel\Container;
use Spryker\Client\TaxApp\Dependency\Client\TaxAppToStoreClientBridge;
use Spryker\Client\TaxApp\Dependency\Client\TaxAppToZedRequestClientBridge;
use Spryker\Client\TaxApp\Dependency\Client\TaxAppToZedRequestClientInterface;
use Spryker\Client\TaxApp\Dependency\External\TaxAppToGuzzleHttpClientAdapter;
use Spryker\Shared\TaxApp\Dependency\Service\TaxAppToUtilEncodingServiceBridge;

/**
 * @method \Spryker\Client\TaxApp\TaxAppConfig getConfig()
 */
class TaxAppDependencyProvider extends AbstractDependencyProvider
{
    /**
     * @var string
     */
    public const SERVICE_UTIL_ENCODING = 'SERVICE_UTIL_ENCODING';

    /**
     * @var string
     */
    public const CLIENT_HTTP = 'CLIENT_HTTP';

    /**
     * @var string
     */
    public const CLIENT_STORE = 'CLIENT_STORE';

    /**
     * @var string
     */
    public const CLIENT_ZED_REQUEST = 'CLIENT_ZED_REQUEST';

    public function provideServiceLayerDependencies(Container $container): Container
    {
        $container = parent::provideServiceLayerDependencies($container);
        $container = $this->addUtilEncodingService($container);
        $container = $this->addHttpClient($container);
        $container = $this->addStoreClient($container);
        $container = $this->addZedRequestClient($container);

        return $container;
    }

    protected function addUtilEncodingService(Container $container): Container
    {
        $container->set(static::SERVICE_UTIL_ENCODING, function (Container $container) {
            return new TaxAppToUtilEncodingServiceBridge(
                $container->getLocator()->utilEncoding()->service(),
            );
        });

        return $container;
    }

    protected function addHttpClient(Container $container): Container
    {
        $container->set(static::CLIENT_HTTP, function () {
            return new TaxAppToGuzzleHttpClientAdapter(
                new GuzzleHttpClient(),
            );
        });

        return $container;
    }

    protected function addStoreClient(Container $container): Container
    {
        $container->set(static::CLIENT_STORE, function (Container $container) {
            return new TaxAppToStoreClientBridge($container->getLocator()->store()->client());
        });

        return $container;
    }

    protected function addZedRequestClient(Container $container): Container
    {
        $container->set(static::CLIENT_ZED_REQUEST, function (Container $container): TaxAppToZedRequestClientInterface {
            return new TaxAppToZedRequestClientBridge(
                $container->getLocator()->zedRequest()->client(),
            );
        });

        return $container;
    }
}
