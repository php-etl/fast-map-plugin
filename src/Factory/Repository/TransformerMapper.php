<?php declare(strict_types=1);

namespace Kiboko\Plugin\FastMap\Factory\Repository;

use Kiboko\Contract\Configurator;
use Kiboko\Plugin\FastMap;

final class TransformerMapper implements Configurator\StepRepositoryInterface
{
    use RepositoryTrait;

    public function __construct(private FastMap\Builder\TransformerBuilder $builder)
    {
        $this->files = [];
        $this->packages = [];
    }

    public function getBuilder(): FastMap\Builder\TransformerBuilder
    {
        return $this->builder;
    }
}