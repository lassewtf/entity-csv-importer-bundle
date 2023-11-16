<?php

namespace Nano\EntityCsvImporterBundle\Types;

use DateTimeInterface;
use Exception;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Uid\Uuid;

class DateCsvValueType extends AbstractCsvValueType
{
    const NAME = 'int';

    protected function defaultOptions(): array
    {
        return [
            'format' => DateTimeInterface::ATOM,
        ];
    }

    public function processValue($value, array $options = [], array $services = [])
    {
        $options = $this->resolveOptions($options);

        $dateTime = \DateTimeImmutable::createFromFormat($options['format'], $value);
        if (!$dateTime) {
            throw new Exception('cant convert date time. format: ' . $options['format'] . ' value: ' . $value);
        }

        return $dateTime;
    }
}