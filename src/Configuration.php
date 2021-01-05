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
                ->append($this->mapNode()->getRootNode())
                ->append($this->listNode()->getRootNode())
                ->append($this->objectNode()->getRootNode())
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
                ->ifTrue(function ($value) {
                    return is_array($value) && array_key_exists('map', $value) && array_key_exists('list', $value);
                })
                ->thenInvalid('Your configuration should either contain the "map" or the "list" key, not both.')
            ->end()
            ->validate()
                ->ifTrue(function ($value) {
                    return is_array($value) && array_key_exists('map', $value) && array_key_exists('object', $value);
                })
                ->thenInvalid('Your configuration should either contain the "map" or the "object" key, not both.')
            ->end()
            ->validate()
                ->ifTrue(function ($value) {
                    return is_array($value) && array_key_exists('list', $value) && array_key_exists('object', $value);
                })
                ->thenInvalid('Your configuration should either contain the "list" or the "object" key, not both.')
            ->end()
            ->validate()
                ->ifTrue(function (array $value) {
                    return array_key_exists('object', $value) && !array_key_exists('class', $value);
                })
                ->thenInvalid('Your configuration should contain the "class" field if the "object" field is present.')
            ->end()
            ->validate()
                ->ifTrue(function (array $value) {
                    return array_key_exists('object', $value) && !array_key_exists('expression', $value);
                })
                ->thenInvalid('Your configuration should contain the "expression" field if the "object" field is present.')
            ->end()
            ->validate()
                ->ifTrue(function (array $value) {
                    return array_key_exists('collection', $value) && !array_key_exists('class', $value);
                })
                ->thenInvalid('Your configuration should contain the "class" field if the "collection" field is present.')
            ->end()
            ->validate()
                ->ifTrue(function (array $value) {
                    return array_key_exists('collection', $value) && !array_key_exists('expression', $value);
                })
                ->thenInvalid('Your configuration should contain the "expression" field if the "collection" field is present.')
            ->end()
            ->validate()
                ->ifTrue(function (array $value) {
                    return array_key_exists('list', $value) && !array_key_exists('expression', $value);
                })
                ->thenInvalid('Your configuration should contain the "expression" field if the "list" field is present.')
            ->end()
        ;

        return $builder;
    }

    private function evaluateMap($children)
    {
        if (count($this->configurationStack) > 0) {
            $parent = array_pop($this->configurationStack);
            array_push($this->configurationStack, $parent);
        }

        $node = $this->getMapNode($parent ?? null);

        array_push($this->configurationStack, $node);
        $children = $node->finalize($children);
        array_pop($this->configurationStack);

        return $children;
    }

    private function evaluateList($children)
    {
        if (count($this->configurationStack) > 0) {
            $parent = array_pop($this->configurationStack);
            array_push($this->configurationStack, $parent);
        }

        $node = $this->getListNode($parent ?? null);

        array_push($this->configurationStack, $node);
        $children = $node->finalize($children);
        array_pop($this->configurationStack);

        return $children;
    }

    private function evaluateObject($children)
    {
        if (count($this->configurationStack) > 0) {
            $parent = array_pop($this->configurationStack);
            array_push($this->configurationStack, $parent);
        }

        $node = $this->getObjectNode($parent ?? null);

        array_push($this->configurationStack, $node);
        $children = $node->finalize($children);
        array_pop($this->configurationStack);

        return $children;
    }

    private function getMapNode(?PrototypedArrayNode $parent): NodeInterface
    {
        $definition = $this->mapNode()
            ->getRootNode();

        if ($parent !== null) {
            $definition->setParent(new Configuration\PlaceholderNode());
        }

        return $definition->getNode(true);
    }

    private function getListNode(?PrototypedArrayNode $parent): NodeInterface
    {
        $definition = $this->listNode()
            ->getRootNode();

        if ($parent !== null) {
            $definition->setParent(new Configuration\PlaceholderNode());
        }

        return $definition->getNode(true);
    }

    private function getObjectNode(?PrototypedArrayNode $parent): NodeInterface
    {
        $definition = $this->objectNode()
            ->getRootNode();

        if ($parent !== null) {
            $definition->setParent(new Configuration\PlaceholderNode());
        }

        return $definition->getNode(true);
    }

    private function mapNode(): TreeBuilder
    {
        $builder = new TreeBuilder('map');

        $builder->getRootNode()
            ->arrayPrototype()
                ->validate()
                    ->ifTrue(function (array $value) {
                        return array_key_exists('copy', $value) && array_key_exists('expression', $value);
                    })
                    ->thenInvalid('Your configuration should either contain the "copy" or the "expression" key, not both.')
                ->end()
                ->validate()
                    ->ifTrue(function (array $value) {
                        return array_key_exists('copy', $value) && array_key_exists('constant', $value);
                    })
                    ->thenInvalid('Your configuration should either contain the "copy" or the "constant" key, not both.')
                ->end()
                ->validate()
                    ->ifTrue(function (array $value) {
                        return array_key_exists('copy', $value) && array_key_exists('map', $value);
                    })
                    ->thenInvalid('Your configuration should either contain the "copy" or the "map" key, not both.')
                ->end()
                ->validate()
                    ->ifTrue(function (array $value) {
                        return array_key_exists('expression', $value) && array_key_exists('constant', $value);
                    })
                    ->thenInvalid('Your configuration should either contain the "expression" or the "constant" key, not both.')
                ->end()
                ->validate()
                    ->ifTrue(function (array $value) {
                        return array_key_exists('expression', $value) && array_key_exists('map', $value);
                    })
                    ->thenInvalid('Your configuration should either contain the "expression" or the "map" key, not both.')
                ->end()
                ->validate()
                    ->ifTrue(function (array $value) {
                        return array_key_exists('constant', $value) && array_key_exists('map', $value);
                    })
                    ->thenInvalid('Your configuration should either contain the "constant" or the "map" key, not both.')
                ->end()
                ->validate()
                    ->ifTrue(function (array $value) {
                        return array_key_exists('object', $value) && !array_key_exists('class', $value);
                    })
                    ->thenInvalid('Your configuration should contain the "class" field if the "object" field is present.')
                ->end()
                ->validate()
                    ->ifTrue(function (array $value) {
                        return array_key_exists('object', $value) && !array_key_exists('expression', $value);
                    })
                    ->thenInvalid('Your configuration should contain the "expression" field if the "object" field is present.')
                ->end()
                ->validate()
                    ->ifTrue(function (array $value) {
                        return array_key_exists('collection', $value) && !array_key_exists('class', $value);
                    })
                    ->thenInvalid('Your configuration should contain the "class" field if the "collection" field is present.')
                ->end()
                ->validate()
                    ->ifTrue(function (array $value) {
                        return array_key_exists('collection', $value) && !array_key_exists('expression', $value);
                    })
                    ->thenInvalid('Your configuration should contain the "expression" field if the "collection" field is present.')
                ->end()
                ->validate()
                    ->ifTrue(function (array $value) {
                        return array_key_exists('list', $value) && !array_key_exists('expression', $value);
                    })
                    ->thenInvalid('Your configuration should contain the "expression" field if the "list" field is present.')
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
                ->end()
            ->end()
        ;

        return $builder;
    }

    private function listNode(): TreeBuilder
    {
        $builder = new TreeBuilder('list');

        $builder->getRootNode()
            ->arrayPrototype()
                ->validate()
                    ->ifTrue(function (array $value) {
                        return array_key_exists('copy', $value) && array_key_exists('expression', $value);
                    })
                    ->thenInvalid('Your configuration should either contain the "copy" or the "expression" key, not both.')
                ->end()
                ->validate()
                    ->ifTrue(function (array $value) {
                        return array_key_exists('copy', $value) && array_key_exists('constant', $value);
                    })
                    ->thenInvalid('Your configuration should either contain the "copy" or the "constant" key, not both.')
                ->end()
                ->validate()
                    ->ifTrue(function (array $value) {
                        return array_key_exists('copy', $value) && array_key_exists('map', $value);
                    })
                    ->thenInvalid('Your configuration should either contain the "copy" or the "map" key, not both.')
                ->end()
                ->validate()
                    ->ifTrue(function (array $value) {
                        return array_key_exists('expression', $value) && array_key_exists('constant', $value);
                    })
                    ->thenInvalid('Your configuration should either contain the "expression" or the "constant" key, not both.')
                ->end()
                ->validate()
                    ->ifTrue(function (array $value) {
                        return array_key_exists('expression', $value) && array_key_exists('map', $value);
                    })
                    ->thenInvalid('Your configuration should either contain the "expression" or the "map" key, not both.')
                ->end()
                ->validate()
                    ->ifTrue(function (array $value) {
                        return array_key_exists('constant', $value) && array_key_exists('map', $value);
                    })
                    ->thenInvalid('Your configuration should either contain the "constant" or the "map" key, not both.')
                ->end()
                ->validate()
                    ->ifTrue(function (array $value) {
                        return array_key_exists('object', $value) && !array_key_exists('class', $value);
                    })
                    ->thenInvalid('Your configuration should contain the "class" field if the "object" field is present.')
                ->end()
                ->validate()
                    ->ifTrue(function (array $value) {
                        return array_key_exists('object', $value) && !array_key_exists('expression', $value);
                    })
                    ->thenInvalid('Your configuration should contain the "expression" field if the "object" field is present.')
                ->end()
                ->validate()
                    ->ifTrue(function (array $value) {
                        return array_key_exists('collection', $value) && !array_key_exists('class', $value);
                    })
                    ->thenInvalid('Your configuration should contain the "class" field if the "collection" field is present.')
                ->end()
                ->validate()
                    ->ifTrue(function (array $value) {
                        return array_key_exists('collection', $value) && !array_key_exists('expression', $value);
                    })
                    ->thenInvalid('Your configuration should contain the "expression" field if the "collection" field is present.')
                ->end()
                ->validate()
                    ->ifTrue(function (array $value) {
                        return array_key_exists('list', $value) && !array_key_exists('expression', $value);
                    })
                    ->thenInvalid('Your configuration should contain the "expression" field if the "list" field is present.')
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
                ->end()
            ->end()
        ;

        return $builder;
    }

    private function objectNode(): TreeBuilder
    {
        $builder = new TreeBuilder('object');

        $builder->getRootNode()
            ->arrayPrototype()
                ->validate()
                    ->ifTrue(function (array $value) {
                        return array_key_exists('copy', $value) && array_key_exists('expression', $value);
                    })
                    ->thenInvalid('Your configuration should either contain the "copy" or the "expression" key, not both.')
                ->end()
                ->validate()
                    ->ifTrue(function (array $value) {
                        return array_key_exists('copy', $value) && array_key_exists('constant', $value);
                    })
                    ->thenInvalid('Your configuration should either contain the "copy" or the "constant" key, not both.')
                ->end()
                ->validate()
                    ->ifTrue(function (array $value) {
                        return array_key_exists('copy', $value) && array_key_exists('map', $value);
                    })
                    ->thenInvalid('Your configuration should either contain the "copy" or the "map" key, not both.')
                ->end()
                ->validate()
                    ->ifTrue(function (array $value) {
                        return array_key_exists('expression', $value) && array_key_exists('constant', $value);
                    })
                    ->thenInvalid('Your configuration should either contain the "expression" or the "constant" key, not both.')
                ->end()
                ->validate()
                    ->ifTrue(function (array $value) {
                        return array_key_exists('expression', $value) && array_key_exists('map', $value);
                    })
                    ->thenInvalid('Your configuration should either contain the "expression" or the "map" key, not both.')
                ->end()
                ->validate()
                    ->ifTrue(function (array $value) {
                        return array_key_exists('constant', $value) && array_key_exists('map', $value);
                    })
                    ->thenInvalid('Your configuration should either contain the "constant" or the "map" key, not both.')
                ->end()
                ->validate()
                    ->ifTrue(function (array $value) {
                        return array_key_exists('object', $value) && !array_key_exists('class', $value);
                    })
                    ->thenInvalid('Your configuration should contain the "class" field if the "object" field is present.')
                ->end()
                ->validate()
                    ->ifTrue(function (array $value) {
                        return array_key_exists('object', $value) && !array_key_exists('expression', $value);
                    })
                    ->thenInvalid('Your configuration should contain the "expression" field if the "object" field is present.')
                ->end()
                ->validate()
                    ->ifTrue(function (array $value) {
                        return array_key_exists('collection', $value) && !array_key_exists('class', $value);
                    })
                    ->thenInvalid('Your configuration should contain the "class" field if the "collection" field is present.')
                ->end()
                ->validate()
                    ->ifTrue(function (array $value) {
                        return array_key_exists('collection', $value) && !array_key_exists('expression', $value);
                    })
                    ->thenInvalid('Your configuration should contain the "expression" field if the "collection" field is present.')
                ->end()
                ->validate()
                    ->ifTrue(function (array $value) {
                        return array_key_exists('list', $value) && !array_key_exists('expression', $value);
                    })
                    ->thenInvalid('Your configuration should contain the "expression" field if the "list" field is present.')
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
                ->end()
            ->end()
        ;

        return $builder;
    }
}
