<?php

declare(strict_types=1);

namespace Kiboko\Plugin\FastMap\Factory\Repository;

use Kiboko\Contract\Configurator;
use Kiboko\Plugin\FastMap;

final class ObjectMapper implements Configurator\RepositoryInterface
{
    use RepositoryTrait;

    public function __construct(private readonly FastMap\Builder\ObjectMapper $builder)
    {
        $this->files = [];
        $this->packages = [];
    }

    public function getBuilder(): FastMap\Builder\ObjectMapper
    {
        return $this->builder;
    }
}
