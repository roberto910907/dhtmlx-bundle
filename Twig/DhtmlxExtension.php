<?php

namespace jsh11\DhtmlxBundle\Twig;

use jsh11\DhtmlxBundle\Gantt\AbstractGantt;
use Symfony\Component\Form\FormFactory;
use Twig_Environment;

class DhtmlxExtension extends \Twig_Extension
{
    private $twig;
    private $formFactory;

    /**
     * @param Twig_Environment $twig
     * @param FormFactory $formFactory
     */
    public function __construct(Twig_Environment $twig, FormFactory $formFactory)
    {
        $this->twig = $twig;
        $this->formFactory = $formFactory;
    }

    public function getFunctions()
    {
        return [
            new \Twig_SimpleFunction('gantt_js', [$this, 'renderGanttJs'], ['is_safe' => ['html']]),
            new \Twig_SimpleFunction('gantt_html', [$this, 'renderGanttHtml'], ['is_safe' => ['html']]),
        ];
    }

    public function renderGanttJs(AbstractGantt $gantt)
    {
        $form = $this->formFactory->create($gantt->getForm());

        return $this->twig->render("@Dhtmlx/gantt.js.twig", [
            'gantt' => $gantt,
            'form' => $form->createView()
        ]);
    }

    public function renderGanttHtml(AbstractGantt $gantt)
    {
        return $this->twig->render("@Dhtmlx/gantt.html.twig", [
            'gantt' => $gantt
        ]);
    }

    public function getName()
    {
        return 'jsh11_dhtmlx_extension';
    }
}
