<?php declare(strict_types=1);

namespace Kiboko\Plugin\FastMap\Factory\Repository;

use Kiboko\Contract\Configurator;
use Kiboko\Plugin\FastMap;

final class ConditionalMapper implements Configurator\RepositoryInterface
{
    use RepositoryTrait;

    public function __construct(private FastMap\Builder\ConditionalMapperBuilder $builder)
    {
        $this->files = [];
        $this->packages = [];
    }

    public function getBuilder(): FastMap\Builder\ConditionalMapperBuilder
    {
        return $this->builder;
    }
}
