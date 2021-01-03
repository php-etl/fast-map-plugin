<?php declare(strict_types=1);

namespace Kiboko\Component\ETL\Flow\FastMap\Configuration;

use Kiboko\Component\ETL\Flow\FastMap\Configuration;
use Symfony\Component\Config\Definition\ConfigurationInterface;

final class ArrayMapper implements ConfigurationInterface
{
    public function getConfigTreeBuilder()
    {
        $builder = (new Configuration())->getConfigTreeBuilder();

        $builder->getRootNode()
            ->validate()
                ->ifTrue(function (array $value) {
                    return !array_key_exists('array', $value);
                })
                ->thenInvalid('Your configuration should contain the "array" key.')
            ->end()
        ;

        return $builder;
    }
}
