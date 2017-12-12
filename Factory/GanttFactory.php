<?php

namespace jsh11\DhtmlxBundle\Factory;

use Doctrine\ORM\EntityManager;
use jsh11\DhtmlxBundle\Gantt\AbstractGantt;

class GanttFactory
{
    private $entityManager;

    /**
     * GanttFactory constructor.
     * @param EntityManager $entityManager
     */
    public function __construct(EntityManager $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    public function create($className)
    {
        /** @var AbstractGantt $gantt */
        $gantt = new $className;
        $gantt
            ->setEntityManager($this->entityManager)
            ->configure();

        return $gantt;
    }
}