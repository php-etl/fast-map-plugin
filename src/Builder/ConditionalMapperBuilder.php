<?php declare(strict_types=1);

namespace Kiboko\Plugin\FastMap\Builder;

use Kiboko\Contract\Configurator\RepositoryInterface;
use PhpParser\Builder;
use PhpParser\Node;
use PhpParser\ParserFactory;
use Symfony\Component\ExpressionLanguage\ExpressionLanguage;

final class ConditionalMapperBuilder implements Builder
{
    private iterable $alternatives;

    public function __construct(private ExpressionLanguage $interpreter)
    {
        $this->alternatives = [];
    }

    public function withAlternative(string $condition, RepositoryInterface $repository): self
    {
        $this->alternatives[] = [$condition, $repository];

        return $this;
    }

    public function getNode(): Node
    {
        return $this->compileConditions($this->alternatives);
    }

    private function compileConditions(array $alternatives): Node
    {
        $parser = (new ParserFactory())->create(ParserFactory::PREFER_PHP7, null);

        /** @var RepositoryInterface $repository */
        [$condition, $repository] = array_shift($alternatives);

        return new Node\Expr\New_(
            new Node\Stmt\Class_(
                name: null,
                subNodes: [
                    'implements' => [
                        new Node\Name\FullyQualified('std'),
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
                                                        $repository->getBuilder()->getNode()
                                                    ),
                                                    ...array_map(
                                                        function ($alternative) use ($parser) {
                                                            [$condition, $repository] = $alternative;

                                                            return new Node\Expr\ArrayItem(
                                                                $repository->getBuilder()->getNode()
                                                            );
                                                        },
                                                        $alternatives
                                                    )
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
                            name: 'transform',
                            subNodes: [
                                'stmts' => [
                                    new Node\Stmt\If_(
                                        cond: $parser->parse('<?php ' . $this->interpreter->compile($condition, ['input', 'output']) . ';')[0]->expr,
                                        subNodes: array_filter([
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
                                                        cond: $parser->parse('<?php ' . $this->interpreter->compile($condition, ['input', 'output']) . ';')[0]->expr,
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
                                            )],
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
