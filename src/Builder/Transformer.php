<?php

declare(strict_types=1);

namespace Kiboko\Plugin\FastMap\Builder;

use Kiboko\Contract\Configurator\StepBuilderInterface;
use Kiboko\Contract\Mapping\CompiledMapperInterface;
use PhpParser\Builder;
use PhpParser\Node;

final class Transformer implements StepBuilderInterface
{
    private ?Node\Expr $logger = null;

    public function __construct(private readonly Builder|Node $mapper)
    {
    }

    public function withLogger(Node\Expr $logger): self
    {
        $this->logger = $logger;

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
                                    new Node\Param(
                                        var: new Node\Expr\Variable(
                                            name: 'logger'
                                        ),
                                        type: new Node\Name\FullyQualified(
                                            name: \Psr\Log\LoggerInterface::class
                                        ),
                                        flags: Node\Stmt\Class_::MODIFIER_PUBLIC,
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
                                            var: new Node\Expr\Variable('input'),
                                            expr: new Node\Expr\Yield_(null)
                                        ),
                                    ),
                                    new Node\Stmt\While_(
                                        cond: new Node\Expr\BinaryOp\NotIdentical(
                                            left: new Node\Expr\Variable('input'),
                                            right: new Node\Expr\ConstFetch(
                                                new Node\Name('null')
                                            )
                                        ),
                                        stmts: [
                                            new Node\Stmt\TryCatch(
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
                                                                        value: new Node\Expr\Variable('input')
                                                                    ),
                                                                    new Node\Arg(
                                                                        value: new Node\Expr\Variable('input')
                                                                    ),
                                                                ]
                                                            ),
                                                        ),
                                                    ),
                                                ],
                                                catches: [
                                                    new Node\Stmt\Catch_(
                                                        types: [
                                                            new Node\Name\FullyQualified(\Kiboko\Contract\Pipeline\RejectedItemException::class),
                                                        ],
                                                        var: new Node\Expr\Variable('exception'),
                                                        stmts: [
                                                            new Node\Stmt\Expression(
                                                                new Node\Expr\MethodCall(
                                                                    var: new Node\Expr\PropertyFetch(
                                                                        var: new Node\Expr\Variable('this'),
                                                                        name: new Node\Identifier('logger'),
                                                                    ),
                                                                    name: new Node\Name('error'),
                                                                    args: [
                                                                        new Node\Arg(
                                                                            new Node\Expr\MethodCall(
                                                                                var: new Node\Expr\Variable('exception'),
                                                                                name: new Node\Identifier('getMessage')
                                                                            )
                                                                        ),
                                                                        new Node\Expr\Array_([
                                                                            new Node\Expr\ArrayItem(
                                                                                value: new Node\Expr\Variable('input'),
                                                                                key: new Node\Scalar\String_('input')
                                                                            ),
                                                                        ]),
                                                                    ]
                                                                )
                                                            ),
                                                            new Node\Stmt\Expression(
                                                                new Node\Expr\Assign(
                                                                    var: new Node\Expr\Variable('input'),
                                                                    expr: new Node\Expr\Yield_(
                                                                        new Node\Expr\New_(
                                                                            class: new Node\Name\FullyQualified(
                                                                                \Kiboko\Component\Bucket\RejectionResultBucket::class
                                                                            ),
                                                                            args: [
                                                                                new Node\Arg(
                                                                                    new Node\Expr\MethodCall(
                                                                                        new Node\Expr\Variable('exception'),
                                                                                        'getMessage'
                                                                                    ),
                                                                                ),
                                                                                new Node\Arg(
                                                                                    new Node\Expr\Variable('exception'),
                                                                                ),
                                                                                new Node\Arg(
                                                                                    new Node\Expr\Variable('input'),
                                                                                ),
                                                                            ],
                                                                        )
                                                                    )
                                                                ),
                                                            ),
                                                            new Node\Stmt\Continue_(),
                                                        ]
                                                    ),
                                                ]
                                            ),
                                            new Node\Stmt\Expression(
                                                new Node\Expr\Assign(
                                                    var: new Node\Expr\Variable('input'),
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
                                                )
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
                                                        new Node\Expr\Variable('input'),
                                                    ),
                                                ],
                                            ),
                                        )
                                    ),
                                ],
                                'returnType' => new Node\Name\FullyQualified('Generator'),
                            ],
                        ),
                    ],
                ],
            ),
            args: [
                new Node\Arg(
                    $this->mapper instanceof Builder ? $this->mapper->getNode() : $this->mapper,
                ),
                new Node\Arg(
                    $this->logger ?? new Node\Expr\New_(new Node\Name\FullyQualified(\Psr\Log\NullLogger::class))
                ),
            ],
        );
    }
}
