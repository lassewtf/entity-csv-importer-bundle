<?php

namespace Nano\EntityCsvImporterBundle\Types;

use Symfony\Component\OptionsResolver\OptionsResolver;

abstract class AbstractCsvValueType
{
    const NAME = 'null';

    protected function defaultOptions(): array
    {
        return [];
    }

    protected function requiredOptions(): array
    {
        return [];
    }

    protected function resolveOptions(array $options): array
    {
        $resolver = new OptionsResolver();
        $resolver->setRequired($this->requiredOptions());
        $resolver->setDefaults($this->defaultOptions());
        return $resolver->resolve($options);
    }

    public function processValue($value, array $options = [], array $services = [])
    {
        $options = $this->resolveOptions($options);
        return $value;
    }
}