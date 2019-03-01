<?php
/**
 * Created by PhpStorm.
 * User: virtua
 * Date: 2019-03-01
 * Time: 08:13
 */

namespace App\Utils;

use Doctrine\Common\Persistence\Proxy;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityManagerInterface;

abstract class ExportEntityHelper
{
    /**
     * @var array
     */
    protected $headers = [];

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

    public function getTableHeaders(): array
    {
        return 0 === count($this->headers) ?
            array_column($this->reflection->getProperties(), 'name')
            : $this->headers;
    }

    public function getTableRow($object): array
    {
        $className = $this->reflection->getName();
        if (!$object instanceof $className) {
            throw new \InvalidArgumentException(
                sprintf('Argument has to be instance of %s, %s passed', $className, get_class($object))
            );
        }

        return $this->makeRow($object);
    }

    protected function convert(&$value)
    {
        if (is_null($value)) {
            $value = '';
        }
        if ($value instanceof \DateTime) {
            $value = $value->format('Y-m-d H:i:s');
        }
    }

    private function makeRow($object): array
    {
        $output = [];
        $headers = $this->getTableHeaders();
        foreach ($headers as $property) {
            $getter = $this->makeGetter($property);
            $value = $object->$getter();
            $this->convert($value);
            $output[] = $value;
        }

        return $output;
    }

    private function makeGetter(string $name): string
    {
        $getter = 'get' . ucfirst($name);
        if (!$this->reflection->hasMethod($getter)) {
            throw new \InvalidArgumentException(
                sprintf('%s not implemented in %s', $getter, $this->reflection->getName())
            );
        }

        return $getter;
    }

    private function isEntity($class): bool
    {
        if (is_object($class)) {
            $class = ($class instanceof Proxy) ? get_parent_class($class) : get_class($class);
        }
        return ! $this->em->getMetadataFactory()->isTransient($class);
    }
}