<?php declare(strict_types=1);

namespace functional\Kiboko\Plugin\FastMap\Builder;

use functional\Kiboko\Plugin\FastMap\DTO\Customer;
use Kiboko\Component\FastMapConfig\ArrayBuilder;
use Kiboko\Component\PHPUnitExtension\Assert\TransformerBuilderAssertTrait;
use Kiboko\Plugin\FastMap\Builder\ArrayMapper;
use Kiboko\Plugin\FastMap\Builder\Transformer;
use Kiboko\Plugin\FastMap\Configuration\ConfigurationApplier;
use PHPUnit\Framework\TestCase;
use Vfs\FileSystem;

abstract class ArrayMapperTest extends TestCase
{
    use TransformerBuilderAssertTrait;

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

    public function testSuccessfulArrayMapping()
    {
        $mapper = new ArrayBuilder();

        $builder = new Transformer(
            new ArrayMapper($mapper)
        );

        (new ConfigurationApplier())(
            $mapper->children(),
            [
                [
                    'field' => '[customer]',
                    'class' => 'functional\\Kiboko\\Plugin\\FastMap\\DTO\\Customer',
                    'expression' => 'input["key"]',
                    'object' => [
                        [
                            'field' => 'email',
                            'copy' => '[customer][email]',
                        ]
                    ]
                ]
            ]
        );

        $this->assertBuildsTransformerTransformsLike(
            [
                [
                    'customer' => [
                        'email' => 'myemail@gmail.com'
                    ]
                ]
            ],
        [
                (new Customer())->setEmail('myemail@gmail.com')
            ],
            $builder
        );
    }
}
