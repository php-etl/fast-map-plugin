<?php

declare(strict_types=1);

namespace Kiboko\Plugin\FastMap\Builder;

use Kiboko\Contract\Configurator\StepBuilderInterface;
use Kiboko\Contract\Mapping\CompiledMapperInterface;
use PhpParser\Builder;
use PhpParser\Node;

final readonly class Transformer implements StepBuilderInterface
{
    public function __construct(private Builder|Node $mapper)
    {
    }

    public function withLogger(Node\Expr $logger): self
    {
        return $this;
    }

    public function withRejection(Node\Expr $rejection): self
    {
        return $this;
    }

    public function withState(Node\Expr $state): self
    {
        return $this;
    }

    public function getNode(): Node
    {
        return new Node\Expr\New_(
            class: new Node\Stmt\Class_(
                name: null,
                subNodes: [
                    'implements' => [
                        new Node\Name\FullyQualified(\Kiboko\Contract\Pipeline\TransformerInterface::class),
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
                                            expr: new Node\Expr\Yield_(
                                                new Node\Expr\New_(
                                                    class: new Node\Name\FullyQualified(
                                                        \Kiboko\Component\Bucket\AcceptanceResultBucket::class
                                                    ),
                                                    args: [
                                                        new Node\Arg(
                                                            new Node\Expr\Variable('line'),
                                                        ),
                                                    ],
                                                )
                                            )
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
                                                            ),
                                                        ]
                                                    ),
                                                ),
                                            ),
                                        ],
                                    ),
                                    new Node\Stmt\Expression(
                                        new Node\Expr\Yield_(
                                            new Node\Expr\New_(
                                                class: new Node\Name\FullyQualified(
                                                    \Kiboko\Component\Bucket\AcceptanceResultBucket::class,
                                                ),
                                                args: [
                                                    new Node\Arg(
                                                        new Node\Expr\Variable('line'),
                                                    ),
                                                ],
                                            ),
                                        )
                                    ),
                                ],
                                'returnType' => new Node\Name\FullyQualified(\Generator::class),
                            ],
                        ),
                    ],
                ],
            ),
            args: [
                new Node\Arg(
                    $this->mapper instanceof Builder ? $this->mapper->getNode() : $this->mapper,
                ),
            ],
        );
    }
}
