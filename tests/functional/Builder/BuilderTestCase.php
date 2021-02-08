<?php declare(strict_types=1);

namespace functional\Kiboko\Plugin\FastMap\Builder;

use PhpParser\Builder as DefaultBuilder;
use PHPUnit\Framework\TestCase;
use Vfs\FileSystem;

abstract class BuilderTestCase extends TestCase
{
    private ?FileSystem $fs = null;

    protected function setUp(): void
    {
        $this->fs = FileSystem::factory('vfs://');
        $this->fs->mount();
    }

    protected function tearDown(): void
    {
        $this->fs->unmount();
        $this->fs = null;
    }

    protected function assertArrayMapsAs(array $expected, DefaultBuilder $builder, string $message = '')
    {
        static::assertThat(
            $builder,
            new ArrayMapsAs($expected),
            $message
        );
    }
}
