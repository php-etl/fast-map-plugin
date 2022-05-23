<?php

declare(strict_types=1);

namespace Kiboko\Plugin\FastMap\Configuration;

use Kiboko\Plugin\FastMap\Configuration;
use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\ConfigurationInterface;

final class ListMapper implements ConfigurationInterface
{
    public function getConfigTreeBuilder(): TreeBuilder
    {
        return (new Configuration())->getListTreeBuilder();
    }
}
