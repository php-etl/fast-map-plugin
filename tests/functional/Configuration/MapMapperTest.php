<?php

declare(strict_types=1);

namespace functional\Kiboko\Plugin\FastMap\Configuration;

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
final class MapMapperTest extends TestCase
{
    #[\PHPUnit\Framework\Attributes\Test]
    public function empty(): void
    {
        $processor = new Processor();
        $configuration = new FastMap\Configuration\MapMapper();

        $this->assertEmpty(
            $processor->processConfiguration($configuration, [
                [],
            ])
        );
    }

    #[\PHPUnit\Framework\Attributes\Test]
    public function withCopyField(): void
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

    #[\PHPUnit\Framework\Attributes\Test]
    public function withExpressionField(): void
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

    #[\PHPUnit\Framework\Attributes\Test]
    public function withConstantField(): void
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

    #[\PHPUnit\Framework\Attributes\Test]
    public function withMapField(): void
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

    #[\PHPUnit\Framework\Attributes\Test]
    public function withListField(): void
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

    #[\PHPUnit\Framework\Attributes\Test]
    public function withListFieldWithoutExpression(): void
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

    #[\PHPUnit\Framework\Attributes\Test]
    public function withObjectField(): void
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

    #[\PHPUnit\Framework\Attributes\Test]
    public function withObjectFieldWithoutClass(): void
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

    #[\PHPUnit\Framework\Attributes\Test]
    public function withObjectFieldWithoutExpression(): void
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

    #[\PHPUnit\Framework\Attributes\Test]
    public function withCollectionField(): void
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

    #[\PHPUnit\Framework\Attributes\Test]
    public function withCollectionFieldWithoutClass(): void
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

    #[\PHPUnit\Framework\Attributes\Test]
    public function withCollectionFieldWithoutExpression(): void
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
