<?php

declare(strict_types=1);

namespace Kiboko\Plugin\FastMap\Factory\Repository;

use Kiboko\Contract\Configurator;
use Kiboko\Plugin\FastMap;

final class ConditionalMapper implements Configurator\RepositoryInterface
{
    use RepositoryTrait;

    public function __construct(private readonly FastMap\Builder\ConditionalMapper $builder)
    {
        $this->files = [];
        $this->packages = [];
    }

    public function getBuilder(): FastMap\Builder\ConditionalMapper
    {
        return $this->builder;
    }
}
