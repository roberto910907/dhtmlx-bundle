<?php

namespace jsh11\DhtmlxBundle\Twig;

use jsh11\DhtmlxBundle\Gantt\AbstractGantt;
use Twig_Environment;

class DhtmlxExtension extends \Twig_Extension
{
    private $twig;

    /**
     * @param Twig_Environment $twig
     */
    public function __construct(Twig_Environment $twig)
    {
        $this->twig = $twig;
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
        return $this->twig->render("@Dhtmlx/gantt.js.twig", [
            'gantt' => $gantt
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
