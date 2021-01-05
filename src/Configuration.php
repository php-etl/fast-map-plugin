<?php declare(strict_types=1);

namespace Kiboko\Component\ETL\Flow\FastMap;

use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\ConfigurationInterface;
use Symfony\Component\Config\Definition\NodeInterface;
use Symfony\Component\Config\Definition\PrototypedArrayNode;

final class Configuration implements ConfigurationInterface
{
//    private ConfigurationInterface $loggerConfiguration;
    private array $configurationStack = [];

//    public function __construct(?ConfigurationInterface $loggerConfiguration = null)
//    {
//        $this->loggerConfiguration = $loggerConfiguration ?? new Configuration\Logger();
//    }

    public function getConfigTreeBuilder()
    {
        $builder = new TreeBuilder('fastmap');

        $builder->getRootNode()
//            ->ignoreExtraKeys()
            ->children()
                ->scalarNode('class')->end()
                ->scalarNode('expression')->end()
                ->append($this->getMapTreeBuilder()->getRootNode())
                ->append($this->getListTreeBuilder()->getRootNode())
                ->append($this->getObjectTreeBuilder()->getRootNode())
                ->append($this->getCollectionTreeBuilder()->getRootNode())
            ->end()
            ->validate()
                ->ifTrue(function ($value) {
                    return !is_array($value);
                })
                ->thenInvalid('Your configuration should be an array.')
            ->end()
            ->validate()
                ->always(function (array $value) {
                    if (array_key_exists('object', $value) && count($value['object']) <= 0) {
                        unset($value['object']);
                    }
                    if (array_key_exists('map', $value) && count($value['map']) <= 0) {
                        unset($value['map']);
                    }
                    if (array_key_exists('list', $value) && count($value['list']) <= 0) {
                        unset($value['list']);
                    }
                    if (array_key_exists('collection', $value) && count($value['collection']) <= 0) {
                        unset($value['collection']);
                    }
                    return $value;
                })
            ->end()
            ->validate()
                ->always($this->mutuallyExclusiveFields('copy', 'expression', 'constant', 'class', 'map', 'object', 'list', 'collection'))
            ->end()
            ->validate()
                ->always($this->mutuallyExclusiveFields('expression', 'copy', 'constant', 'map'))
            ->end()
            ->validate()
                ->always($this->mutuallyExclusiveFields('constant', 'copy', 'expression', 'class', 'map', 'object', 'list', 'collection'))
            ->end()
            ->validate()
                ->always($this->mutuallyExclusiveFields('map', 'copy', 'expression', 'constant', 'class', 'object', 'list', 'collection'))
            ->end()
            ->validate()
                ->always($this->mutuallyExclusiveFields('object', 'copy', 'constant', 'map', 'list', 'collection'))
            ->end()
            ->validate()
                ->always($this->mutuallyExclusiveFields('list', 'copy', 'constant', 'class', 'map', 'object', 'collection'))
            ->end()
            ->validate()
                ->always($this->mutuallyExclusiveFields('collection', 'copy', 'constant', 'map', 'object', 'list'))
            ->end()
            ->validate()
                ->always($this->mutuallyDependentFields('object', 'class', 'expression'))
            ->end()
            ->validate()
                ->always($this->mutuallyDependentFields('collection', 'class', 'expression'))
            ->end()
            ->validate()
                ->always($this->mutuallyDependentFields('list', 'expression'))
            ->end()
        ;

        return $builder;
    }

    private function evaluateMap($children)
    {
        $node = $this->getMapNode();
        $children = $node->finalize($children);

        return $children;
    }

    private function evaluateList($children)
    {
        $node = $this->getListNode();
        $children = $node->finalize($children);

        return $children;
    }

    private function evaluateObject($children)
    {
        $node = $this->getObjectNode();
        $children = $node->finalize($children);

        return $children;
    }

    private function evaluateCollection($children)
    {
        $node = $this->getCollectionNode();
        $children = $node->finalize($children);

        return $children;
    }

    private function getMapNode(): NodeInterface
    {
        $definition = $this->getMapTreeBuilder()
            ->getRootNode();

        return $definition->getNode(true);
    }

    private function getListNode(): NodeInterface
    {
        $definition = $this->getListTreeBuilder()
            ->getRootNode();

        return $definition->getNode(true);
    }

    private function getObjectNode(): NodeInterface
    {
        $definition = $this->getObjectTreeBuilder()
            ->getRootNode();

        return $definition->getNode(true);
    }

    private function getCollectionNode(): NodeInterface
    {
        $definition = $this->getCollectionTreeBuilder()
            ->getRootNode();

        return $definition->getNode(true);
    }

    private function mutuallyExclusiveFields(string $field, string ...$exclusions): \Closure
    {
        return function (array $value) use ($field, $exclusions) {
            if (!array_key_exists($field, $value)) {
                return $value;
            }

            foreach ($exclusions as $exclusion) {
                if (array_key_exists($exclusion, $value)) {
                    throw new \InvalidArgumentException(sprintf(
                        'Your configuration should either contain the "%s" or the "%s" field, not both.',
                        $field,
                        $exclusion,
                    ));
                }
            }

            return $value;
        };
    }

    private function mutuallyDependentFields(string $field, string ...$dependencies): \Closure
    {
        return function (array $value) use ($field, $dependencies) {
            if (!array_key_exists($field, $value)) {
                return $value;
            }

            foreach ($dependencies as $dependency) {
                if (!array_key_exists($dependency, $value)) {
                    throw new \InvalidArgumentException(sprintf(
                        'Your configuration should contain the "%s" field if the "%s" field is present.',
                        $dependency,
                        $field,
                    ));
                }
            }

            return $value;
        };
    }

    private function getChildTreeBuilder(string $name): TreeBuilder
    {
        $builder = new TreeBuilder($name);

        $builder->getRootNode()
            ->arrayPrototype()
                ->validate()
                    ->always($this->mutuallyExclusiveFields('copy', 'expression', 'constant', 'class', 'map', 'object', 'list', 'collection'))
                ->end()
                ->validate()
                    ->always($this->mutuallyExclusiveFields('expression', 'copy', 'constant', 'map'))
                ->end()
                ->validate()
                    ->always($this->mutuallyExclusiveFields('constant', 'copy', 'expression', 'class', 'map', 'object', 'list', 'collection'))
                ->end()
                ->validate()
                    ->always($this->mutuallyExclusiveFields('map', 'copy', 'expression', 'constant', 'class', 'object', 'list', 'collection'))
                ->end()
                ->validate()
                    ->always($this->mutuallyExclusiveFields('object', 'copy', 'constant', 'map', 'list', 'collection'))
                ->end()
                ->validate()
                    ->always($this->mutuallyExclusiveFields('list', 'copy', 'constant', 'class', 'map', 'object', 'collection'))
                ->end()
                ->validate()
                    ->always($this->mutuallyExclusiveFields('collection', 'copy', 'constant', 'map', 'object', 'list'))
                ->end()
                ->validate()
                    ->always($this->mutuallyDependentFields('object', 'class', 'expression'))
                ->end()
                ->validate()
                    ->always($this->mutuallyDependentFields('collection', 'class', 'expression'))
                ->end()
                ->validate()
                    ->always($this->mutuallyDependentFields('list', 'expression'))
                ->end()
                ->validate()
                    ->ifTrue(function (array $value) {
                        return array_key_exists('expression', $value)
                            && array_key_exists('class', $value)
                            && !array_key_exists('object', $value)
                            && !array_key_exists('collection', $value);
                    })
                    ->thenInvalid('Your configuration should not contain both the "expression" and the "class" alone, maybe you forgot a "collection", "list" or an "object" field.')
                ->end()
                ->children()
                    ->scalarNode('field')->isRequired()->end()
                    ->scalarNode('copy')->end()
                    ->scalarNode('expression')->end()
                    ->scalarNode('constant')->end()
                    ->variableNode('map')
                        ->validate()
                            ->ifTrue(function($element) {
                                return !is_array($element);
                            })
                            ->thenInvalid('The children element must be an array.')
                        ->end()
                        ->validate()
                            ->ifArray()
                            ->then(function (array $children) {
                                return $this->evaluateMap($children);
                            })
                        ->end()
                    ->end()
                    ->variableNode('list')
                        ->validate()
                            ->ifTrue(function($element) {
                                return !is_array($element);
                            })
                            ->thenInvalid('The children element must be an array.')
                        ->end()
                        ->validate()
                            ->ifArray()
                            ->then(function (array $children) {
                                return $this->evaluateList($children);
                            })
                        ->end()
                    ->end()
                    ->scalarNode('class')->end()
                    ->variableNode('object')
                        ->validate()
                            ->ifTrue(function($element) {
                                return !is_array($element);
                            })
                            ->thenInvalid('The children element must be an array.')
                        ->end()
                        ->validate()
                            ->ifArray()
                            ->then(function (array $children) {
                                return $this->evaluateObject($children);
                            })
                        ->end()
                    ->end()
                    ->variableNode('collection')
                        ->validate()
                            ->ifTrue(function($element) {
                                return !is_array($element);
                            })
                            ->thenInvalid('The children element must be an array.')
                        ->end()
                        ->validate()
                            ->ifArray()
                            ->then(function (array $children) {
                                return $this->evaluateCollection($children);
                            })
                        ->end()
                    ->end()
                ->end()
            ->end()
        ;

        return $builder;
    }

    public function getMapTreeBuilder(): TreeBuilder
    {
        return $this->getChildTreeBuilder('map');
    }

    public function getListTreeBuilder(): TreeBuilder
    {
        return $this->getChildTreeBuilder('list');
    }

    public function getCollectionTreeBuilder(): TreeBuilder
    {
        return $this->getChildTreeBuilder('collection');
    }

    public function getObjectTreeBuilder(): TreeBuilder
    {
        return $this->getChildTreeBuilder('object');
    }
}
