<?php

declare(strict_types=1);

namespace Kiboko\Plugin\FastMap\Factory;

use Kiboko\Component\FastMapConfig\ObjectBuilder;
use Kiboko\Contract\Configurator;
use Kiboko\Plugin\FastMap;
use Symfony\Component\Config\Definition\ConfigurationInterface;
use Symfony\Component\Config\Definition\Exception as Symfony;
use Symfony\Component\Config\Definition\Processor;
use Symfony\Component\ExpressionLanguage\ExpressionLanguage;

final readonly class ObjectMapper implements Configurator\FactoryInterface
{
    private Processor $processor;
    private ConfigurationInterface $configuration;

    public function __construct(private ?ExpressionLanguage $interpreter, private array $additionalExpressionVariables = [])
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
        } catch (Symfony\InvalidConfigurationException|Symfony\InvalidTypeException $exception) {
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

    public function compile(array $config): Repository\TransformerMapper
    {
        $mapper = new ObjectBuilder(
            className: $config['class'],
            interpreter: $this->interpreter,
        );

        $builder = new FastMap\Builder\Transformer(
            new FastMap\Builder\ObjectMapper($mapper)
        );

        (new FastMap\Configuration\ConfigurationApplier($this->additionalExpressionVariables))($mapper->children(), $config);

        try {
            return new Repository\TransformerMapper($builder);
        } catch (Symfony\InvalidConfigurationException|Symfony\InvalidTypeException $exception) {
            throw new Configurator\InvalidConfigurationException(message: $exception->getMessage(), previous: $exception);
        }
    }
}
