<?php declare(strict_types=1);

namespace Kiboko\Plugin\FastMap\Factory;

use Kiboko\Component\FastMapConfig\ObjectBuilder;
use Kiboko\Contract\Configurator\RepositoryInterface;
use Kiboko\Plugin\FastMap;
use Kiboko\Contract\Configurator;
use Symfony\Component\Config\Definition\ConfigurationInterface;
use Symfony\Component\Config\Definition\Exception as Symfony;
use Symfony\Component\Config\Definition\Processor;
use Symfony\Component\ExpressionLanguage\ExpressionLanguage;

final class ObjectMapper implements Configurator\FactoryInterface
{
    private Processor $processor;
    private ConfigurationInterface $configuration;

    public function __construct()
    {
        $this->processor = new Processor();
        $this->configuration = new FastMap\Configuration\ObjectMapper();
    }

    public function configuration(): ConfigurationInterface
    {
        return $this->configuration;
    }

    /**
     * @throws Configurator\ConfigurationExceptionInterface
     */
    public function normalize(array $config): array
    {
        try {
            return $this->processor->processConfiguration($this->configuration, $config);
        } catch (Symfony\InvalidTypeException|Symfony\InvalidConfigurationException $exception) {
            throw new Configurator\InvalidConfigurationException($exception->getMessage(), 0, $exception);
        }
    }

    public function validate(array $config): bool
    {
        try {
            if ($this->normalize($config)) {
                return true;
            }
        } catch (\Exception) {
        }

        return false;
    }

    public function compile(array $config): RepositoryInterface
    {
        if (array_key_exists('expression_language', $config)
            && is_array($config['expression_language'])
            && count($config['expression_language'])
        ) {
            $interpreter = new ExpressionLanguage();
            foreach ($config['expression_language'] as $provider) {
                $interpreter->registerProvider(new $provider);
            }
        }

        $mapper = new ObjectBuilder(
            className: $config['class'],
            interpreter: $interpreter ?? null
        );

        $builder = new FastMap\Builder\ObjectMapper($mapper);

        (new FastMap\Configuration\ConfigurationApplier())($mapper->children(), $config);

        try {
            return new Repository\ObjectMapper($builder);
        } catch (Symfony\InvalidTypeException|Symfony\InvalidConfigurationException $exception) {
            throw new Configurator\InvalidConfigurationException(
                message: $exception->getMessage(),
                previous: $exception
            );
        }
    }
}
