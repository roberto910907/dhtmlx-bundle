<?php

namespace jsh11\DhtmlxBundle\Gantt;

use Doctrine\ORM\EntityManager;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\PropertyAccess\PropertyAccess;
use Symfony\Component\Routing\Router;

abstract class AbstractGantt implements GanttInterface
{
    /** @var Router $router */
    protected $router;
    /** @var EntityManager $entityManager */
    protected $entityManager;
    /** @var  Request $request */
    protected $request;

    protected $ajax = array();
    protected $config = array();
    protected $mapping = array();
    protected $form;
    protected $editing = false;

    /**
     * @return array
     */
    public function getAjax()
    {
        return $this->ajax;
    }

    /**
     * @param array $ajax
     */
    public function setAjax(array $ajax)
    {
        $this->ajax = $ajax;
    }

    /**
     * @return array
     */
    public function getConfig()
    {
        return $this->config;
    }

    /**
     * @param array $config
     */
    public function setConfig(array $config)
    {
        $this->config = $config;
    }

    /**
     * @return array
     */
    public function getMapping()
    {
        return $this->mapping;
    }

    /**
     * @param array $mapping
     */
    public function setMapping(array $mapping)
    {
        $this->mapping = $mapping;
    }

    /**
     * @return array
     */
    public function getForm()
    {
        return $this->form;
    }

    /**
     * @param  $form
     * @return AbstractGantt
     */
    public function setForm($form)
    {
        $this->form = $form;
        return $this;
    }

    /**
     * @return bool
     */
    public function isEditing()
    {
        return $this->editing;
    }

    /**
     * @param bool $editing
     * @return AbstractGantt
     */
    public function setEditing($editing)
    {
        $this->editing = $editing;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getRouter()
    {
        return $this->router;
    }

    /**
     * @param Router $router
     * @return AbstractGantt
     */
    public function setRouter(Router $router)
    {
        $this->router = $router;
        return $this;
    }

    /**
     * @return EntityManager
     */
    public function getEntityManager()
    {
        return $this->entityManager;
    }

    /**
     * @param EntityManager $entityManager
     * @return AbstractGantt
     */
    public function setEntityManager($entityManager)
    {
        $this->entityManager = $entityManager;
        return $this;
    }

    public function handleRequest(Request $request)
    {
        $this->request = $request;
        $this->editing = $request->query->get('editing');

        if ($this->editing) {
            $this->edit();
        }
    }

    public function edit()
    {
        $data = $this->request->request->all();
        $id = $data['ids'];

        $repository = $this->entityManager->getRepository($this->getEntity());

        $accessor = PropertyAccess::createPropertyAccessor();
        $entity = $repository->find($id);

        foreach ($this->mapping as $key => $field) {
            if ($key !== 'id') {
                if ($key == 'start_date') {
                    $value = new \DateTime($data[$id . '_' . $key]);
                } else {
                    $value = $data[$id . '_' . $key];
                }
                $accessor->setValue($entity, $this->mapping[$key], $value);
            }
        }

        $this->entityManager->persist($entity);
        $this->entityManager->flush();
    }

    public function getResponse()
    {
        $repository = $this->entityManager->getRepository($this->getEntity());
        $entities = $repository->findAll();
        $accessor = PropertyAccess::createPropertyAccessor();
        $data = [];

        foreach ($entities as $entity) {
            $data[] = [
                'id' => $accessor->getValue($entity, $this->mapping['id']),
                'text' => $accessor->getValue($entity, $this->mapping['text']),
                'start_date' => $accessor->getValue($entity, $this->mapping['start_date'])->format('d.m.Y'),
                'duration' => $accessor->getValue($entity, $this->mapping['duration']),
                'progress' => $accessor->getValue($entity, $this->mapping['progress']),
                'server' => true
            ];
        }

        return new JsonResponse([
            'data' => $data
        ]);
    }

    public function isSubmitted()
    {
        return $this->request->isXmlHttpRequest();
    }
}