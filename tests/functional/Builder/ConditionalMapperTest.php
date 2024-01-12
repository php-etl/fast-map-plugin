<?php

declare(strict_types=1);

namespace functional\Kiboko\Plugin\FastMap\Builder;

use Kiboko\Component\FastMapConfig\ArrayAppendBuilder;
use Kiboko\Component\PHPUnitExtension\Assert\TransformerBuilderAssertTrait;
use Kiboko\Plugin\FastMap\Builder\ArrayMapper;
use Kiboko\Plugin\FastMap\Builder\Transformer;
use Symfony\Component\ExpressionLanguage\ExpressionLanguage;

final class ConditionalMapperTest extends BuilderTestCase
{
    use TransformerBuilderAssertTrait;

    public function testWithOneConditionThatMatch()
    {
        $builder = new \Kiboko\Plugin\FastMap\Builder\ConditionalMapper($interpreter = new ExpressionLanguage());

        $builder->withAlternative(
            'input["enable"] == true',
            new ArrayMapper(
                $mapper = new ArrayAppendBuilder(
                    interpreter: $interpreter,
                ),
            )
        );

        $mapper->children()->constant('[updated_at]', '2024-01-11');

        $builder = new Transformer($builder);

        $this->assertBuildsTransformerTransformsLike(
            [
                [
                    'identifier' => 1,
                    'enable' => true,
                ]
            ],
            [
                [
                    'identifier' => 1,
                    'enable' => true,
                    'updated_at' => '2024-01-11'
                ]
            ],
            $builder,
        );
    }

    public function testWithSeveralConditionsThatMatch()
    {
        $builder = new \Kiboko\Plugin\FastMap\Builder\ConditionalMapper($interpreter = new ExpressionLanguage());
        $mapperBuilder = new ArrayMapper(
            $mapper = new ArrayAppendBuilder(
                interpreter: $interpreter,
            ),
        );

        $builder->withAlternative(
            'input["enable"] == true',
            $mapperBuilder
        );

        $builder->withAlternative(
            'input["identifier"] === 1',
            $mapperBuilder
        );

        $mapper->children()->constant('[updated_at]', '2024-01-11');
        $mapper->children()->constant('[price]', '19.99');

        $builder = new Transformer($builder);

        $this->assertBuildsTransformerTransformsLike(
            [
                [
                    'identifier' => 1,
                    'enable' => true,
                ]
            ],
            [
                [
                    'identifier' => 1,
                    'enable' => true,
                    'updated_at' => '2024-01-11',
                    'price' => 19.99
                ]
            ],
            $builder,
        );
    }

    public function testWithConditionThatDoesNotMatch()
    {
        $builder = new \Kiboko\Plugin\FastMap\Builder\ConditionalMapper($interpreter = new ExpressionLanguage());

        $builder->withAlternative(
            'input["identifier"] === 0',
            new ArrayMapper(
                $mapper = new ArrayAppendBuilder(
                    interpreter: $interpreter,
                ),
            )
        );

        $mapper->children()->constant('[updated_at]', '2024-01-11');

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
                    'identifier' => 1,
                    'enabled' => true,
                ]
            ],
            $builder,
        );
    }
}
