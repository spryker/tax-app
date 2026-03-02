<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace Spryker\Zed\TaxApp\Business;

use Spryker\Client\TaxApp\TaxAppClientInterface;
use Spryker\Shared\TaxApp\Dependency\Service\TaxAppToUtilEncodingServiceInterface;
use Spryker\Zed\Kernel\Business\AbstractBusinessFactory;
use Spryker\Zed\TaxApp\Business\AccessTokenProvider\AccessTokenProvider;
use Spryker\Zed\TaxApp\Business\AccessTokenProvider\AccessTokenProviderInterface;
use Spryker\Zed\TaxApp\Business\Aggregator\PriceAggregator;
use Spryker\Zed\TaxApp\Business\Aggregator\PriceAggregatorInterface;
use Spryker\Zed\TaxApp\Business\Calculator\Calculator;
use Spryker\Zed\TaxApp\Business\Calculator\CalculatorInterface;
use Spryker\Zed\TaxApp\Business\Calculator\FallbackCalculator;
use Spryker\Zed\TaxApp\Business\Calculator\FallbackCalculatorInterface;
use Spryker\Zed\TaxApp\Business\Calculator\TaxAppCalculator;
use Spryker\Zed\TaxApp\Business\Calculator\TaxAppCalculatorInterface;
use Spryker\Zed\TaxApp\Business\Config\ConfigDeleter;
use Spryker\Zed\TaxApp\Business\Config\ConfigDeleterInterface;
use Spryker\Zed\TaxApp\Business\Config\ConfigReader;
use Spryker\Zed\TaxApp\Business\Config\ConfigReaderInterface;
use Spryker\Zed\TaxApp\Business\Config\ConfigWriter;
use Spryker\Zed\TaxApp\Business\Config\ConfigWriterInterface;
use Spryker\Zed\TaxApp\Business\Mapper\Addresses\AddressMapper;
use Spryker\Zed\TaxApp\Business\Mapper\Addresses\AddressMapperInterface;
use Spryker\Zed\TaxApp\Business\Mapper\Prices\ItemExpensePriceRetriever;
use Spryker\Zed\TaxApp\Business\Mapper\Prices\ItemExpensePriceRetrieverInterface;
use Spryker\Zed\TaxApp\Business\Mapper\TaxAppMapper;
use Spryker\Zed\TaxApp\Business\Mapper\TaxAppMapperInterface;
use Spryker\Zed\TaxApp\Business\Order\RefundProcessor;
use Spryker\Zed\TaxApp\Business\Order\RefundProcessorInterface;
use Spryker\Zed\TaxApp\Business\Sender\PaymentSubmitTaxInvoiceSender;
use Spryker\Zed\TaxApp\Business\Sender\PaymentSubmitTaxInvoiceSenderInterface;
use Spryker\Zed\TaxApp\Business\Validator\TaxIdValidator;
use Spryker\Zed\TaxApp\Business\Validator\TaxIdValidatorInterface;
use Spryker\Zed\TaxApp\Business\Writer\TaxAppStoreRelationWriter;
use Spryker\Zed\TaxApp\Business\Writer\TaxAppStoreRelationWriterInterface;
use Spryker\Zed\TaxApp\Dependency\Facade\TaxAppToKernelAppFacadeInterface;
use Spryker\Zed\TaxApp\Dependency\Facade\TaxAppToMessageBrokerFacadeInterface;
use Spryker\Zed\TaxApp\Dependency\Facade\TaxAppToOauthClientFacadeInterface;
use Spryker\Zed\TaxApp\Dependency\Facade\TaxAppToSalesFacadeInterface;
use Spryker\Zed\TaxApp\Dependency\Facade\TaxAppToStoreFacadeInterface;
use Spryker\Zed\TaxApp\TaxAppDependencyProvider;

/**
 * @method \Spryker\Zed\TaxApp\Persistence\TaxAppEntityManagerInterface getEntityManager()()
 * @method \Spryker\Zed\TaxApp\Persistence\TaxAppRepositoryInterface getRepository()
 * @method \Spryker\Zed\TaxApp\TaxAppConfig getConfig()
 */
class TaxAppBusinessFactory extends AbstractBusinessFactory
{
    public function createConfigWriter(): ConfigWriterInterface
    {
        return new ConfigWriter($this->getEntityManager(), $this->getStoreFacade());
    }

    public function createConfigDeleter(): ConfigDeleterInterface
    {
        return new ConfigDeleter($this->getEntityManager(), $this->getStoreFacade());
    }

    public function createConfigReader(): ConfigReaderInterface
    {
        return new ConfigReader($this->getRepository(), $this->getStoreFacade());
    }

    public function createTaxAppStoreRelationWriter(): TaxAppStoreRelationWriterInterface
    {
        return new TaxAppStoreRelationWriter($this->getRepository(), $this->createConfigWriter());
    }

    public function getStoreFacade(): TaxAppToStoreFacadeInterface
    {
        return $this->getProvidedDependency(TaxAppDependencyProvider::FACADE_STORE);
    }

    /**
     * @return array<\Spryker\Zed\TaxAppExtension\Dependency\Plugin\CalculableObjectTaxAppExpanderPluginInterface>
     */
    public function getCalculableObjectTaxAppExpanderPlugins(): array
    {
        return $this->getProvidedDependency(TaxAppDependencyProvider::PLUGINS_CALCULABLE_OBJECT_TAX_APP_EXPANDER);
    }

    /**
     * @return array<\Spryker\Zed\TaxAppExtension\Dependency\Plugin\OrderTaxAppExpanderPluginInterface>
     */
    public function getOrderTaxAppExpanderPlugins(): array
    {
        return $this->getProvidedDependency(TaxAppDependencyProvider::PLUGINS_ORDER_TAX_APP_EXPANDER);
    }

    public function createCalculator(): CalculatorInterface
    {
        return new Calculator(
            $this->getStoreFacade(),
            $this->createConfigReader(),
            $this->createFallbackQuoteCalculator(),
            $this->createFallbackOrderCalculator(),
            $this->createTaxAppCalculator(),
        );
    }

    public function createFallbackQuoteCalculator(): FallbackCalculatorInterface
    {
        return new FallbackCalculator(
            $this->getFallbackQuoteCalculationPlugins(),
        );
    }

    public function createFallbackOrderCalculator(): FallbackCalculatorInterface
    {
        return new FallbackCalculator(
            $this->getFallbackOrderCalculationPlugins(),
        );
    }

    public function createTaxAppCalculator(): TaxAppCalculatorInterface
    {
        return new TaxAppCalculator(
            $this->createTaxAppMapper(),
            $this->getTaxAppClient(),
            $this->createAccessTokenProvider(),
            $this->getCalculableObjectTaxAppExpanderPlugins(),
            $this->createPriceAggregator(),
        );
    }

    public function createAccessTokenProvider(): AccessTokenProviderInterface
    {
        return new AccessTokenProvider(
            $this->getOauthClientFacade(),
            $this->getConfig(),
        );
    }

    public function createPaymentSubmitTaxInvoiceSender(): PaymentSubmitTaxInvoiceSenderInterface
    {
        return new PaymentSubmitTaxInvoiceSender(
            $this->getMessageBrokerFacade(),
            $this->getStoreFacade(),
            $this->getSalesFacade(),
            $this->createTaxAppMapper(),
            $this->getOrderTaxAppExpanderPlugins(),
        );
    }

    public function createRefundProcessor(): RefundProcessorInterface
    {
        return new RefundProcessor(
            $this->getTaxAppClient(),
            $this->getStoreFacade(),
            $this->getSalesFacade(),
            $this->createTaxAppMapper(),
            $this->createAccessTokenProvider(),
            $this->createConfigReader(),
            $this->getOrderTaxAppExpanderPlugins(),
        );
    }

    public function getMessageBrokerFacade(): TaxAppToMessageBrokerFacadeInterface
    {
        return $this->getProvidedDependency(TaxAppDependencyProvider::FACADE_MESSAGE_BROKER);
    }

    public function getSalesFacade(): TaxAppToSalesFacadeInterface
    {
        return $this->getProvidedDependency(TaxAppDependencyProvider::FACADE_SALES);
    }

    public function createTaxAppMapper(): TaxAppMapperInterface
    {
        return new TaxAppMapper(
            $this->createAddressMapper(),
            $this->createItemExpensePriceRetriever(),
            $this->getStoreFacade(),
            $this->getConfig(),
        );
    }

    public function createAddressMapper(): AddressMapperInterface
    {
        return new AddressMapper();
    }

    public function createItemExpensePriceRetriever(): ItemExpensePriceRetrieverInterface
    {
        return new ItemExpensePriceRetriever();
    }

    public function createTaxIdValidator(): TaxIdValidatorInterface
    {
        return new TaxIdValidator(
            $this->createConfigReader(),
            $this->createAccessTokenProvider(),
            $this->getKernelAppFacade(),
            $this->getEntityManager(),
            $this->getUtilEncodingService(),
        );
    }

    public function getTaxAppClient(): TaxAppClientInterface
    {
        return $this->getProvidedDependency(TaxAppDependencyProvider::CLIENT_TAX_APP);
    }

    public function getOauthClientFacade(): TaxAppToOauthClientFacadeInterface
    {
        return $this->getProvidedDependency(TaxAppDependencyProvider::FACADE_OAUTH_CLIENT);
    }

    public function createPriceAggregator(): PriceAggregatorInterface
    {
        return new PriceAggregator();
    }

    /**
     * @return array<\Spryker\Zed\CalculationExtension\Dependency\Plugin\CalculationPluginInterface>
     */
    public function getFallbackQuoteCalculationPlugins(): array
    {
        return $this->getProvidedDependency(TaxAppDependencyProvider::PLUGINS_FALLBACK_QUOTE_CALCULATION);
    }

    /**
     * @return array<\Spryker\Zed\CalculationExtension\Dependency\Plugin\CalculationPluginInterface>
     */
    public function getFallbackOrderCalculationPlugins(): array
    {
        return $this->getProvidedDependency(TaxAppDependencyProvider::PLUGINS_FALLBACK_ORDER_CALCULATION);
    }

    public function getKernelAppFacade(): TaxAppToKernelAppFacadeInterface
    {
        return $this->getProvidedDependency(TaxAppDependencyProvider::FACADE_KERNEL_APP);
    }

    public function getUtilEncodingService(): TaxAppToUtilEncodingServiceInterface
    {
        return $this->getProvidedDependency(TaxAppDependencyProvider::SERVICE_UTIL_ENCODING);
    }
}
