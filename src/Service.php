<?php declare(strict_types=1);

namespace Kiboko\Plugin\FastMap;

use Kiboko\Contract\Configurator;
use Kiboko\Plugin\FastMap\Factory;
use Kiboko\Contract\Configurator\InvalidConfigurationException;
use Kiboko\Contract\Configurator\ConfigurationExceptionInterface;
use Symfony\Component\Config\Definition\Exception as Symfony;
use Symfony\Component\Config\Definition\Processor;
use Symfony\Component\ExpressionLanguage\ExpressionLanguage;

#[Configurator\Pipeline(
    name: "fastmap",
    dependencies: [
        'php-etl/pipeline-contracts:~0.3.0@dev',
        'php-etl/bucket-contracts:~0.1.0@dev',
        'php-etl/bucket:~0.2.0@dev',
    ],
    steps: [
        null => "transformer",
    ],
)]
final class Service implements Configurator\PipelinePluginInterface
{
    private Processor $processor;
    private Configurator\PluginConfigurationInterface $configuration;
    private ExpressionLanguage $interpreter;

    public function __construct(
        ?ExpressionLanguage $interpreter = null,
        private array $additionalExpressionVariables = []
    ) {
        $this->processor = new Processor();
        $this->configuration = new Configuration();
        $this->interpreter = $interpreter ?? new ExpressionLanguage();
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
    public function compile(array $config): Factory\Repository\TransformerMapper
    {
        if (array_key_exists('expression_language', $config)
            && is_array($config['expression_language'])
            && count($config['expression_language'])
        ) {
            foreach ($config['expression_language'] as $provider) {
                $this->interpreter->registerProvider(new $provider);
            }
        }

        try {
            if (array_key_exists('conditional', $config)) {
                $conditionalFactory = new Factory\ConditionalMapper($this->interpreter, $this->additionalExpressionVariables);

                return $conditionalFactory->compile($config['conditional']);
            } elseif (array_key_exists('map', $config)) {
                $arrayFactory = new Factory\ArrayMapper($this->interpreter, $this->additionalExpressionVariables);

                return $arrayFactory->compile($config['map']);
            } elseif (array_key_exists('object', $config)) {
                $objectFactory = new Factory\ObjectMapper($this->interpreter, $this->additionalExpressionVariables);

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
