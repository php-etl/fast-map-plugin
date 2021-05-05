<?php declare(strict_types=1);

namespace Kiboko\Plugin\FastMap;

use Kiboko\Contract\Configurator\RepositoryInterface;
use Kiboko\Plugin\FastMap\Factory;
use Kiboko\Contract\Configurator\InvalidConfigurationException;
use Kiboko\Contract\Configurator\ConfigurationExceptionInterface;
use Kiboko\Contract\Configurator\FactoryInterface;
use Symfony\Component\Config\Definition\ConfigurationInterface;
use Symfony\Component\Config\Definition\Exception as Symfony;
use Symfony\Component\Config\Definition\Processor;
use Symfony\Component\ExpressionLanguage\ExpressionLanguage;

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
    public function compile(array $config): RepositoryInterface
    {
        $interpreter = new ExpressionLanguage();
        if (array_key_exists('expression_language', $config)
            && is_array($config['expression_language'])
            && count($config['expression_language'])
        ) {
            foreach ($config['expression_language'] as $provider) {
                $interpreter->registerProvider(new $provider);
            }
        }

        try {
            if (array_key_exists('conditional', $config)) {
                $conditionalFactory = new Factory\ConditionalMapper($interpreter);

                return $conditionalFactory->compile($config['conditional']);
            } elseif (array_key_exists('map', $config)) {
                $arrayFactory = new Factory\ArrayMapper($interpreter);

                return $arrayFactory->compile($config['map']);
            } elseif (array_key_exists('object', $config)) {
                $objectFactory = new Factory\ObjectMapper($interpreter);

                return $objectFactory->compile($config['object']);
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
