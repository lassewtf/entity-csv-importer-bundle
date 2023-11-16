<?php

namespace Nano\EntityCsvImporterBundle\Service;

use Doctrine\ORM\EntityManagerInterface;
use Nano\EntityCsvImporterBundle\Types\AbstractCsvValueType;
use Nano\EntityCsvImporterBundle\Types\DoctrineReferenceCsvValueType;
use Nano\EntityCsvImporterBundle\Types\RawCsvValueType;

class ValueProcessor
{
    const PROPERTY_MAPPING_TYPES = [
        DoctrineReferenceCsvValueType::class,
        RawCsvValueType::class
    ];

    private array $typeMapping = [];
    private EntityManagerInterface $em;

    public function __construct(EntityManagerInterface $em)
    {
        $this->em = $em;
        $this->buildMappingTypes();
    }


    private function buildMappingTypes(): void
    {
        foreach (self::PROPERTY_MAPPING_TYPES as $mappingType) {
            $name = $mappingType::NAME;
            $this->typeMapping[$name] = new $mappingType;
        }
    }

    private function getMappingType(string $name)
    {
        return $this->typeMapping[$name];
    }

    public function processValue(string $typeName, $value, $options = [])
    {


        /** @var AbstractCsvValueType $mappingType */
        $mappingType = $this->getMappingType($typeName);
        $services = [];
        if (DoctrineReferenceCsvValueType::NAME === $typeName) {
            $services['em'] = $this->em;
        }

        return $mappingType->processValue($value, $options, $services);
    }
}