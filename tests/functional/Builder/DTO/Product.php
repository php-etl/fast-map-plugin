<?php

declare(strict_types=1);

namespace functional\Kiboko\Plugin\FastMap\Builder\DTO;

class Product
{
    public function __construct(
        public int $id,
        public ?bool $enabled = false,
    ) {
    }
}
