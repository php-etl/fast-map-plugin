<?php declare(strict_types=1);

namespace functional\Kiboko\Plugin\FastMap\Builder;

use Kiboko\Component\FastMapConfig\ArrayBuilder;
use Kiboko\Plugin\FastMap\Builder\ArrayMapper;
use Kiboko\Plugin\FastMap\Builder\Transformer;
use Kiboko\Plugin\FastMap\Configuration\ConfigurationApplier;

final class ArrayMapperTest extends BuilderTestCase
{
    public function testSuccessfulArrayMapping()
    {
        $mapper = new ArrayBuilder();

        $builder = new Transformer(
            new ArrayMapper($mapper)
        );

        (new ConfigurationApplier())(
            $mapper->children(),
            [
                'object' => [
                    'field' => 'key',
                    'expression' => 'input["key"]',
                ]
            ]
        );

        $this->assertArrayMapsAs(
            [
                [
                    'map' => [
                        'key' => 'value'
                    ]
                ]
            ],
            $builder
        );
    }
}
