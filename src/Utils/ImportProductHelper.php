<?php
/**
 * Created by PhpStorm.
 * User: virtua
 * Date: 2019-03-01
 * Time: 14:11
 */

namespace App\Utils;


class ImportProductHelper
{
    /**
     * @var \ReflectionClass
     */
    private $reflection;

    /**
     * @var EntityManager
     */
    private $em;

    public function __construct(EntityManagerInterface $em)
    {
        $this->em = $em;
    }

    public function setReflectionClass($object)
    {
        if (!$this->isEntity($object)) {
            throw new \InvalidArgumentException('Argument must have /Entity Annotation');
        }
        $this->reflection = new \ReflectionClass($object);
    }

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

    private function getReflectionHeaders()
    {
        return array_column($this->reflection->getProperties(), 'name');
    }

    private function isEntity($class): bool
    {
        if (is_object($class)) {
            $class = ($class instanceof Proxy) ? get_parent_class($class) : get_class($class);
        }
        return ! $this->em->getMetadataFactory()->isTransient($class);
    }
}