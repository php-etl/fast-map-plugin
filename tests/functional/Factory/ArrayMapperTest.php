<?php

declare(strict_types=1);

namespace functional\Kiboko\Plugin\FastMap\Factory;

use Kiboko\Contract\Configurator\InvalidConfigurationException;
use Kiboko\Plugin\FastMap;
use PHPUnit\Framework\TestCase;
use Symfony\Component\ExpressionLanguage\ExpressionLanguage;

/**
 * @internal
 */
#[\PHPUnit\Framework\Attributes\CoversNothing]
/**
 * @internal
 *
 * @coversNothing
 */
final class ArrayMapperTest extends TestCase
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
                'map' => [
                    [
                        'field' => '[foo]',
                        'expression' => 'input["foo"]',
                    ],
                ],
            ],
        ];
    }

    #[\PHPUnit\Framework\Attributes\DataProvider('configProvider')]
    #[\PHPUnit\Framework\Attributes\Test]
    public function withConfiguration(mixed $expected, mixed $actual): void
    {
        $factory = new FastMap\Factory\ArrayMapper(new ExpressionLanguage());

        $this->assertTrue($factory->validate($actual));

        $this->assertEquals(
            new FastMap\Configuration\MapMapper(),
            $factory->configuration()
        );

        $this->assertEquals(
            $expected,
            $factory->normalize($actual)
        );
    }

    #[\PHPUnit\Framework\Attributes\Test]
    public function failToNormalize(): void
    {
        $this->expectException(InvalidConfigurationException::class);
        $this->expectExceptionCode(0);
        $this->expectExceptionMessage('Invalid type for path "map". Expected "array", but got "string"');

        $factory = new FastMap\Factory\ArrayMapper(new ExpressionLanguage());
        $factory->normalize([
            'map' => '',
        ]);
    }

    #[\PHPUnit\Framework\Attributes\Test]
    public function failToValidate(): void
    {
        $factory = new FastMap\Factory\ArrayMapper(new ExpressionLanguage());
        $this->assertFalse($factory->validate([]));
    }
}
