<?php

namespace Nano\EntityCsvImporterBundle;

use Nano\EntityCsvImporterBundle\DependencyInjection\NanoEntityCsvImporterExtension;
use Symfony\Component\HttpKernel\Bundle\AbstractBundle;

class NanoEntityCsvImporterBundle extends AbstractBundle
{
    public function getPath(): string
    {
        return __DIR__;
    }

    public function getContainerExtension(): NanoEntityCsvImporterExtension
    {
        return new NanoEntityCsvImporterExtension();
    }
}