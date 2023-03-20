<?php

declare(strict_types=1);

namespace functional\Kiboko\Plugin\FastMap;

use Kiboko\Plugin\FastMap;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Config\Definition\Exception\InvalidConfigurationException;
use Symfony\Component\Config\Definition\Processor;

/**
 * @internal
 */
#[\PHPUnit\Framework\Attributes\CoversNothing]
/**
 * @internal
 *
 * @coversNothing
 */
final class ConfigurationTest extends TestCase
{
    #[\PHPUnit\Framework\Attributes\Test]
    public function empty(): void
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

    #[\PHPUnit\Framework\Attributes\Test]
    public function withNoFields(): void
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

    #[\PHPUnit\Framework\Attributes\Test]
    public function withCompetingFields(): void
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

    #[\PHPUnit\Framework\Attributes\Test]
    public function mapWithCopyField(): void
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

    #[\PHPUnit\Framework\Attributes\Test]
    public function mapWithExpressionField(): void
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

    #[\PHPUnit\Framework\Attributes\Test]
    public function mapWithConstantField(): void
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

    #[\PHPUnit\Framework\Attributes\Test]
    public function mapWithMapField(): void
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

    #[\PHPUnit\Framework\Attributes\Test]
    public function mapWithListField(): void
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

    #[\PHPUnit\Framework\Attributes\Test]
    public function mapWithListFieldWithoutExpression(): void
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

    #[\PHPUnit\Framework\Attributes\Test]
    public function mapWithObjectField(): void
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

    #[\PHPUnit\Framework\Attributes\Test]
    public function mapWithObjectFieldWithoutClass(): void
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

    #[\PHPUnit\Framework\Attributes\Test]
    public function mapWithObjectFieldWithoutExpression(): void
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

    #[\PHPUnit\Framework\Attributes\Test]
    public function mapWithCollectionField(): void
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

    #[\PHPUnit\Framework\Attributes\Test]
    public function mapWithCollectionFieldWithoutClass(): void
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

    #[\PHPUnit\Framework\Attributes\Test]
    public function mapWithCollectionFieldWithoutExpression(): void
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

    #[\PHPUnit\Framework\Attributes\Test]
    public function listWithCopyField(): void
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

    #[\PHPUnit\Framework\Attributes\Test]
    public function listWithExpressionField(): void
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

    #[\PHPUnit\Framework\Attributes\Test]
    public function listWithConstantField(): void
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

    #[\PHPUnit\Framework\Attributes\Test]
    public function listWithMapField(): void
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

    #[\PHPUnit\Framework\Attributes\Test]
    public function listWithListField(): void
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

    #[\PHPUnit\Framework\Attributes\Test]
    public function listWithListFieldWithoutExpression(): void
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

    #[\PHPUnit\Framework\Attributes\Test]
    public function listWithObjectField(): void
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

    #[\PHPUnit\Framework\Attributes\Test]
    public function listWithObjectFieldWithoutClass(): void
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

    #[\PHPUnit\Framework\Attributes\Test]
    public function listWithObjectFieldWithoutExpression(): void
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

    #[\PHPUnit\Framework\Attributes\Test]
    public function listWithCollectionField(): void
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

    #[\PHPUnit\Framework\Attributes\Test]
    public function listWithCollectionFieldWithoutClass(): void
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

    #[\PHPUnit\Framework\Attributes\Test]
    public function listWithCollectionFieldWithoutExpression(): void
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

    #[\PHPUnit\Framework\Attributes\Test]
    public function objectWithoutClass(): void
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

    #[\PHPUnit\Framework\Attributes\Test]
    public function objectWithoutExpression(): void
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

    #[\PHPUnit\Framework\Attributes\Test]
    public function objectWithCopyField(): void
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

    #[\PHPUnit\Framework\Attributes\Test]
    public function objectWithExpressionField(): void
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

    #[\PHPUnit\Framework\Attributes\Test]
    public function objectWithConstantField(): void
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

    #[\PHPUnit\Framework\Attributes\Test]
    public function objectWithMapField(): void
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

    #[\PHPUnit\Framework\Attributes\Test]
    public function objectWithListField(): void
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

    #[\PHPUnit\Framework\Attributes\Test]
    public function objectWithListFieldWithoutExpression(): void
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

    #[\PHPUnit\Framework\Attributes\Test]
    public function objectWithObjectField(): void
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

    #[\PHPUnit\Framework\Attributes\Test]
    public function objectWithObjectFieldWithoutClass(): void
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

    #[\PHPUnit\Framework\Attributes\Test]
    public function objectWithObjectFieldWithoutExpression(): void
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

    #[\PHPUnit\Framework\Attributes\Test]
    public function objectWithCollectionField(): void
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

    #[\PHPUnit\Framework\Attributes\Test]
    public function objectWithCollectionFieldWithoutClass(): void
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

    #[\PHPUnit\Framework\Attributes\Test]
    public function objectWithCollectionFieldWithoutExpression(): void
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

    #[\PHPUnit\Framework\Attributes\Test]
    public function collectionWithoutClass(): void
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

    #[\PHPUnit\Framework\Attributes\Test]
    public function collectionWithoutExpression(): void
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

    #[\PHPUnit\Framework\Attributes\Test]
    public function collectionWithCopyField(): void
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

    #[\PHPUnit\Framework\Attributes\Test]
    public function collectionWithExpressionField(): void
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

    #[\PHPUnit\Framework\Attributes\Test]
    public function collectionWithConstantField(): void
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

    #[\PHPUnit\Framework\Attributes\Test]
    public function collectionWithMapField(): void
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

    #[\PHPUnit\Framework\Attributes\Test]
    public function collectionWithListField(): void
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

    #[\PHPUnit\Framework\Attributes\Test]
    public function collectionWithListFieldWithoutExpression(): void
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

    #[\PHPUnit\Framework\Attributes\Test]
    public function collectionWithObjectField(): void
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

    #[\PHPUnit\Framework\Attributes\Test]
    public function collectionWithObjectFieldWithoutClass(): void
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

    #[\PHPUnit\Framework\Attributes\Test]
    public function collectionWithObjectFieldWithoutExpression(): void
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

    #[\PHPUnit\Framework\Attributes\Test]
    public function collectionWithCollectionField(): void
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

    #[\PHPUnit\Framework\Attributes\Test]
    public function collectionWithCollectionFieldWithoutClass(): void
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

    #[\PHPUnit\Framework\Attributes\Test]
    public function collectionWithCollectionFieldWithoutExpression(): void
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
