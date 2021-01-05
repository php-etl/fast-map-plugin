<?php declare(strict_types=1);

namespace Kiboko\Component\ETL\Flow\FastMap\Builder;

use Kiboko\Component\ETL\Config\ObjectBuilder;
use Kiboko\Component\ETL\FastMap\Contracts\CompilableInterface;
use Kiboko\Component\ETL\FastMap\Contracts\CompiledMapperInterface;
use Kiboko\Contract\ETL\Configurator\InvalidConfigurationException;
use Kiboko\Contract\ETL\Pipeline\TransformerInterface;
use PhpParser\Builder;
use PhpParser\Node;

final class ObjectMapper implements Builder
{
    private ObjectBuilder $mapper;

    public function __construct()
    {
        $this->mapper = new ObjectBuilder();
    }

    public function getNode(): Node
    {
        $mapper = $this->mapper->getMapper();

        if (!$mapper instanceof CompilableInterface) {
            throw new InvalidConfigurationException('The provided mapper could not be compiled.');
        }

        return new Node\Expr\New_(
            class: new Node\Stmt\Class_(
                name: null,
                subNodes: [
                    'implements' => [
                        new Node\Name\FullyQualified(TransformerInterface::class),
                    ],
                    'stmts' => [
                        new Node\Stmt\ClassMethod(
                            name: new Node\Identifier('__construct'),
                            subNodes: [
                                'flags' => Node\Stmt\Class_::MODIFIER_PUBLIC,
                                'params' => [
                                    new Node\Param(
                                        var: new Node\Expr\Variable(
                                            name: 'mapper',
                                        ),
                                        type: new Node\Name\FullyQualified(
                                            CompiledMapperInterface::class,
                                        ),
                                        flags: Node\Stmt\Class_::MODIFIER_PRIVATE
                                    ),
                                ],
                            ],
                        ),
                        new Node\Stmt\ClassMethod(
                            name: new Node\Identifier('transform'),
                            subNodes: [
                                'flags' => Node\Stmt\Class_::MODIFIER_PUBLIC,
                                'stmts' => [
                                    new Node\Stmt\Expression(
                                        new Node\Expr\Assign(
                                            var: new Node\Expr\Variable('line'),
                                            expr: new Node\Expr\Yield_(null)
                                        ),
                                    ),
                                    new Node\Stmt\Do_(
                                        cond: new Node\Expr\Assign(
                                            var: new Node\Expr\Variable('line'),
                                            expr: new Node\Expr\Yield_(new Node\Expr\Variable('line'))
                                        ),
                                        stmts: [
                                            new Node\Stmt\Expression(
                                                new Node\Expr\Assign(
                                                    var: new Node\Expr\Variable('line'),
                                                    expr: new Node\Expr\FuncCall(
                                                        name: new Node\Expr\PropertyFetch(
                                                            var: new Node\Expr\Variable('this'),
                                                            name: new Node\Identifier('mapper'),
                                                        ),
                                                        args: [
                                                            new Node\Arg(
                                                                value: new Node\Expr\Variable('line')
                                                            )
                                                        ]
                                                    ),
                                                ),
                                            ),
                                        ],
                                    ),
                                    new Node\Stmt\Expression(
                                        new Node\Expr\Yield_(new Node\Expr\Variable('line'))
                                    ),
                                ],
                                'returnType' => new Node\Name\FullyQualified(\Generator::class),
                                'params' => [
                                    new Node\Param(
                                        new Node\Expr\Variable(
                                            name: 'input'
                                        ),
                                    ),
                                    new Node\Param(
                                        var: new Node\Expr\Variable(
                                            name: 'output',
                                        ),
                                        default: new Node\Expr\ConstFetch(
                                            name: new Node\Name(name: 'null'),
                                        ),
                                    ),
                                ],
                            ],
                        ),
                    ],
                ],
            ),
            args: [
                new Node\Arg(
                    new Node\Expr\New_(
                        new Node\Stmt\Class_(
                            name: null,
                            subNodes: [
                                'stmts' => [
                                    new Node\Stmt\ClassMethod(
                                        name: new Node\Identifier('transform'),
                                        subNodes: [
                                            'flags' => Node\Stmt\Class_::MODIFIER_PUBLIC,
                                            'stmts' => $mapper->compile(new Node\Expr\Variable('output')),
                                            'returnType' => new Node\Name\FullyQualified(\Generator::class),
                                            'params' => [
                                                new Node\Param(
                                                    new Node\Expr\Variable(
                                                        name: 'input'
                                                    ),
                                                ),
                                                new Node\Param(
                                                    var: new Node\Expr\Variable(
                                                        name: 'output',
                                                    ),
                                                    default: new Node\Expr\ConstFetch(
                                                        name: new Node\Name(name: 'null'),
                                                    ),
                                                ),
                                            ],
                                        ],
                                    ),
                                ],
                            ],
                        ),
                    ),
                ),
            ],
        );
    }

    public function withFieldCopy(string $destination, string $source): self
    {
        $this->mapper->children()->copy($destination, $source);

        return $this;
    }
}
