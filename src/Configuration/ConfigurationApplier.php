<?php

declare(strict_types=1);

namespace Kiboko\Plugin\FastMap\Configuration;

use Kiboko\Component\FastMapConfig\CompositeBuilderInterface;
use Kiboko\Contract\Configurator\InvalidConfigurationException;

final readonly class ConfigurationApplier
{
    public function __construct(private array $additionalExpressionVariables = [])
    {
    }

    public function __invoke(CompositeBuilderInterface $mapper, iterable $fields): void
    {
        foreach ($fields as $field) {
            if (\array_key_exists('object', $field)) {
                $this(
                    $mapper->object($field['field'], $field['class'], $field['expression'])->children(),
                    $field['object'],
                );
            } elseif (\array_key_exists('collection', $field)) {
                $this(
                    $mapper->collection($field['field'], $field['class'], $field['expression'])->children(),
                    $field['collection'],
                );
            } elseif (\array_key_exists('map', $field)) {
                $this(
                    $mapper->map($field['field'], $field['expression'])->children(),
                    $field['map'],
                );
            } elseif (\array_key_exists('list', $field)) {
                $this(
                    $mapper->list($field['field'], $field['expression'])->children(),
                    $field['list'],
                );
            } elseif (\array_key_exists('copy', $field)) {
                $mapper->copy($field['field'], $field['copy']);
            } elseif (\array_key_exists('expression', $field)) { // Should be at the end in order to let the complex fields use the "expression" property
                $mapper->expression(
                    $field['field'],
                    $field['expression'],
                    array_merge([], $field['variables'] ?? [], $this->additionalExpressionVariables),
                );
            } elseif (\array_key_exists('constant', $field)) {
                $mapper->constant($field['field'], $field['constant']);
            } else {
                throw new InvalidConfigurationException(sprintf('No field type is suitable for the field %s, configuration was %s.', $field['field'], json_encode($field, \JSON_THROW_ON_ERROR)));
            }
        }
    }
}
