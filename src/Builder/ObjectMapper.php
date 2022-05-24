<?php

declare(strict_types=1);

namespace Kiboko\Plugin\FastMap\Builder;

use Kiboko\Component\FastMapConfig\ObjectBuilderInterface;
use PhpParser\Builder;
use PhpParser\Node;

final class ObjectMapper implements Builder
{
    public function __construct(private ObjectBuilderInterface $mapper)
    {
    }

    public function getNode(): Node
    {
        return new Node\Expr\New_(
            new Node\Stmt\Class_(
                name: null,
                subNodes: [
                    'implements' => [
                        new Node\Name\FullyQualified('Kiboko\\Contract\\Mapping\\CompiledMapperInterface'),
                    ],
                    'stmts' => [
                        new Node\Stmt\ClassMethod(
                            name: new Node\Identifier('__invoke'),
                            subNodes: [
                                'flags' => Node\Stmt\Class_::MODIFIER_PUBLIC,
                                'stmts' => [
                                    ...$this->mapper->getMapper()->compile(new Node\Expr\Variable('output')),
                                    new Node\Stmt\Return_(new Node\Expr\Variable('output')),
                                ],
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
        );
    }
}
