<?php declare(strict_types=1);

namespace functional\Kiboko\Component\ETL\Flow\FastMap;

use Kiboko\Component\ETL\Flow\FastMap;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Config\Definition\Exception\InvalidConfigurationException;
use Symfony\Component\Config\Definition\Processor;

final class ConfigurationTest extends TestCase
{
    public function testEmpty()
    {
        $processor = new Processor();
        $configuration = new FastMap\Configuration();

        $this->assertEmpty(
            $processor->processConfiguration($configuration, [
                []
            ])
        );
    }

    public function testWithNoFields()
    {
        $processor = new Processor();
        $configuration = new FastMap\Configuration();

        $this->assertEmpty(
            $processor->processConfiguration($configuration, [
                [
                    'map' => [],
                ]
            ])
        );
    }

    public function testWithCompetingFields()
    {
        $processor = new Processor();
        $configuration = new FastMap\Configuration();

        $this->expectException(InvalidConfigurationException::class);
        $this->expectExceptionMessage('Your configuration should either contain the "map" or the "object" key, not both.');

        $processor->processConfiguration($configuration, [
            [
                'map' => [
                    [
                        'field' => '[foo]',
                        'copy' => '[foo]',
                    ]
                ],
            ],
            [
                'object' => [
                    [
                        'field' => '[foo]',
                        'copy' => '[foo]',
                    ]
                ],
            ]
        ]);
    }

    public function testMapWithCopyField()
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

    public function testMapWithExpressionField()
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

    public function testMapWithConstantField()
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

    public function testListWithCopyField()
    {
        $processor = new Processor();
        $configuration = new FastMap\Configuration();

        $this->assertEquals(
            [
                'list' => [
                    [
                        'field' => '[foo]',
                        'copy' => '[foo]',
                    ],
                ],
            ],
            $processor->processConfiguration($configuration, [
                [
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

    public function testListWithExpressionField()
    {
        $processor = new Processor();
        $configuration = new FastMap\Configuration();

        $this->assertEquals(
            [
                'list' => [
                    [
                        'field' => '[foo]',
                        'expression' => 'input["foo"]',
                    ],
                ],
            ],
            $processor->processConfiguration($configuration, [
                [
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

    public function testListWithConstantField()
    {
        $processor = new Processor();
        $configuration = new FastMap\Configuration();

        $this->assertEquals(
            [
                'list' => [
                    [
                        'field' => '[foo]',
                        'constant' => 'Lorem ipsum dolor sit amet, consectetur adipiscing elit.',
                    ],
                ],
            ],
            $processor->processConfiguration($configuration, [
                [
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

    public function testObjectWithoutClass()
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

    public function testObjectWithoutExpression()
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

    public function testObjectWithCopyField()
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

    public function testObjectWithExpressionField()
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

    public function testObjectWithConstantField()
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
}
