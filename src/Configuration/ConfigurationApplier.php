<?php declare(strict_types=1);

namespace Kiboko\Component\ETL\Flow\FastMap\Configuration;

use Kiboko\Component\ETL\Config\CompositeBuilderInterface;

final class ConfigurationApplier
{
    public function __invoke(CompositeBuilderInterface $mapper, iterable $fields): void
    {
        foreach ($fields as $field) {
            if (array_key_exists('copy', $field)) {
                $mapper->copy($field['field'], $field['copy']);
            } else if (array_key_exists('expression', $field)) {
                $mapper->expression($field['field'], $field['expression']);
            } else if (array_key_exists('constant', $field)) {
                $mapper->constant($field['field'], $field['constant']);
            } else if (array_key_exists('object', $field)) {
                $this(
                    $mapper->object($field['field'], $field['class'], $field['expression'])->children(),
                    $field['object'],
                );
            } else if (array_key_exists('map', $field)) {
                $this(
                    $mapper->map($field['field'], $field['expression'])->children(),
                    $field['map'],
                );
            } else if (array_key_exists('list', $field)) {
                $this(
                    $mapper->list($field['field'], $field['expression'])->children(),
                    $field['list'],
                );
            }
        }
    }
}
