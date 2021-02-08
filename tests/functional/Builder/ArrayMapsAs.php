<?php declare(strict_types=1);

namespace functional\Kiboko\Plugin\FastMap\Builder;

use PhpParser\Builder;
use PhpParser\Node;
use PhpParser\PrettyPrinter;
use PHPUnit\Framework\Constraint\Constraint;
use function sprintf;

final class ArrayMapsAs extends Constraint
{
    public function __construct(
        private array $expected,
    ) {}

    public function toString(): string
    {
        return sprintf(
            'mapper returns %s',
            json_encode($this->expected)
        );
    }

    /**
     * @param Builder $other value or object to evaluate
     */
    protected function matches($other): bool
    {
        $printer = new PrettyPrinter\Standard();

        $filename = 'vfs://' . hash('sha512', random_bytes(512)) .'.php';

        file_put_contents($filename, $printer->prettyPrintFile([
            new Node\Stmt\Return_($other->getNode())
        ]));

        $mapper = include $filename;

        $result = [];
        foreach ($mapper->transform() as $line) {
            $result[] = $line;
        }

        return $result === $this->expected;
    }
}
