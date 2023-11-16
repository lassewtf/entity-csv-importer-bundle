<?php

namespace Nano\EntityCsvImporterBundle\Service;

use Doctrine\ORM\EntityManagerInterface;
use League\Csv\UnavailableStream;
use Nano\EntityCsvImporterBundle\Import\CsvImport;

class CsvImporterFactory
{
    private EntityManagerInterface $em;
    private ValueProcessor $valueProcessor;

    /**
     * @param EntityManagerInterface $em
     * @param ValueProcessor $valueProcessor
     */
    public function __construct(EntityManagerInterface $em, ValueProcessor $valueProcessor)
    {
        $this->em = $em;
        $this->valueProcessor = $valueProcessor;
    }


    /**
     * @throws UnavailableStream
     */
    public function createCsvImport(string $csvPath, string $entityClass, array $mapping = []): CsvImport
    {
        return new CsvImport($this, $csvPath, $entityClass, $mapping);
    }

    public function getEm(): EntityManagerInterface
    {
        return $this->em;
    }

    public function getValueProcessor(): ValueProcessor
    {
        return $this->valueProcessor;
    }


}