<?php

declare(strict_types=1);

namespace Kiboko\Plugin\FastMap;

use Kiboko\Contract\Configurator;
use Kiboko\Contract\Configurator\ConfigurationExceptionInterface;
use Kiboko\Contract\Configurator\InvalidConfigurationException;
use Symfony\Component\Config\Definition\Exception as Symfony;
use Symfony\Component\Config\Definition\Processor;
use Symfony\Component\ExpressionLanguage\ExpressionLanguage;

#[Configurator\Pipeline(
    name: 'fastmap',
    dependencies: [
        'php-etl/mapping-contracts:0.4.*',
    ],
    steps: [
        new Configurator\Pipeline\StepTransformer(null),
    ],
)]
final readonly class Service implements Configurator\PipelinePluginInterface
{
    private Processor $processor;
    private Configurator\PluginConfigurationInterface $configuration;

    public function __construct(
        private ExpressionLanguage $interpreter = new ExpressionLanguage(),
        private array $additionalExpressionVariables = []
    ) {
        $this->processor = new Processor();
        $this->configuration = new Configuration();
    }

    public function interpreter(): ExpressionLanguage
    {
        return $this->interpreter;
    }

    public function configuration(): Configurator\PluginConfigurationInterface
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
        } catch (Symfony\InvalidConfigurationException|Symfony\InvalidTypeException $exception) {
            throw new InvalidConfigurationException($exception->getMessage(), 0, $exception);
        }
    }

    public function validate(array $config): bool
    {
        try {
            $this->processor->processConfiguration($this->configuration, $config);

            return true;
        } catch (Symfony\InvalidConfigurationException|Symfony\InvalidTypeException) {
            return false;
        }
    }

    /**
     * @throws ConfigurationExceptionInterface
     */
    public function compile(array $config): Factory\Repository\TransformerMapper
    {
        $interpreter = clone $this->interpreter;

        if (\array_key_exists('expression_language', $config)
            && \is_array($config['expression_language'])
            && \count($config['expression_language'])
        ) {
            foreach ($config['expression_language'] as $provider) {
                $interpreter->registerProvider(new $provider());
            }
        }

        try {
            if (\array_key_exists('conditional', $config)) {
                $conditionalFactory = new Factory\ConditionalMapper($interpreter, $this->additionalExpressionVariables);

                return $conditionalFactory->compile($config['conditional']);
            }
            if (\array_key_exists('map', $config)) {
                $arrayFactory = new Factory\ArrayMapper($interpreter, $this->additionalExpressionVariables, isset($config['append']) && $config['append']);

                return $arrayFactory->compile($config['map']);
            }
            if (\array_key_exists('object', $config)) {
                $objectFactory = new Factory\ObjectMapper($interpreter, $this->additionalExpressionVariables);

                return $objectFactory->compile($config['object']);
            }
            throw new InvalidConfigurationException('Could not determine if the factory should build an array or an object transformer.');
        } catch (Symfony\InvalidConfigurationException|Symfony\InvalidTypeException $exception) {
            throw new InvalidConfigurationException($exception->getMessage(), 0, $exception);
        }
    }
}
