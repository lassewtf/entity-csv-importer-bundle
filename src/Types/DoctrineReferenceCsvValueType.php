<?php

namespace Nano\EntityCsvImporterBundle\Types;

class DoctrineReferenceCsvValueType extends AbstractCsvValueType
{
    const NAME = 'reference';

    protected function requiredOptions(): array
    {
        return [
            'entity',
            'property'
        ];
    }
    public  function processValue($value, array $options = [], array $services = [])
    {

        $options = $this->resolveOptions($options);
        $em = $services['em'];
        $repository = $em->getRepository($options['entity']);
        $resultValue = $repository->findOneBy([$options['property'] => $value]);
        if (!$resultValue) {
            throw new \Exception('cant process value. entity not found');
        }
        return $resultValue;
    }
}