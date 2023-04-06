<?php

declare(strict_types=1);

namespace functional\Kiboko\Plugin\FastMap;

use Kiboko\Plugin\FastMap;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Config\Definition\Exception\InvalidConfigurationException;
use Symfony\Component\Config\Definition\Processor;


final class ConfigurationTest extends TestCase
{
    public function testEmpty(): void
    {
        $processor = new Processor();
        $configuration = new FastMap\Configuration();

        $this->assertEquals(
            [],
            $processor->processConfiguration($configuration, [
                [],
            ])
        );
    }

    public function testWithNoFields(): void
    {
        $processor = new Processor();
        $configuration = new FastMap\Configuration();

        $this->assertEquals(
            [],
            $processor->processConfiguration($configuration, [
                [
                    'map' => [],
                ],
            ])
        );
    }

    public function testWithCompetingFields(): void
    {
        $processor = new Processor();
        $configuration = new FastMap\Configuration();

        $this->expectException(InvalidConfigurationException::class);
        $this->expectExceptionMessage('Your configuration should either contain the "map" or the "object" field, not both.');

        $processor->processConfiguration($configuration, [
            [
                'map' => [
                    [
                        'field' => '[foo]',
                        'copy' => '[foo]',
                    ],
                ],
            ],
            [
                'object' => [
                    [
                        'field' => '[foo]',
                        'copy' => '[foo]',
                    ],
                ],
            ],
        ]);
    }

    public function testMapWithCopyField(): void
    {
        $processor = new Processor();
        $configuration = new FastMap\Configuration();

        $this->assertEquals(
            [
                'map' => [
                    [
                        'field' => '[foo]',
                        'copy' => '[foo]',
                    ],
                ],
            ],
            $processor->processConfiguration($configuration, [
                [
                    'map' => [
                        [
                            'field' => '[foo]',
                            'copy' => '[foo]',
                        ],
                    ],
                ],
            ])
        );
    }

    public function testMapWithExpressionField(): void
    {
        $processor = new Processor();
        $configuration = new FastMap\Configuration();

        $this->assertEquals(
            [
                'map' => [
                    [
                        'field' => '[foo]',
                        'expression' => 'input["foo"]',
                    ],
                ],
            ],
            $processor->processConfiguration($configuration, [
                [
                    'map' => [
                        [
                            'field' => '[foo]',
                            'expression' => 'input["foo"]',
                        ],
                    ],
                ],
            ])
        );
    }

    public function testMapWithConstantField(): void
    {
        $processor = new Processor();
        $configuration = new FastMap\Configuration();

        $this->assertEquals(
            [
                'map' => [
                    [
                        'field' => '[foo]',
                        'constant' => 'Lorem ipsum dolor sit amet, consectetur adipiscing elit.',
                    ],
                ],
            ],
            $processor->processConfiguration($configuration, [
                [
                    'map' => [
                        [
                            'field' => '[foo]',
                            'constant' => 'Lorem ipsum dolor sit amet, consectetur adipiscing elit.',
                        ],
                    ],
                ],
            ])
        );
    }

    public function testMapWithMapField(): void
    {
        $processor = new Processor();
        $configuration = new FastMap\Configuration();

        $this->assertEquals(
            [
                'map' => [
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
            ],
            $processor->processConfiguration($configuration, [
                [
                    'map' => [
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
                ],
            ])
        );
    }

    public function testMapWithListField(): void
    {
        $processor = new Processor();
        $configuration = new FastMap\Configuration();

        $this->assertEquals(
            [
                'map' => [
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
            ],
            $processor->processConfiguration($configuration, [
                [
                    'map' => [
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
                ],
            ])
        );
    }

    public function testMapWithListFieldWithoutExpression(): void
    {
        $processor = new Processor();
        $configuration = new FastMap\Configuration();

        $this->expectException(InvalidConfigurationException::class);
        $this->expectExceptionMessage('Your configuration should contain the "expression" field if the "list" field is present.');

        $processor->processConfiguration($configuration, [
            [
                'map' => [
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
            ],
        ]);
    }

    public function testMapWithObjectField(): void
    {
        $processor = new Processor();
        $configuration = new FastMap\Configuration();

        $this->assertEquals(
            [
                'map' => [
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
            ],
            $processor->processConfiguration($configuration, [
                [
                    'map' => [
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
                ],
            ])
        );
    }

    public function testMapWithObjectFieldWithoutClass(): void
    {
        $processor = new Processor();
        $configuration = new FastMap\Configuration();

        $this->expectException(InvalidConfigurationException::class);
        $this->expectExceptionMessage('Your configuration should contain the "class" field if the "object" field is present.');

        $processor->processConfiguration($configuration, [
            [
                'map' => [
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
            ],
        ]);
    }

    public function testMapWithObjectFieldWithoutExpression(): void
    {
        $processor = new Processor();
        $configuration = new FastMap\Configuration();

        $this->expectException(InvalidConfigurationException::class);
        $this->expectExceptionMessage('Your configuration should contain the "expression" field if the "object" field is present.');

        $processor->processConfiguration($configuration, [
            [
                'map' => [
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
            ],
        ]);
    }

    public function testMapWithCollectionField(): void
    {
        $processor = new Processor();
        $configuration = new FastMap\Configuration();

        $this->assertEquals(
            [
                'map' => [
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
            ],
            $processor->processConfiguration($configuration, [
                [
                    'map' => [
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
                ],
            ])
        );
    }

    public function testMapWithCollectionFieldWithoutClass(): void
    {
        $processor = new Processor();
        $configuration = new FastMap\Configuration();

        $this->expectException(InvalidConfigurationException::class);
        $this->expectExceptionMessage('Your configuration should contain the "class" field if the "collection" field is present.');

        $processor->processConfiguration($configuration, [
            [
                'map' => [
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
            ],
        ]);
    }

    public function testMapWithCollectionFieldWithoutExpression(): void
    {
        $processor = new Processor();
        $configuration = new FastMap\Configuration();

        $this->expectException(InvalidConfigurationException::class);
        $this->expectExceptionMessage('Your configuration should contain the "expression" field if the "collection" field is present.');

        $processor->processConfiguration($configuration, [
            [
                'map' => [
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
            ],
        ]);
    }

    public function testListWithCopyField(): void
    {
        $processor = new Processor();
        $configuration = new FastMap\Configuration();

        $this->assertEquals(
            [
                'expression' => 'input',
                'list' => [
                    [
                        'field' => '[foo]',
                        'copy' => '[foo]',
                    ],
                ],
            ],
            $processor->processConfiguration($configuration, [
                [
                    'expression' => 'input',
                    'list' => [
                        [
                            'field' => '[foo]',
                            'copy' => '[foo]',
                        ],
                    ],
                ],
            ])
        );
    }

    public function testListWithExpressionField(): void
    {
        $processor = new Processor();
        $configuration = new FastMap\Configuration();

        $this->assertEquals(
            [
                'expression' => 'input',
                'list' => [
                    [
                        'field' => '[foo]',
                        'expression' => 'input["foo"]',
                    ],
                ],
            ],
            $processor->processConfiguration($configuration, [
                [
                    'expression' => 'input',
                    'list' => [
                        [
                            'field' => '[foo]',
                            'expression' => 'input["foo"]',
                        ],
                    ],
                ],
            ])
        );
    }

    public function testListWithConstantField(): void
    {
        $processor = new Processor();
        $configuration = new FastMap\Configuration();

        $this->assertEquals(
            [
                'expression' => 'input',
                'list' => [
                    [
                        'field' => '[foo]',
                        'constant' => 'Lorem ipsum dolor sit amet, consectetur adipiscing elit.',
                    ],
                ],
            ],
            $processor->processConfiguration($configuration, [
                [
                    'expression' => 'input',
                    'list' => [
                        [
                            'field' => '[foo]',
                            'constant' => 'Lorem ipsum dolor sit amet, consectetur adipiscing elit.',
                        ],
                    ],
                ],
            ])
        );
    }

    public function testListWithMapField(): void
    {
        $processor = new Processor();
        $configuration = new FastMap\Configuration();

        $this->assertEquals(
            [
                'expression' => 'input',
                'list' => [
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
            ],
            $processor->processConfiguration($configuration, [
                [
                    'expression' => 'input',
                    'list' => [
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
                ],
            ])
        );
    }

    public function testListWithListField(): void
    {
        $processor = new Processor();
        $configuration = new FastMap\Configuration();

        $this->assertEquals(
            [
                'expression' => 'input',
                'list' => [
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
            ],
            $processor->processConfiguration($configuration, [
                [
                    'expression' => 'input',
                    'list' => [
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
                ],
            ])
        );
    }

    public function testListWithListFieldWithoutExpression(): void
    {
        $processor = new Processor();
        $configuration = new FastMap\Configuration();

        $this->expectException(InvalidConfigurationException::class);
        $this->expectExceptionMessage('Your configuration should contain the "expression" field if the "list" field is present.');

        $processor->processConfiguration($configuration, [
            [
                'expression' => 'input',
                'list' => [
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
            ],
        ]);
    }

    public function testListWithObjectField(): void
    {
        $processor = new Processor();
        $configuration = new FastMap\Configuration();

        $this->assertEquals(
            [
                'expression' => 'input',
                'list' => [
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
            ],
            $processor->processConfiguration($configuration, [
                [
                    'expression' => 'input',
                    'list' => [
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
                ],
            ])
        );
    }

    public function tetsListWithObjectFieldWithoutClass(): void
    {
        $processor = new Processor();
        $configuration = new FastMap\Configuration();

        $this->expectException(InvalidConfigurationException::class);
        $this->expectExceptionMessage('Your configuration should contain the "class" field if the "object" field is present.');

        $processor->processConfiguration($configuration, [
            [
                'expression' => 'input',
                'list' => [
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
            ],
        ]);
    }

    public function testListWithObjectFieldWithoutExpression(): void
    {
        $processor = new Processor();
        $configuration = new FastMap\Configuration();

        $this->expectException(InvalidConfigurationException::class);
        $this->expectExceptionMessage('Your configuration should contain the "expression" field if the "object" field is present.');

        $processor->processConfiguration($configuration, [
            [
                'expression' => 'input',
                'list' => [
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
            ],
        ]);
    }

    public function testListWithCollectionField(): void
    {
        $processor = new Processor();
        $configuration = new FastMap\Configuration();

        $this->assertEquals(
            [
                'expression' => 'input',
                'list' => [
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
            ],
            $processor->processConfiguration($configuration, [
                [
                    'expression' => 'input',
                    'list' => [
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
                ],
            ])
        );
    }

    public function testListWithCollectionFieldWithoutClass(): void
    {
        $processor = new Processor();
        $configuration = new FastMap\Configuration();

        $this->expectException(InvalidConfigurationException::class);
        $this->expectExceptionMessage('Your configuration should contain the "class" field if the "collection" field is present.');

        $processor->processConfiguration($configuration, [
            [
                'expression' => 'input',
                'list' => [
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
            ],
        ]);
    }

    public function testListWithCollectionFieldWithoutExpression(): void
    {
        $processor = new Processor();
        $configuration = new FastMap\Configuration();

        $this->expectException(InvalidConfigurationException::class);
        $this->expectExceptionMessage('Your configuration should contain the "expression" field if the "collection" field is present.');

        $processor->processConfiguration($configuration, [
            [
                'expression' => 'input',
                'list' => [
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
            ],
        ]);
    }

    public function testObjectWithoutClass(): void
    {
        $processor = new Processor();
        $configuration = new FastMap\Configuration();

        $this->expectException(InvalidConfigurationException::class);
        $this->expectExceptionMessage('Your configuration should contain the "class" field if the "object" field is present.');

        $processor->processConfiguration($configuration, [
            [
                'expression' => 'input',
                'object' => [
                    [
                        'field' => '[foo]',
                        'copy' => '[foo]',
                    ],
                ],
            ],
        ]);
    }

    public function testObjectWithoutExpression(): void
    {
        $processor = new Processor();
        $configuration = new FastMap\Configuration();

        $this->expectException(InvalidConfigurationException::class);
        $this->expectExceptionMessage('Your configuration should contain the "expression" field if the "object" field is present.');

        $processor->processConfiguration($configuration, [
            [
                'class' => \stdClass::class,
                'object' => [
                    [
                        'field' => '[foo]',
                        'copy' => '[foo]',
                    ],
                ],
            ],
        ]);
    }

    public function tetsObjectWithCopyField(): void
    {
        $processor = new Processor();
        $configuration = new FastMap\Configuration();

        $this->assertEquals(
            [
                'class' => 'stdClass',
                'expression' => 'input',
                'object' => [
                    [
                        'field' => '[foo]',
                        'copy' => '[foo]',
                    ],
                ],
            ],
            $processor->processConfiguration($configuration, [
                [
                    'class' => \stdClass::class,
                    'expression' => 'input',
                    'object' => [
                        [
                            'field' => '[foo]',
                            'copy' => '[foo]',
                        ],
                    ],
                ],
            ])
        );
    }

    public function testObjectWithExpressionField(): void
    {
        $processor = new Processor();
        $configuration = new FastMap\Configuration();

        $this->assertEquals(
            [
                'class' => 'stdClass',
                'expression' => 'input',
                'object' => [
                    [
                        'field' => '[foo]',
                        'expression' => 'input["foo"]',
                    ],
                ],
            ],
            $processor->processConfiguration($configuration, [
                [
                    'class' => \stdClass::class,
                    'expression' => 'input',
                    'object' => [
                        [
                            'field' => '[foo]',
                            'expression' => 'input["foo"]',
                        ],
                    ],
                ],
            ])
        );
    }

    public function testObjectWithConstantField(): void
    {
        $processor = new Processor();
        $configuration = new FastMap\Configuration();

        $this->assertEquals(
            [
                'class' => 'stdClass',
                'expression' => 'input',
                'object' => [
                    [
                        'field' => '[foo]',
                        'constant' => 'Lorem ipsum dolor sit amet, consectetur adipiscing elit.',
                    ],
                ],
            ],
            $processor->processConfiguration($configuration, [
                [
                    'class' => \stdClass::class,
                    'expression' => 'input',
                    'object' => [
                        [
                            'field' => '[foo]',
                            'constant' => 'Lorem ipsum dolor sit amet, consectetur adipiscing elit.',
                        ],
                    ],
                ],
            ])
        );
    }

    public function testObjectWithMapField(): void
    {
        $processor = new Processor();
        $configuration = new FastMap\Configuration();

        $this->assertEquals(
            [
                'class' => 'stdClass',
                'expression' => 'input',
                'object' => [
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
            ],
            $processor->processConfiguration($configuration, [
                [
                    'class' => \stdClass::class,
                    'expression' => 'input',
                    'object' => [
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
                ],
            ])
        );
    }

    public function testObjectWithListField(): void
    {
        $processor = new Processor();
        $configuration = new FastMap\Configuration();

        $this->assertEquals(
            [
                'class' => 'stdClass',
                'expression' => 'input',
                'object' => [
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
            ],
            $processor->processConfiguration($configuration, [
                [
                    'class' => \stdClass::class,
                    'expression' => 'input',
                    'object' => [
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
                ],
            ])
        );
    }

    public function testObjectWithListFieldWithoutExpression(): void
    {
        $processor = new Processor();
        $configuration = new FastMap\Configuration();

        $this->expectException(InvalidConfigurationException::class);
        $this->expectExceptionMessage('Your configuration should contain the "expression" field if the "list" field is present.');

        $processor->processConfiguration($configuration, [
            [
                'class' => \stdClass::class,
                'expression' => 'input',
                'object' => [
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
            ],
        ]);
    }

    public function testObjectWithObjectField(): void
    {
        $processor = new Processor();
        $configuration = new FastMap\Configuration();

        $this->assertEquals(
            [
                'class' => 'stdClass',
                'expression' => 'input',
                'object' => [
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
            ],
            $processor->processConfiguration($configuration, [
                [
                    'class' => \stdClass::class,
                    'expression' => 'input',
                    'object' => [
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
                ],
            ])
        );
    }

    public function testObjectWithObjectFieldWithoutClass(): void
    {
        $processor = new Processor();
        $configuration = new FastMap\Configuration();

        $this->expectException(InvalidConfigurationException::class);
        $this->expectExceptionMessage('Your configuration should contain the "class" field if the "object" field is present.');

        $processor->processConfiguration($configuration, [
            [
                'class' => \stdClass::class,
                'expression' => 'input',
                'object' => [
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
            ],
        ]);
    }

    public function testObjectWithObjectFieldWithoutExpression(): void
    {
        $processor = new Processor();
        $configuration = new FastMap\Configuration();

        $this->expectException(InvalidConfigurationException::class);
        $this->expectExceptionMessage('Your configuration should contain the "expression" field if the "object" field is present.');

        $processor->processConfiguration($configuration, [
            [
                'class' => \stdClass::class,
                'expression' => 'input',
                'object' => [
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
            ],
        ]);
    }

    public function testObjectWithCollectionField(): void
    {
        $processor = new Processor();
        $configuration = new FastMap\Configuration();

        $this->assertEquals(
            [
                'class' => 'stdClass',
                'expression' => 'input',
                'object' => [
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
            ],
            $processor->processConfiguration($configuration, [
                [
                    'class' => \stdClass::class,
                    'expression' => 'input',
                    'object' => [
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
                ],
            ])
        );
    }

    public function testObjectWithCollectionFieldWithoutClass(): void
    {
        $processor = new Processor();
        $configuration = new FastMap\Configuration();

        $this->expectException(InvalidConfigurationException::class);
        $this->expectExceptionMessage('Your configuration should contain the "class" field if the "collection" field is present.');

        $processor->processConfiguration($configuration, [
            [
                'class' => \stdClass::class,
                'expression' => 'input',
                'object' => [
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
            ],
        ]);
    }

    public function testObjectWithCollectionFieldWithoutExpression(): void
    {
        $processor = new Processor();
        $configuration = new FastMap\Configuration();

        $this->expectException(InvalidConfigurationException::class);
        $this->expectExceptionMessage('Your configuration should contain the "expression" field if the "collection" field is present.');

        $processor->processConfiguration($configuration, [
            [
                'class' => \stdClass::class,
                'expression' => 'input',
                'object' => [
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
            ],
        ]);
    }

    public function testCollectionWithoutClass(): void
    {
        $processor = new Processor();
        $configuration = new FastMap\Configuration();

        $this->expectException(InvalidConfigurationException::class);
        $this->expectExceptionMessage('Your configuration should contain the "class" field if the "collection" field is present.');

        $processor->processConfiguration($configuration, [
            [
                'expression' => 'input',
                'collection' => [
                    [
                        'field' => '[foo]',
                        'copy' => '[foo]',
                    ],
                ],
            ],
        ]);
    }

    public function testCollectionWithoutExpression(): void
    {
        $processor = new Processor();
        $configuration = new FastMap\Configuration();

        $this->expectException(InvalidConfigurationException::class);
        $this->expectExceptionMessage('Your configuration should contain the "expression" field if the "collection" field is present.');

        $processor->processConfiguration($configuration, [
            [
                'class' => \stdClass::class,
                'collection' => [
                    [
                        'field' => '[foo]',
                        'copy' => '[foo]',
                    ],
                ],
            ],
        ]);
    }

    public function testCollectionWithCopyField(): void
    {
        $processor = new Processor();
        $configuration = new FastMap\Configuration();

        $this->assertEquals(
            [
                'class' => 'stdClass',
                'expression' => 'input',
                'collection' => [
                    [
                        'field' => '[foo]',
                        'copy' => '[foo]',
                    ],
                ],
            ],
            $processor->processConfiguration($configuration, [
                [
                    'class' => \stdClass::class,
                    'expression' => 'input',
                    'collection' => [
                        [
                            'field' => '[foo]',
                            'copy' => '[foo]',
                        ],
                    ],
                ],
            ])
        );
    }

    public function testCollectionWithExpressionField(): void
    {
        $processor = new Processor();
        $configuration = new FastMap\Configuration();

        $this->assertEquals(
            [
                'class' => 'stdClass',
                'expression' => 'input',
                'collection' => [
                    [
                        'field' => '[foo]',
                        'expression' => 'input["foo"]',
                    ],
                ],
            ],
            $processor->processConfiguration($configuration, [
                [
                    'class' => \stdClass::class,
                    'expression' => 'input',
                    'collection' => [
                        [
                            'field' => '[foo]',
                            'expression' => 'input["foo"]',
                        ],
                    ],
                ],
            ])
        );
    }

    public function testCollectionWithConstantField(): void
    {
        $processor = new Processor();
        $configuration = new FastMap\Configuration();

        $this->assertEquals(
            [
                'class' => 'stdClass',
                'expression' => 'input',
                'collection' => [
                    [
                        'field' => '[foo]',
                        'constant' => 'Lorem ipsum dolor sit amet, consectetur adipiscing elit.',
                    ],
                ],
            ],
            $processor->processConfiguration($configuration, [
                [
                    'class' => \stdClass::class,
                    'expression' => 'input',
                    'collection' => [
                        [
                            'field' => '[foo]',
                            'constant' => 'Lorem ipsum dolor sit amet, consectetur adipiscing elit.',
                        ],
                    ],
                ],
            ])
        );
    }

    public function testCollectionWithMapField(): void
    {
        $processor = new Processor();
        $configuration = new FastMap\Configuration();

        $this->assertEquals(
            [
                'class' => 'stdClass',
                'expression' => 'input',
                'collection' => [
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
            ],
            $processor->processConfiguration($configuration, [
                [
                    'class' => \stdClass::class,
                    'expression' => 'input',
                    'collection' => [
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
                ],
            ])
        );
    }

    public function testCollectionWithListField(): void
    {
        $processor = new Processor();
        $configuration = new FastMap\Configuration();

        $this->assertEquals(
            [
                'class' => 'stdClass',
                'expression' => 'input',
                'collection' => [
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
            ],
            $processor->processConfiguration($configuration, [
                [
                    'class' => \stdClass::class,
                    'expression' => 'input',
                    'collection' => [
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
                ],
            ])
        );
    }

    public function testCollectionWithListFieldWithoutExpression(): void
    {
        $processor = new Processor();
        $configuration = new FastMap\Configuration();

        $this->expectException(InvalidConfigurationException::class);
        $this->expectExceptionMessage('Your configuration should contain the "expression" field if the "list" field is present.');

        $processor->processConfiguration($configuration, [
            [
                'class' => \stdClass::class,
                'expression' => 'input',
                'collection' => [
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
            ],
        ]);
    }

    public function testCollectionWithObjectField(): void
    {
        $processor = new Processor();
        $configuration = new FastMap\Configuration();

        $this->assertEquals(
            [
                'class' => 'stdClass',
                'expression' => 'input',
                'collection' => [
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
            ],
            $processor->processConfiguration($configuration, [
                [
                    'class' => \stdClass::class,
                    'expression' => 'input',
                    'collection' => [
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
                ],
            ])
        );
    }

    public function testCollectionWithObjectFieldWithoutClass(): void
    {
        $processor = new Processor();
        $configuration = new FastMap\Configuration();

        $this->expectException(InvalidConfigurationException::class);
        $this->expectExceptionMessage('Your configuration should contain the "class" field if the "object" field is present.');

        $processor->processConfiguration($configuration, [
            [
                'class' => \stdClass::class,
                'expression' => 'input',
                'collection' => [
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
            ],
        ]);
    }

    public function testCollectionWithObjectFieldWithoutExpression(): void
    {
        $processor = new Processor();
        $configuration = new FastMap\Configuration();

        $this->expectException(InvalidConfigurationException::class);
        $this->expectExceptionMessage('Your configuration should contain the "expression" field if the "object" field is present.');

        $processor->processConfiguration($configuration, [
            [
                'class' => \stdClass::class,
                'expression' => 'input',
                'collection' => [
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
            ],
        ]);
    }

    public function testCollectionWithCollectionField(): void
    {
        $processor = new Processor();
        $configuration = new FastMap\Configuration();

        $this->assertEquals(
            [
                'class' => 'stdClass',
                'expression' => 'input',
                'collection' => [
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
            ],
            $processor->processConfiguration($configuration, [
                [
                    'class' => \stdClass::class,
                    'expression' => 'input',
                    'collection' => [
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
                ],
            ])
        );
    }

    public function testCollectionWithCollectionFieldWithoutClass(): void
    {
        $processor = new Processor();
        $configuration = new FastMap\Configuration();

        $this->expectException(InvalidConfigurationException::class);
        $this->expectExceptionMessage('Your configuration should contain the "class" field if the "collection" field is present.');

        $processor->processConfiguration($configuration, [
            [
                'class' => \stdClass::class,
                'expression' => 'input',
                'collection' => [
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
            ],
        ]);
    }

    public function testCollectionWithCollectionFieldWithoutExpression(): void
    {
        $processor = new Processor();
        $configuration = new FastMap\Configuration();

        $this->expectException(InvalidConfigurationException::class);
        $this->expectExceptionMessage('Your configuration should contain the "expression" field if the "collection" field is present.');

        $processor->processConfiguration($configuration, [
            [
                'class' => \stdClass::class,
                'expression' => 'input',
                'collection' => [
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
            ],
        ]);
    }
}
