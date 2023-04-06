<?php

declare(strict_types=1);

namespace functional\Kiboko\Plugin\FastMap\Factory;

use Kiboko\Contract\Configurator\InvalidConfigurationException;
use Kiboko\Plugin\FastMap;
use PHPUnit\Framework\TestCase;
use Symfony\Component\ExpressionLanguage\ExpressionLanguage;

final class ObjectMapperTest extends TestCase
{
    public static function configProvider()
    {
        yield [
            'expected' => [
                [
                    'field' => '[foo]',
                    'expression' => 'input["foo"]',
                ],
            ],
            'actual' => [
                'object' => [
                    [
                        'field' => '[foo]',
                        'expression' => 'input["foo"]',
                    ],
                ],
            ],
        ];
    }

    #[\PHPUnit\Framework\Attributes\DataProvider('configProvider')]
    public function testWithConfiguration(mixed $expected, mixed $actual): void
    {
        $factory = new FastMap\Factory\ObjectMapper(new ExpressionLanguage());

        $this->assertTrue($factory->validate($actual));

        $this->assertEquals(
            new FastMap\Configuration\ObjectMapper(),
            $factory->configuration()
        );

        $this->assertEquals(
            $expected,
            $factory->normalize($actual)
        );
    }

    public function testFailToNormalize(): void
    {
        $this->expectException(InvalidConfigurationException::class);
        $this->expectExceptionCode(0);
        $this->expectExceptionMessage('Invalid type for path "object". Expected "array", but got "string"');

        $factory = new FastMap\Factory\ObjectMapper(new ExpressionLanguage());
        $factory->normalize([
            'object' => '',
        ]);
    }

    public function testFailToValidate(): void
    {
        $factory = new FastMap\Factory\ObjectMapper(new ExpressionLanguage());
        $this->assertFalse($factory->validate([]));
    }
}
