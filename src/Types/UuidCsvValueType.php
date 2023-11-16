<?php

namespace Nano\EntityCsvImporterBundle\Types;

use Exception;
use Symfony\Component\Uid\Uuid;

class UuidCsvValueType extends AbstractCsvValueType
{
    const NAME = 'uuid';

    protected function defaultOptions(): array
    {
        return [
            'format' => 'base58',
        ];
    }

    /**
     * @throws Exception
     */
    public  function processValue($value, array $options = [], array $services = [])
    {
        $options = $this->resolveOptions($options);
        $format = $options['format'];
        $uuid = null;
        if ($format === 'base58') {
            $uuid = Uuid::fromBase58($value);
        } elseif ($format === 'base32' ) {
            $uuid = Uuid::fromBase32($value);
        }
        if (!$uuid instanceof Uuid) {
            throw new Exception('cant create uuid');
        }

        return $uuid;
    }
}