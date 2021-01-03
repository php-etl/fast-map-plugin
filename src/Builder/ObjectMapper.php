<?php declare(strict_types=1);

namespace Kiboko\Component\ETL\Flow\FastMap\Builder;

use PhpParser\Builder;
use PhpParser\Node;

final class ObjectMapper implements Builder
{
    public function getNode(): Node
    {
        return new Node\Scalar\String_('object');
    }
}
