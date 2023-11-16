<?php

namespace Nano\EntityCsvImporterBundle\Service;

use Doctrine\ORM\EntityManagerInterface;
use Exception;
use League\Csv\UnavailableStream;
use Symfony\Component\PropertyAccess\PropertyAccess;
use Symfony\Component\PropertyAccess\PropertyAccessor;

class CsvEntityImporter
{
    const DEFAULT_MAPPING_TYPE = 'raw';

    private int $batchSize = 20;

    private ConfigSorter $configSorter;


    private CsvImporterFactory $importerFactory;

    public function __construct(ConfigSorter $configSorter, CsvImporterFactory $importerFactory)
    {
        $this->configSorter = $configSorter;
        $this->importerFactory = $importerFactory;
    }

    public function setBatchSize(int $size): static
    {
        $this->batchSize = $size;
        return $this;
    }

    /**
     * @throws Exception
     */
    public function loadData(array $config): static
    {
        dump('start csv Importer');
        // sort config
        $sortedConfig = $this->configSorter->sort($config);

        foreach ($sortedConfig as $configItem) {
            // if no dependencies load the class
            $this->loadConfigItem($configItem);
        }
        return $this;
    }

    /**
     * @throws Exception
     */
    private function loadConfigItem(array $configItem): void
    {
        $this->loadCsvData($configItem['entity'], $configItem['file'], $configItem['mapping'] ?? []);
    }

    /**
     * @throws UnavailableStream
     * @throws \League\Csv\Exception
     * @throws Exception
     */
    public function loadCsvData(string $entityClass, string $csvPath, array $mapping = []): static
    {
        dump('loadCsvData: ' . $entityClass);
        $import = $this->importerFactory->createCsvImport($csvPath, $entityClass, $mapping);
        $import->setBatchSize($this->batchSize);
        $import->import();
        return $this;
    }

}