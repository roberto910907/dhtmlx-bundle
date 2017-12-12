DhtmlxBundle
==================

Installation
------------
composer require jsh11/dhtmlx-bundle 1.1

Register the bundle in `app/AppKernel.php`:

``` php
// app/AppKernel.php
public function registerBundles()
{
    return array(
        // ...
        new jsh11\DhtmlxBundle\DhtmlxBundle(),
    );
}
```

Usage
-----
Create a Gannt class
.. code-block::

    <?php
    // src/AppBundle/Gantt/TestGantt.php
    
    namespace AppBundle\Gantt;
    
    use AppBundle\Entity\Test;
    use jsh11\DhtmlxBundle\Gantt\AbstractGantt;
    
    class TestGantt extends AbstractGantt
    {
        public function configure()
        {
            $this->setAjax([
                'route_list' => 'test_index',
                'route_new' => 'test_new',
                'route_edit' => 'test_edit',
                'route_delete' => 'test_delete',
            ]);
    
            $this->setConfig([
                'date_grid' => "%d.%m.%Y",
                'step' => 1,
                'scale_unit' => 'day'
            ]);
    
            $this->setMapping([
                'id' => 'id',
                'text' => 'name',
                'start_date' => 'startAt',
                'duration' => 'duration',
                'progress' => 'advanced'
            ]);
    
            return $this;
        }
    
        public function getEntity()
        {
            return Test::class;
        }
    
        public function getName()
        {
            return "test_gantt";
        }
    }
    
Create a controller class
.. code-block::

    <?php
    
    namespace AppBundle\Controller;
    
    use AppBundle\Entity\Test;
    use AppBundle\Form\TestType;
    use AppBundle\Gantt\TestGantt;
    use jsh11\DhtmlxBundle\Factory\GanttFactory;
    use Symfony\Bundle\FrameworkBundle\Controller\Controller;
    use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
    use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
    use Symfony\Component\HttpFoundation\JsonResponse;
    use Symfony\Component\HttpFoundation\Request;
    
    /**
     * Test controller.
     *
     * @Route("test")
     */
    class TestController extends Controller
    {
        private $ganttFactory;
    
        /**
         * DefaultController constructor.
         * @param GanttFactory $ganttFactory
         */
        public function __construct(GanttFactory $ganttFactory)
        {
            $this->ganttFactory = $ganttFactory;
        }
    
        /**
         * @Route("/", name="test_index", options = {"expose" = true})
         */
        public function indexAction(Request $request)
        {
            $gantt = $this->ganttFactory->create(TestGantt::class);
    
            $gantt->handleRequest($request);
    
            if ($gantt->isSubmitted()) {
                return $gantt->getResponse();
            }
    
            return $this->render('@App/index.html.twig', [
                'gantt' => $gantt
            ]);
        }
    
        /**
         * Creates a new test entity.
         *
         * @Route("/new", name="test_new", options = {"expose" = true})
         * @Method({"GET", "POST"})
         */
        public function newAction(Request $request)
        {
            $test = new Test();
            $form = $this->createForm(TestType::class, $test, [
                'action' => $this->generateUrl('test_new'),
                'method' => 'POST'
            ]);
            $form->handleRequest($request);
    
            if ($form->isSubmitted() && $form->isValid()) {
                $em = $this->getDoctrine()->getManager();
                $em->persist($test);
                $em->flush();
    
                return new JsonResponse([
                    'type' => 'success'
                ]);
            }
    
            return $this->render('test/form.html.twig', array(
                'test' => $test,
                'form' => $form->createView(),
            ));
        }
    
        /**
         * Displays a form to edit an existing test entity.
         *
         * @Route("/{id}/edit", name="test_edit", options = {"expose" = true})
         * @Method({"GET", "POST"})
         */
        public function editAction(Request $request, Test $test)
        {
            $form = $this->createForm('AppBundle\Form\TestType', $test, [
                'action' => $this->generateUrl('test_edit', ['id' => $test->getId()]),
                'method' => 'POST'
            ]);
            $form->handleRequest($request);
    
            if ($form->isSubmitted() && $form->isValid()) {
                $this->getDoctrine()->getManager()->flush();
    
                return new JsonResponse([
                    'type' => 'success'
                ]);
            }
    
            return $this->render('test/form.html.twig', array(
                'test' => $test,
                'form' => $form->createView(),
            ));
        }
    
        /**
         * Deletes a test entity.
         *
         * @Route("/{id}/delete", name="test_delete", options = {"expose" = true})
         * @Method("GET")
         */
        public function deleteAction(Request $request, Test $test)
        {
            $em = $this->getDoctrine()->getManager();
            $em->remove($test);
            $em->flush();
    
            return new JsonResponse([
                'type' => 'success'
            ]);
        }
    }

Create the view for gantt diagram:
.. code-block::

    {% extends 'base.html.twig' %}
    
    {% block stylesheets %}
        <link rel="stylesheet" href="{{ asset('bundles/app/boostrap_3.3.7/css/bootstrap.min.css') }}">
        <link rel="stylesheet" href="{{ asset('bundles/app/dhtmlxgantt_v5.0.1/codebase/dhtmlxgantt.css') }}">
        <link rel="stylesheet" href="{{ asset('bundles/app/jquery-confirm-3.3.0/jquery-confirm.min.css') }}">
        <style>
            body, html {
                height: 100%;
                width: 100%;
            }
        </style>
    {% endblock %}
    
    {% block body %}
        {{ gantt_html(gantt) }}
    {% endblock %}
    
    {% block javascripts %}
        <script src="{{ asset('bundles/app/jquery-3.2.1/jquery-3.2.1.min.js') }}"></script>
        <script src="{{ asset('bundles/app/boostrap_3.3.7/js/bootstrap.min.js') }}"></script>
        <script src="{{ asset('bundles/app/dhtmlxgantt_v5.0.1/codebase/dhtmlxgantt.js') }}"></script>
        <script src="{{ asset('bundles/app/jquery-confirm-3.3.0/jquery-confirm.min.js') }}"></script>
        <script src="{{ asset('bundles/fosjsrouting/js/router.js') }}"></script>
        <script src="{{ asset('js/fos_js_routes.js') }}"></script>
        <script>
            $(function () {
                {{ gantt_js(gantt) }}
            });
        </script>
    {% endblock %}