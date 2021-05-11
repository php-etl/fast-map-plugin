<?php declare(strict_types=1);

namespace Kiboko\Plugin\FastMap\Factory;

use Kiboko\Component\FastMapConfig\ArrayBuilder;
use Kiboko\Contract\Configurator\InvalidConfigurationException;
use Kiboko\Contract\Configurator\RepositoryInterface;
use Kiboko\Plugin\FastMap;
use Kiboko\Contract\Configurator;
use Symfony\Component\Config\Definition\ConfigurationInterface;
use Symfony\Component\Config\Definition\Exception as Symfony;
use Symfony\Component\Config\Definition\Processor;
use Symfony\Component\ExpressionLanguage\ExpressionLanguage;

final class ConditionalMapper implements Configurator\FactoryInterface
{
    private Processor $processor;
    private ConfigurationInterface $configuration;

    public function __construct(private ?ExpressionLanguage $interpreter, private array $additionalExpressionVariables = [])
    {
        $this->processor = new Processor();
        $this->configuration = new FastMap\Configuration\ConditionalMapper();
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
        $builder = new FastMap\Builder\ConditionalMapper(
            interpreter: $this->interpreter,
        );

        foreach ($config as $alternative) {
            try {
                if (array_key_exists('map', $alternative)) {
                    $mapper = new ArrayBuilder(
                        interpreter: $this->interpreter,
                    );

                    $mapperBuilder = new FastMap\Builder\ArrayMapper($mapper);

                    (new FastMap\Configuration\ConfigurationApplier($this->additionalExpressionVariables))($mapper->children(), $alternative['map']);

                    try {
                        $builder->withAlternative(
                            $alternative['condition'],
                            $mapperBuilder
                        );
                    } catch (Symfony\InvalidTypeException|Symfony\InvalidConfigurationException $exception) {
                        throw new Configurator\InvalidConfigurationException(
                            message: $exception->getMessage(),
                            previous: $exception
                        );
                    }
                } elseif (array_key_exists('object', $alternative)) {
                    $mapper = new ArrayBuilder(
                        interpreter: $this->interpreter,
                    );

                    $mapperBuilder = new FastMap\Builder\ArrayMapper($mapper);

                    (new FastMap\Configuration\ConfigurationApplier())($mapper->children(), $alternative['object']);

                    try {
                        $builder->withAlternative(
                            $alternative['condition'],
                            $mapperBuilder
                        );
                    } catch (Symfony\InvalidTypeException|Symfony\InvalidConfigurationException $exception) {
                        throw new Configurator\InvalidConfigurationException(
                            message: $exception->getMessage(),
                            previous: $exception
                        );
                    }
                } else {
                    throw new InvalidConfigurationException(
                        'Could not determine if the factory should build an array or an object transformer.'
                    );
                }
            } catch (Symfony\InvalidTypeException|Symfony\InvalidConfigurationException $exception) {
                throw new InvalidConfigurationException($exception->getMessage(), 0, $exception);
            }
        }

        try {
            return new Repository\TransformerMapper(
                new FastMap\Builder\Transformer($builder),
            );
        } catch (Symfony\InvalidTypeException|Symfony\InvalidConfigurationException $exception) {
            throw new Configurator\InvalidConfigurationException(
                message: $exception->getMessage(),
                previous: $exception
            );
        }
    }
}
