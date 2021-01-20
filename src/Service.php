<?php declare(strict_types=1);

namespace Kiboko\Plugin\FastMap;

use Kiboko\Plugin\FastMap\Factory;
use Kiboko\Contract\Configurator\InvalidConfigurationException;
use Kiboko\Contract\Configurator\ConfigurationExceptionInterface;
use Kiboko\Contract\Configurator\FactoryInterface;
use Symfony\Component\Config\Definition\ConfigurationInterface;
use Symfony\Component\Config\Definition\Exception as Symfony;
use Symfony\Component\Config\Definition\Processor;

final class Service implements FactoryInterface
{
    private Processor $processor;
    private ConfigurationInterface $configuration;

    public function __construct()
    {
        $this->processor = new Processor();
        $this->configuration = new Configuration();
    }

    public function configuration(): ConfigurationInterface
    {
        return $this->configuration;
    }

    /**
     * @throws ConfigurationExceptionInterface
     */
    public function normalize(array $config): array
    {
        try {
            return $this->processor->processConfiguration($this->configuration, $config);
        } catch (Symfony\InvalidTypeException|Symfony\InvalidConfigurationException $exception) {
            throw new InvalidConfigurationException($exception->getMessage(), 0, $exception);
        }
    }

    public function validate(array $config): bool
    {
        try {
            $this->processor->processConfiguration($this->configuration, $config);

            return true;
        } catch (Symfony\InvalidTypeException|Symfony\InvalidConfigurationException $exception) {
            return false;
        }
    }

    /**
     * @throws ConfigurationExceptionInterface
     */
    public function compile(array $config): \PhpParser\Builder
    {
        try {
            if (isset($config['map'])) {
                $arrayFactory = new Factory\ArrayMapper();

                $mapper = $arrayFactory->compile($config['map']);

//                $logger = $loggerFactory->compile($config['logger'] ?? []);
//                $mapper->withLogger($logger->getNode());

                return $mapper;
            } elseif (isset($config['object'])) {
                $objectFactory = new Factory\ObjectMapper();

                $mapper = $objectFactory->compile($config['object']);

//                $logger = $loggerFactory->compile($config['logger'] ?? []);
//                $mapper->withLogger($logger->getNode());

                return $mapper;
            } else {
                throw new InvalidConfigurationException(
                    'Could not determine if the factory should build an array or an object transformer.'
                );
            }
        } catch (Symfony\InvalidTypeException|Symfony\InvalidConfigurationException $exception) {
            throw new InvalidConfigurationException($exception->getMessage(), 0, $exception);
        }
    }
}
