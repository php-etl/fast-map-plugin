<?php

declare(strict_types=1);

namespace Kiboko\Plugin\FastMap\Builder;

use PhpParser\Builder;
use PhpParser\Node;
use PhpParser\ParserFactory;
use Symfony\Component\ExpressionLanguage\ExpressionLanguage;

final class ConditionalMapper implements Builder
{
    private iterable $alternatives = [];

    public function __construct(private readonly ExpressionLanguage $interpreter)
    {
    }

    public function withAlternative(string $condition, Builder $builder): self
    {
        $this->alternatives[] = [$condition, $builder];

        return $this;
    }

    public function getNode(): Node
    {
        return $this->compileConditions($this->alternatives);
    }

    private function compileConditions(array $alternatives): Node
    {
        $parser = (new ParserFactory())->create(ParserFactory::PREFER_PHP7, null);

        /** @var Builder $builder */
        [$condition, $builder] = array_shift($alternatives);

        return new Node\Expr\New_(
            new Node\Stmt\Class_(
                name: null,
                subNodes: [
                    'implements' => [
                        new Node\Name\FullyQualified(\Kiboko\Contract\Mapping\CompiledMapperInterface::class),
                    ],
                    'stmts' => [
                        new Node\Stmt\Property(
                            flags: Node\Stmt\Class_::MODIFIER_PRIVATE,
                            props: [
                                new Node\Stmt\PropertyProperty(
                                    name: new Node\Name('mappers'),
                                ),
                            ],
                            type: new Node\Identifier('iterable')
                        ),
                        new Node\Stmt\ClassMethod(
                            name: '__construct',
                            subNodes: [
                                'flags' => Node\Stmt\Class_::MODIFIER_PUBLIC,
                                'stmts' => [
                                    new Node\Stmt\Expression(
                                        new Node\Expr\Assign(
                                            new Node\Expr\PropertyFetch(
                                                new Node\Expr\Variable('this'),
                                                new Node\Identifier('mappers'),
                                            ),
                                            new Node\Expr\Array_(
                                                items: [
                                                    new Node\Expr\ArrayItem(
                                                        $builder->getNode()
                                                    ),
                                                    ...array_map(
                                                        function ($alternative) {
                                                            [$condition, $builder] = $alternative;

                                                            return new Node\Expr\ArrayItem(
                                                                $builder->getNode()
                                                            );
                                                        },
                                                        $alternatives
                                                    ),
                                                ],
                                                attributes: [
                                                    'kind' => Node\Expr\Array_::KIND_SHORT,
                                                ],
                                            ),
                                        ),
                                    ),
                                ],
                            ],
                        ),
                        new Node\Stmt\ClassMethod(
                            name: '__invoke',
                            subNodes: [
                                'flags' => Node\Stmt\Class_::MODIFIER_PUBLIC,
                                'params' => [
                                    new Node\Param(
                                        var: new Node\Expr\Variable('input'),
                                    ),
                                    new Node\Param(
                                        var: new Node\Expr\Variable('output'),
                                        default: new Node\Expr\ConstFetch(
                                            new Node\Name('null')
                                        ),
                                    ),
                                ],
                                'stmts' => [
                                    new Node\Stmt\If_(
                                        cond: $parser->parse('<?php '.$this->interpreter->compile($condition, ['input', 'output']).';')[0]->expr ?? throw new \UnexpectedValueException('Expected parsing result to be an instance of Node\Expr.'),
                                        subNodes: [
                                            'stmts' => [
                                                new Node\Stmt\Return_(
                                                    new Node\Expr\FuncCall(
                                                        new Node\Expr\ArrayDimFetch(
                                                            var: new Node\Expr\PropertyFetch(
                                                                var: new Node\Expr\Variable('this'),
                                                                name: new Node\Identifier('mappers')
                                                            ),
                                                            dim: new Node\Scalar\LNumber(0),
                                                        ),
                                                        args: [
                                                            new Node\Arg(
                                                                new Node\Expr\Variable('input'),
                                                            ),
                                                            new Node\Arg(
                                                                new Node\Expr\Variable('output'),
                                                            ),
                                                        ],
                                                    ),
                                                ),
                                            ],
                                            'elseifs' => array_map(
                                                function ($alternative, $index) use ($parser) {
                                                    [$condition, $repository] = $alternative;

                                                    return new Node\Stmt\ElseIf_(
                                                        cond: $parser->parse('<?php '.$this->interpreter->compile($condition, ['input', 'output']).';')[0]->expr ?? throw new \UnexpectedValueException('Expected parsing result to be an instance of Node\Expr.'),
                                                        stmts: [
                                                            new Node\Stmt\Return_(
                                                                new Node\Expr\FuncCall(
                                                                    new Node\Expr\ArrayDimFetch(
                                                                        var: new Node\Expr\PropertyFetch(
                                                                            var: new Node\Expr\Variable('this'),
                                                                            name: new Node\Identifier('mappers')
                                                                        ),
                                                                        dim: new Node\Scalar\LNumber($index + 1),
                                                                    ),
                                                                    args: [
                                                                        new Node\Arg(
                                                                            new Node\Expr\Variable('input'),
                                                                        ),
                                                                        new Node\Arg(
                                                                            new Node\Expr\Variable('output'),
                                                                        ),
                                                                    ],
                                                                ),
                                                            ),
                                                        ],
                                                    );
                                                },
                                                $alternatives,
                                                array_keys($alternatives),
                                            ),
                                            'else' => new Node\Stmt\Else_(
                                                stmts: [
                                                    new Node\Stmt\Return_(
                                                        new Node\Expr\Variable('input'),
                                                    ),
                                                ]
                                            ),
                                        ],
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
