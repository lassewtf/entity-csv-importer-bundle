<?php

namespace Nano\EntityCsvImporterBundle\Types;

use Symfony\Component\Uid\Uuid;

class IntCsvValueType extends AbstractCsvValueType
{
    const NAME = 'int';
    public function processValue($value, array $options = [], array $services = [])
    {
        $int = intval($value);

        return $int;
    }
}