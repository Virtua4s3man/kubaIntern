<?php
/**
 * Created by PhpStorm.
 * User: virtua
 * Date: 2019-03-01
 * Time: 08:13
 */

namespace App\Utils;

use App\Entity\ProductCategory;
use Doctrine\Common\Persistence\Proxy;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityManagerInterface;

class ExportEntityHelper
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

    public function getTableHeaders(): array
    {
        return array_column($this->reflection->getProperties(), 'name');
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

    private function makeRow($object): array
    {
        $output = [];
        $headers = $this->getTableHeaders();
        foreach ($headers as $property) {
            $getter = $this->makeGetter($property);
            $value = $object->$getter();
//            todo ja bym to widział tak że zamiast tych ifów
//            klasa powinna mieć array obiektów typu converters
//            i każdy z nich powinien implementować export entity converter interface
//            no i tutaj by było takie foreach $this->>converters as converter: converter->convert
//            ale dużo pisania na 2 convertery więc się chyba nie opłaca na razie przynajmniej
            if ($value instanceof \DateTime) {
                $value = $value->format('Y-m-d H:i:s');
            }
            if ($value instanceof ProductCategory) {
                $value = $value->getName();
            }
            $output[] = $value;
        }

        return $output;
    }

    private function makeGetter(string $name)
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