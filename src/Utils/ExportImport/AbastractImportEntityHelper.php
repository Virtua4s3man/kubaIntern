<?php
/**
 * Created by PhpStorm.
 * User: virtua
 * Date: 2019-03-01
 * Time: 14:11
 */

namespace App\Utils\ExportImport;

abstract class AbastractImportEntityHelper extends AbstractEntityReflector
{
    /**
     * @var \SplFileObject
     */
    private $splFile;

    /**
     * @var array
     */
    private $headers;

    /**
     * @var int
     */
    private $linesCount;

    public function configureImport($filePath, $entityClass)
    {
        parent::setReflectionClass($entityClass);
        $this->splFile = new \SplFileObject($filePath);
        $this->headers = $this->readFileHeaders();
        $this->linesCount = $this->getLinesCount();
    }

    public function generateNamedData()
    {
        $this->splFile->rewind();
        $this->splFile->fgetcsv();
        while (!$this->splFile->eof()) {
            $data = $this->splFile->fgetcsv();

            if (count($data) === count($this->headers)) {
                yield $this->nameData($data);
            }

            if ($this->invalidData($data)) {
                throw new \InvalidArgumentException(
                    sprintf(
                        'Invalid csv file, missing field, check line %s, position %s',
                        $this->splFile->key(),
                        $this->splFile->ftell()
                    )
                );
            }
        }
    }

    public function readFileHeaders(): array
    {
        $this->splFile->rewind();
        $headers = $this->splFile->fgetcsv();

        if (! $this->headersAreValid($headers)) {
            throw new \InvalidArgumentException(
                sprintf('File contains headers which are not valid properties of %s', $this->reflection->getName())
            );
        }

        return $headers;
    }

    protected function makeSetter(string $name): string
    {
        $setter = 'set' . ucfirst($name);
        if (!$this->reflection->hasMethod($setter)) {
            throw new \InvalidArgumentException(
                sprintf('%s not implemented in %s', $setter, $this->reflection->getName())
            );
        }

        return $setter;
    }

    private function invalidData(array $data): bool
    {
        return count($data) !== count($this->headers) and $this->splFile->key() < $this->linesCount;
    }

    private function nameData(array $data): array
    {
        return array_combine(
            $this->headers,
            $data
        );
    }

    private function getLinesCount()
    {
        $this->splFile->rewind();
        $this->splFile->seek($this->splFile->getSize());
        return $this->splFile->key();
    }

    private function headersAreValid($headers)
    {
        return count(array_diff($headers, $this->getEntityProperties())) === 0;
    }
}