<?php

declare(strict_types=1);

namespace functional\Kiboko\Plugin\FastMap\Builder;

use functional\Kiboko\Plugin\FastMap\Builder\DTO\Product;
use Kiboko\Component\FastMapConfig\ObjectBuilder;
use Kiboko\Component\PHPUnitExtension\Assert\TransformerBuilderAssertTrait;
use Kiboko\Plugin\FastMap\Builder\Transformer;

final class ObjectMapperTest extends BuilderTestCase
{
    use TransformerBuilderAssertTrait;

    public function testObjectMapper()
    {
        $builder = new \Kiboko\Plugin\FastMap\Builder\ObjectMapper(
            $mapper = new ObjectBuilder(Product::class)
        );

        $mapper->arguments('input.id', 'input.enabled');

        $builder = new Transformer($builder);

        $this->assertBuildsTransformerTransformsLike(
            [
                new Product(1)
            ],
            [
                new Product(1)
            ],
            $builder,
        );

        $this->assertBuildsTransformerTransformsLike(
            [
                new Product(1, true)
            ],
            [
                new Product(1, true)
            ],
            $builder,
        );
    }
}
