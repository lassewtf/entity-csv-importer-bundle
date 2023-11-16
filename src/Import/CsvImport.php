<?php

namespace Nano\EntityCsvImporterBundle\Import;

use Exception;
use League\Csv\Reader;
use League\Csv\UnavailableStream;
use Nano\EntityCsvImporterBundle\Service\CsvImporterFactory;
use Symfony\Component\PropertyAccess\PropertyAccess;
use Symfony\Component\PropertyAccess\PropertyAccessor;

class CsvImport
{
    const DEFAULT_MAPPING_TYPE = 'raw';

    protected CsvImporterFactory $factory;
    private string $csvPath;
    private string $entityClass;
    private array $mapping;
    private Reader $reader;
    private PropertyAccessor $propertyAccessor;

    private array $header = [];
    private int $batchSize = 20;

    /**
     * @param CsvImporterFactory $factory
     * @param string $csvPath
     * @param string $entityClass
     * @throws UnavailableStream
     * @throws \League\Csv\Exception
     */
    public function __construct(CsvImporterFactory $factory, string $csvPath, string $entityClass, array $mapping = [])
    {
        $this->factory = $factory;
        $this->csvPath = $csvPath;
        $this->entityClass = $entityClass;
        $this->mapping = $mapping;
        $this->propertyAccessor = PropertyAccess::createPropertyAccessor();
        $this->initReader();
    }

    /**
     * @throws UnavailableStream
     * @throws \League\Csv\Exception
     */
    private function initReader()
    {
        $this->reader = Reader::createFromPath($this->csvPath, 'r');;
        $this->reader->setHeaderOffset(0); // Erste Zeile als Header
        $this->header = $this->reader->getHeader();
    }

    private function validateWriteAccess()
    {
        $entity = new $this->entityClass;
        foreach ($this->header as $property) {
            if (!$this->propertyAccessor->isWritable($entity, $property)) {
                return false;
            }
        }
        return true;
    }

    public function setBatchSize(int $size)
    {
        $this->batchSize = $size;
    }

    private function createEntityFromRow(array $row)
    {
        $entityClass = $this->entityClass;
        $entity = new $entityClass();
        foreach ($row as $property => $value) {
            $value = $this->processValue($value, $this->mapping[$property] ?? []);
            $this->propertyAccessor->setValue($entity, $property, $value);
        }

        return $entity;
    }

    private function processValue($value, $propertyMapping)
    {
        $type = $propertyMapping['type'] ?? self::DEFAULT_MAPPING_TYPE;

        $options = array_diff_key($propertyMapping, array_flip(['type']));

        return $this->factory->getValueProcessor()->processValue($type, $value, $options);
    }

    /**
     * @throws \League\Csv\Exception
     */
    public function import()
    {

        if (!$this->validateWriteAccess()) {
            throw new Exception('entity is not writeable');
        }

        $i = 0;
        foreach ($this->reader as $rowIndex => $row) {
            try {
                $entity = $this->createEntityFromRow($row);
                $this->factory->getEm()->persist($entity);
            } catch (Exception $e) {
                $errorMessage = sprintf("Fehler in Zeile %d beim Verarbeiten der Entity '%s': %s", $rowIndex, $this->entityClass, $e->getMessage());
                // Bessere Fehlermeldung mit Kontextinformationen
                throw new Exception($errorMessage);
            }
            if (($i % $this->batchSize) === 0) {
                $this->factory->getEm()->flush();
                $this->factory->getEm()->clear(); // Optional
            }
            $i++;
        }

        $this->factory->getEm()->flush();
    }

}