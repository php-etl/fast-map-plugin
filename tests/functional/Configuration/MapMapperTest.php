<?php

declare(strict_types=1);

namespace functional\Kiboko\Plugin\FastMap\Configuration;

use Kiboko\Plugin\FastMap;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Config\Definition\Exception\InvalidConfigurationException;
use Symfony\Component\Config\Definition\Processor;

final class MapMapperTest extends TestCase
{
    public function testEmpty(): void
    {
        $processor = new Processor();
        $configuration = new FastMap\Configuration\MapMapper();

        $this->assertEmpty(
            $processor->processConfiguration($configuration, [
                [],
            ])
        );
    }

    public function testWithCopyField(): void
    {
        $processor = new Processor();
        $configuration = new FastMap\Configuration\MapMapper();

        $this->assertEquals(
            [
                [
                    'field' => '[foo]',
                    'copy' => '[foo]',
                ],
            ],
            $processor->processConfiguration($configuration, [
                [
                    [
                        'field' => '[foo]',
                        'copy' => '[foo]',
                    ],
                ],
            ])
        );
    }

    public function testWithExpressionField(): void
    {
        $processor = new Processor();
        $configuration = new FastMap\Configuration\MapMapper();

        $this->assertEquals(
            [
                [
                    'field' => '[foo]',
                    'expression' => 'input["foo"]',
                ],
            ],
            $processor->processConfiguration($configuration, [
                [
                    [
                        'field' => '[foo]',
                        'expression' => 'input["foo"]',
                    ],
                ],
            ])
        );
    }

    public function testWithConstantField(): void
    {
        $processor = new Processor();
        $configuration = new FastMap\Configuration\MapMapper();

        $this->assertEquals(
            [
                [
                    'field' => '[foo]',
                    'constant' => 'Lorem ipsum dolor sit amet, consectetur adipiscing elit.',
                ],
            ],
            $processor->processConfiguration($configuration, [
                [
                    [
                        'field' => '[foo]',
                        'constant' => 'Lorem ipsum dolor sit amet, consectetur adipiscing elit.',
                    ],
                ],
            ])
        );
    }

    public function testWithMapField(): void
    {
        $processor = new Processor();
        $configuration = new FastMap\Configuration\MapMapper();

        $this->assertEquals(
            [
                [
                    'field' => '[foo]',
                    'expression' => 'input["foo"]',
                    'map' => [
                        [
                            'field' => '[bar]',
                            'constant' => 'Lorem ipsum dolor sit amet, consectetur adipiscing elit.',
                        ],
                    ],
                ],
            ],
            $processor->processConfiguration($configuration, [
                [
                    [
                        'field' => '[foo]',
                        'expression' => 'input["foo"]',
                        'map' => [
                            [
                                'field' => '[bar]',
                                'constant' => 'Lorem ipsum dolor sit amet, consectetur adipiscing elit.',
                            ],
                        ],
                    ],
                ],
            ])
        );
    }

    public function testWithListField(): void
    {
        $processor = new Processor();
        $configuration = new FastMap\Configuration\MapMapper();

        $this->assertEquals(
            [
                [
                    'field' => '[foo]',
                    'expression' => 'input["foo"]',
                    'list' => [
                        [
                            'field' => '[bar]',
                            'constant' => 'Lorem ipsum dolor sit amet, consectetur adipiscing elit.',
                        ],
                    ],
                ],
            ],
            $processor->processConfiguration($configuration, [
                [
                    [
                        'field' => '[foo]',
                        'expression' => 'input["foo"]',
                        'list' => [
                            [
                                'field' => '[bar]',
                                'constant' => 'Lorem ipsum dolor sit amet, consectetur adipiscing elit.',
                            ],
                        ],
                    ],
                ],
            ])
        );
    }

    public function testWithListFieldWithoutExpression(): void
    {
        $processor = new Processor();
        $configuration = new FastMap\Configuration\MapMapper();

        $this->expectException(InvalidConfigurationException::class);
        $this->expectExceptionMessage('Your configuration should contain the "expression" field if the "list" field is present.');

        $processor->processConfiguration($configuration, [
            [
                [
                    'field' => '[foo]',
                    'list' => [
                        [
                            'field' => '[bar]',
                            'constant' => 'Lorem ipsum dolor sit amet, consectetur adipiscing elit.',
                        ],
                    ],
                ],
            ],
        ]);
    }

    public function testWithObjectField(): void
    {
        $processor = new Processor();
        $configuration = new FastMap\Configuration\MapMapper();

        $this->assertEquals(
            [
                [
                    'field' => '[foo]',
                    'class' => 'stdClass',
                    'expression' => 'input["foo"]',
                    'object' => [
                        [
                            'field' => '[bar]',
                            'constant' => 'Lorem ipsum dolor sit amet, consectetur adipiscing elit.',
                        ],
                    ],
                ],
            ],
            $processor->processConfiguration($configuration, [
                [
                    [
                        'field' => '[foo]',
                        'class' => \stdClass::class,
                        'expression' => 'input["foo"]',
                        'object' => [
                            [
                                'field' => '[bar]',
                                'constant' => 'Lorem ipsum dolor sit amet, consectetur adipiscing elit.',
                            ],
                        ],
                    ],
                ],
            ])
        );
    }

    public function testWithObjectFieldWithoutClass(): void
    {
        $processor = new Processor();
        $configuration = new FastMap\Configuration\MapMapper();

        $this->expectException(InvalidConfigurationException::class);
        $this->expectExceptionMessage('Your configuration should contain the "class" field if the "object" field is present.');

        $processor->processConfiguration($configuration, [
            [
                [
                    'field' => '[foo]',
                    'expression' => 'input["foo"]',
                    'object' => [
                        [
                            'field' => '[bar]',
                            'constant' => 'Lorem ipsum dolor sit amet, consectetur adipiscing elit.',
                        ],
                    ],
                ],
            ],
        ]);
    }

    public function testWithObjectFieldWithoutExpression(): void
    {
        $processor = new Processor();
        $configuration = new FastMap\Configuration\MapMapper();

        $this->expectException(InvalidConfigurationException::class);
        $this->expectExceptionMessage('Your configuration should contain the "expression" field if the "object" field is present.');

        $processor->processConfiguration($configuration, [
            [
                [
                    'field' => '[foo]',
                    'class' => \stdClass::class,
                    'object' => [
                        [
                            'field' => '[bar]',
                            'constant' => 'Lorem ipsum dolor sit amet, consectetur adipiscing elit.',
                        ],
                    ],
                ],
            ],
        ]);
    }

    public function testWithCollectionField(): void
    {
        $processor = new Processor();
        $configuration = new FastMap\Configuration\MapMapper();

        $this->assertEquals(
            [
                [
                    'field' => '[foo]',
                    'class' => 'stdClass',
                    'expression' => 'input["foo"]',
                    'collection' => [
                        [
                            'field' => '[bar]',
                            'constant' => 'Lorem ipsum dolor sit amet, consectetur adipiscing elit.',
                        ],
                    ],
                ],
            ],
            $processor->processConfiguration($configuration, [
                [
                    [
                        'field' => '[foo]',
                        'class' => \stdClass::class,
                        'expression' => 'input["foo"]',
                        'collection' => [
                            [
                                'field' => '[bar]',
                                'constant' => 'Lorem ipsum dolor sit amet, consectetur adipiscing elit.',
                            ],
                        ],
                    ],
                ],
            ])
        );
    }

    public function testWithCollectionFieldWithoutClass(): void
    {
        $processor = new Processor();
        $configuration = new FastMap\Configuration\MapMapper();

        $this->expectException(InvalidConfigurationException::class);
        $this->expectExceptionMessage('Your configuration should contain the "class" field if the "collection" field is present.');

        $processor->processConfiguration($configuration, [
            [
                [
                    'field' => '[foo]',
                    'expression' => 'input["foo"]',
                    'collection' => [
                        [
                            'field' => '[bar]',
                            'constant' => 'Lorem ipsum dolor sit amet, consectetur adipiscing elit.',
                        ],
                    ],
                ],
            ],
        ]);
    }

    public function testWithCollectionFieldWithoutExpression(): void
    {
        $processor = new Processor();
        $configuration = new FastMap\Configuration\MapMapper();

        $this->expectException(InvalidConfigurationException::class);
        $this->expectExceptionMessage('Your configuration should contain the "expression" field if the "collection" field is present.');

        $processor->processConfiguration($configuration, [
            [
                [
                    'field' => '[foo]',
                    'class' => \stdClass::class,
                    'collection' => [
                        [
                            'field' => '[bar]',
                            'constant' => 'Lorem ipsum dolor sit amet, consectetur adipiscing elit.',
                        ],
                    ],
                ],
            ],
        ]);
    }
}
