<?php
/**
 * Created by PhpStorm.
 * User: virtua
 * Date: 2019-03-01
 * Time: 14:11
 */

namespace App\Utils\ExportImport;

class ImportProductHelper extends AbstractEntityReflector
{
    public function getHeaders($fileHandle): array
    {
        $data = fgetcsv($fileHandle, 1000, ",");

        if ($data == false) {
            throw new \InvalidArgumentException('No headers found');
        }

        if (! $this->headersAreValid($data)) {
            throw new \InvalidArgumentException(
                sprintf('File contains invalid properties as headers for %s', $this->reflection->getName())
            );
        }

        return array_flip($data);
    }

    private function headersAreValid($headers)
    {
        return count(array_diff($headers, $this->getReflectionHeaders())) === 0;
    }
}