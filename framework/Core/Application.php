<?php
/**
 * Created by PhpStorm.
 * User: quantick
 * Date: 18.01.18
 */

namespace Artifly\Core;


use Artifly\Core\Component\Container\Container;
use Artifly\Core\Component\Router\DispatchedRoute;
use Artifly\Core\Component\Router\Router;
use Artifly\Core\Component\Template\TemplateEngine;
use Artifly\Core\Http\ActionHandler;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Twig_Environment;
use Twig_Loader_Filesystem;

/**
 * Class Application
 *
 * @package Artifly\Core
 */
class Application
{
//region SECTION: Fields
    /**
     * @var Request
     */
    private $request;
    /**
     * @var Container
     */
    private $container;
    /**
     * @var string
     */
    private $frameworkPath = '';
    /**
     * @var string
     */
    private $corePath = '';
    /**
     * @var string
     */
    private $templatesPath = '';
    /**
     * @var TemplateEngine
     */
    private $templateEngine = null;
//endregion Fields

//region SECTION: Constructor
    /**
     * Application constructor.
     */
    public function __construct()
    {
        $this->corePath      = dirname(__FILE__);
        $this->frameworkPath = $this->corePath.'/../';
        $this->templatesPath = $this->frameworkPath.'../templates';

        $this->request   = Request::createFromGlobals();
        $this->container = new Container();
        $this->container->addInstance($this->request);
        $loader         = new Twig_Loader_Filesystem($this->templatesPath);
        $twig           = new Twig_Environment(
            $loader, [
//            'cache' => '/path/to/compilation_cache',
            ]
        );
        $this->templateEngine = new TemplateEngine($twig);
        $this->container->addInstance($this->templateEngine);
    }
//endregion Constructor

//region SECTION: Public
    /**
     * @param Router $router
     *
     * @throws Exception\ControllerResponseError
     */
    public function run(Router $router)
    {
        $this->container->addInstance($router);
        $dispatchedRoute = $router->dispatch($this->request);
        switch ($dispatchedRoute->getDispatchType()) {
            case DispatchedRoute::ROUTE_FOUNDED:
                $handlerType = $dispatchedRoute->getHandler() instanceof \Closure ? ActionHandler::CLOSURE_TYPE : ActionHandler::CONTROLLER_TYPE;
                $handler     = new ActionHandler($dispatchedRoute->getHandler(), $handlerType);
                $handler->setContainer($this->container);
                $content = $handler->execute($dispatchedRoute->getArgs());
                $this->printContent($content);
                break;
            default:
                $this->print404();
        }
    }
//endregion Public

//region SECTION: Private
    /**
     * @throws \Twig_Error_Loader
     * @throws \Twig_Error_Runtime
     * @throws \Twig_Error_Syntax
     */
    private function print404(): void
    {
        $response = new Response($this->templateEngine->render('404.html'), 404);
        $response->send();
    }

    /**
     * @param     $url
     * @param int $code
     */
    private function redirect($url, $code = 302): void
    {
        $response = new RedirectResponse($url, $code);
        $response->send();
    }

    /**
     * @param $content
     */
    private function printContent($content)
    {
        $response = $content instanceof Response ? $content : new Response($content,200);
        $response->send();
    }
//endregion Private

//region SECTION: Getters/Setters
    /**
     * @return Container
     */
    public function getContainer()
    {
        return $this->container;
    }

    /**
     * @return mixed
     */
    public function getRequest()
    {
        return $this->request;
    }
//endregion Getters/Setters
}