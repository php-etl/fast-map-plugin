<?php declare(strict_types=1);

namespace Kiboko\Plugin\FastMap\Builder;

use Kiboko\Component\FastMapConfig\ObjectBuilder;
use Kiboko\Component\FastMap\Contracts\CompiledMapperInterface;
use PhpParser\Builder;
use PhpParser\Node;

final class ObjectMapper implements Builder
{
    public function __construct(private ObjectBuilder $mapper)
    {
    }

    public function getNode(): Node
    {
        return new Node\Expr\New_(
            class: new Node\Stmt\Class_(
                name: null,
                subNodes: [
                    'implements' => [
                        new Node\Name\FullyQualified('Kiboko\\Contract\\Pipeline\\TransformerInterface'),
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
                                            'stmts' => $this->mapper->getMapper()->compile(new Node\Expr\Variable('output')),
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
}
