<?php
/**
 * Created by PhpStorm.
 * User: virtua
 * Date: 2019-03-01
 * Time: 15:24
 */

namespace App\Utils\ExportImport;

use Doctrine\ORM\EntityManagerInterface;

abstract class AbstractEntityReflector
{
    /**
     * @var \ReflectionClass
     */
    protected $reflection;

    /**
     * @var EntityManager
     */
    protected $em;

    public function __construct(EntityManagerInterface $em)
    {
        $this->em = $em;
    }

    public function setReflectionClass($entityClass)
    {
        if (!$this->isEntity($entityClass)) {
            throw new \InvalidArgumentException('Argument must have /Entity Annotation');
        }
        $this->reflection = new \ReflectionClass($entityClass);
    }

    protected function getEntityProperties(): array
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