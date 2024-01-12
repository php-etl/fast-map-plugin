<?php

declare(strict_types=1);

namespace functional\Builder;

use functional\Kiboko\Plugin\FastMap\Builder\BuilderTestCase;
use Kiboko\Component\FastMapConfig\ArrayAppendBuilder;
use Kiboko\Component\FastMapConfig\ArrayBuilder;
use Kiboko\Component\PHPUnitExtension\Assert\TransformerBuilderAssertTrait;
use Kiboko\Plugin\FastMap\Builder\ArrayMapper;
use Kiboko\Plugin\FastMap\Builder\Transformer;
use Symfony\Component\ExpressionLanguage\ExpressionLanguage;

final class ArrayMapperTest extends BuilderTestCase
{
    use TransformerBuilderAssertTrait;

    public function testWithASingleCopyField()
    {
        $builder = new \Kiboko\Plugin\FastMap\Builder\ArrayMapper(
            $mapper = new ArrayBuilder()
        );

        $mapper->children()->copy('[id]', '[identifier]');

        $builder = new Transformer($builder);

        $this->assertBuildsTransformerTransformsLike(
            [
                [
                    'identifier' => 1,
                    'enabled' => true,
                ]
            ],
            [
                [
                    'id' => 1,
                ]
            ],
            $builder,
        );
    }

    public function testWithSameNumberOfOutputFields()
    {
        $builder = new \Kiboko\Plugin\FastMap\Builder\ArrayMapper(
            $mapper = new ArrayBuilder()
        );

        $mapper->children()->copy('[id]', '[identifier]');
        $mapper->children()->copy('[enabled]', '[enabled]');

        $builder = new Transformer($builder);

        $this->assertBuildsTransformerTransformsLike(
            [
                [
                    'identifier' => 1,
                    'enabled' => true,
                ]
            ],
            [
                [
                    'id' => 1,
                    'enabled' => true,
                ]
            ],
            $builder,
        );
    }
}
