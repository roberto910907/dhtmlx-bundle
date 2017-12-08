<?php

namespace jsh11\DhtmlxBundle\Factory;

use Doctrine\ORM\EntityManager;
use jsh11\DhtmlxBundle\Gantt\AbstractGantt;
use Symfony\Component\Form\FormFactory;
use Symfony\Component\Routing\Router;

class GanttFactory
{
    private $router;
    private $entityManager;

    /**
     * GanttFactory constructor.
     * @param Router $router
     * @param EntityManager $entityManager
     */
    public function __construct(Router $router, EntityManager $entityManager)
    {
        $this->router = $router;
        $this->entityManager = $entityManager;
    }

    public function create($className)
    {
        /** @var AbstractGantt $gantt */
        $gantt = new $className;
        $gantt
            ->setRouter($this->router)
            ->setEntityManager($this->entityManager)
            ->configure();

        return $gantt;
    }
}